<?php

namespace App\Http\Controllers;

use App\Exports\VisualisasiTemplateExport;
use App\Models\Visualisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\FacadesStorage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class VisualisasiController extends Controller
{
    private function checkViewPermission()
    {
        $user = Auth::user();
        
        if ($user->hasRole('reviewer')) {
            abort(403, 'Reviewer tidak memiliki akses ke visualisasi management.');
        }
    }

    private function checkCreatePermission()
    {
        $user = Auth::user();
        
        if ($user->hasRole('reviewer')) {
            abort(403, 'Reviewer tidak memiliki akses ke visualisasi management.');
        }
        
        if ($user->hasRole('penanggung-jawab')) {
            abort(403, 'Penanggung jawab hanya dapat melihat dan mendownload visualisasi.');
        }
        
        if (!$user->hasRole(['super-admin', 'opd'])) {
            abort(403, 'Hanya Super Admin dan OPD yang dapat membuat visualisasi.');
        }
    }

    private function checkEditPermission()
    {
        $user = Auth::user();
        
        if ($user->hasRole('reviewer')) {
            abort(403, 'Reviewer tidak memiliki akses ke visualisasi management.');
        }
        
        if ($user->hasRole('penanggung-jawab')) {
            abort(403, 'Penanggung jawab hanya dapat melihat dan mendownload visualisasi.');
        }
        
        if (!$user->hasRole(['super-admin', 'opd'])) {
            abort(403, 'Hanya Super Admin dan OPD yang dapat mengedit visualisasi.');
        }
    }

    private function checkVisualisasiAccess($visualisasi)
    {
        $user = Auth::user();
        
        // Super admin bisa akses semua
        if ($user->hasRole('super-admin')) {
            return;
        }
        
        // OPD hanya bisa akses data miliknya
        if ($user->hasRole('opd')) {
            if ($visualisasi->user_id !== $user->id) {
                abort(403, 'Anda hanya dapat mengakses visualisasi milik Anda sendiri.');
            }
            return;
        }
        
        // Penanggung jawab hanya bisa akses data yang aktif dan publik
        if ($user->hasRole('penanggung-jawab')) {
            if (!$visualisasi->is_active || !$visualisasi->is_public) {
                abort(403, 'Anda hanya dapat mengakses visualisasi yang aktif dan publik.');
            }
            return;
        }
    }

    private function checkDownloadPermission()
    {
        $user = Auth::user();
        
        if ($user->hasRole('reviewer')) {
            abort(403, 'Reviewer tidak memiliki akses untuk download visualisasi.');
        }
    }

    /**
     * Display a listing of visualisasi.
     */
    public function index(Request $request): View
    {
        $this->checkViewPermission();
        
        $user = Auth::user();
        $query = Visualisasi::with('user');

        // Filter berdasarkan role
        if ($user->hasRole('super-admin')) {
            // Super admin bisa melihat semua
            $query->latest();
        } elseif ($user->hasRole('opd')) {
            // OPD hanya bisa melihat miliknya sendiri
            $query->where('user_id', $user->id)->latest();
        } elseif ($user->hasRole('penanggung-jawab')) {
            // Penanggung jawab bisa melihat semua yang aktif dan publik
            $query->where('is_active', true)->where('is_public', true)->latest();
        }

        // Filter berdasarkan search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan topic
        if ($request->filled('topic')) {
            $query->byTopic($request->topic);
        }

        // Filter berdasarkan tipe
        if ($request->filled('tipe')) {
            $query->byTipe($request->tipe);
        }

        // Filter berdasarkan data source
        if ($request->filled('data_source')) {
            $query->where('data_source', $request->data_source);
        }

        // Filter berdasarkan status (hanya untuk super-admin dan opd yang melihat data sendiri)
        if ($request->filled('status') && ($user->hasRole('super-admin') || ($user->hasRole('opd')))) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter berdasarkan visibility (hanya untuk super-admin dan opd yang melihat data sendiri)
        if ($request->filled('visibility') && ($user->hasRole('super-admin') || ($user->hasRole('opd')))) {
            if ($request->visibility === 'public') {
                $query->public();
            } elseif ($request->visibility === 'private') {
                $query->where('is_public', false);
            }
        }

        // Pagination
        $visualisasi = $query->paginate(10)->withQueryString();

        // Data untuk dropdown filter
        $topics = Visualisasi::getTopics();
        $tipes = Visualisasi::getTipes();

        return view('visualisasi.index', compact(
            'visualisasi',
            'topics',
            'tipes'
        ));
    }

    /**
     * Show the form for creating a new visualisasi.
     */
    public function create(): View
    {
        $this->checkCreatePermission();
        
        $topics = Visualisasi::getTopics();
        $tipes = Visualisasi::getTipes();
        
        return view('visualisasi.create', compact('topics', 'tipes'));
    }

    /**
     * Store a newly created visualisasi in storage.
     */
    public function store(Request $request)
    {
        $this->checkCreatePermission();
        
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'topic' => 'required|in:' . implode(',', Visualisasi::getTopics()),
            'tipe' => 'required|in:' . implode(',', array_keys(Visualisasi::getTipes())),
            'data_source' => 'required|in:file,manual',
            'source_file' => 'nullable|file|mimes:xlsx,xls,csv|max:5120', // 5MB max
            'manual_data' => 'nullable|string', // JSON string for manual data
            'is_active' => 'boolean',
            'is_public' => 'boolean'
        ]);

        // Generate slug from nama
        $baseSlug = Str::slug($validated['nama']);
        $slug = $baseSlug;
        $counter = 1;
        
        // Check for duplicate slugs and append number if needed
        while (Visualisasi::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        $validated['slug'] = $slug;

        // Handle file upload
        if ($request->hasFile('source_file') && $request->data_source === 'file') {
            $file = $request->file('source_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('visualisasi_data', $filename, 'public');
            $validated['source_file'] = $path;
        }

        // Handle manual data
        if ($request->data_source === 'manual' && $request->filled('manual_data')) {
            $manualData = json_decode($request->manual_data, true);
            
            // Store manual data in data_config
            $validated['data_config'] = [
                'type' => 'manual',
                'x_label' => $manualData['x_label'] ?? 'Kategori',
                'y_label' => $manualData['y_label'] ?? 'Nilai',
                'data' => [
                    'labels' => $manualData['labels'] ?? [],
                    'values' => $manualData['values'] ?? []
                ]
            ];
        }

        // Remove manual_data from validated array as it's not a database column
        unset($validated['manual_data']);
        $validated['user_id'] = Auth::user()->id;
        $visualisasi = Visualisasi::create($validated);

        // Check if user wants to continue adding
        if ($request->has('continue')) {
            return redirect()->route('visualisasi.create')
                ->with('success', 'Visualisasi berhasil dibuat. Silakan tambah visualisasi lain.');
        }

        return redirect()->route('visualisasi.index')
            ->with('success', 'Visualisasi berhasil dibuat.');
    }

    /**
     * Display the specified visualisasi.
     */
    public function show(Request $request, Visualisasi $visualisasi): View
    {
        $this->checkViewPermission();
        $this->checkVisualisasiAccess($visualisasi);

        $visualisasi->load('user');
        
        // Increment views only if not owner
        if ($visualisasi->user_id !== Auth::id()) {
            $visualisasi->incrementViews();
        }

        // Check if this is an embed request
        if ($request->has('embed')) {
            return view('visualisasi.embed', compact('visualisasi'));
        }

        // Get related visualizations (same topic or type)
        $relatedQuery = Visualisasi::where('id', '!=', $visualisasi->id)
            ->where('is_active', true)
            ->where('is_public', true)
            ->where(function($query) use ($visualisasi) {
                $query->where('topic', $visualisasi->topic)
                      ->orWhere('tipe', $visualisasi->tipe);
            })
            ->latest()
            ->limit(5);

        $relatedVisualizations = $relatedQuery->get();

        return view('visualisasi.show', compact('visualisasi', 'relatedVisualizations'));
    }

    /**
     * Show the form for editing the specified visualisasi.
     */
    public function edit(Visualisasi $visualisasi): View
    {
        $this->checkEditPermission();
        $this->checkVisualisasiAccess($visualisasi);
        
        $topics = Visualisasi::getTopics();
        $tipes = Visualisasi::getTipes();
        
        return view('visualisasi.edit', compact('visualisasi', 'topics', 'tipes'));
    }

    /**
     * Update the specified visualisasi in storage.
     */
    public function update(Request $request, Visualisasi $visualisasi)
    {
        $this->checkEditPermission();
        $this->checkVisualisasiAccess($visualisasi);
        
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'topic' => 'required|in:' . implode(',', Visualisasi::getTopics()),
            'tipe' => 'required|in:' . implode(',', array_keys(Visualisasi::getTipes())),
            'data_source' => 'required|in:file,manual',
            'source_file' => 'nullable|file|mimes:xlsx,xls,csv|max:5120', // 5MB max
            'manual_data' => 'nullable|string', // JSON string for manual data
            'is_active' => 'boolean',
            'is_public' => 'boolean'
        ]);

        // Check if nama has changed and generate new slug if needed
        if ($validated['nama'] !== $visualisasi->nama) {
            $baseSlug = Str::slug($validated['nama']);
            $slug = $baseSlug;
            $counter = 1;
            
            // Check for duplicate slugs (excluding current record)
            while (Visualisasi::where('slug', $slug)->where('id', '!=', $visualisasi->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            
            $validated['slug'] = $slug;
        }

        // Handle file upload (only if new file is uploaded)
        if ($request->hasFile('source_file') && $request->data_source === 'file') {
            // Delete old file if exists
            if ($visualisasi->source_file && $visualisasi->fileExists()) {
                Storage::disk('public')->delete($visualisasi->source_file);
            }

            $file = $request->file('source_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('visualisasi_data', $filename, 'public');
            $validated['source_file'] = $path;
        } elseif ($request->data_source === 'manual') {
            // If switching to manual, clear the file
            if ($visualisasi->source_file && $visualisasi->fileExists()) {
                Storage::disk('public')->delete($visualisasi->source_file);
            }
            $validated['source_file'] = null;
        }

        // Handle manual data
        if ($request->data_source === 'manual' && $request->filled('manual_data')) {
            $manualData = json_decode($request->manual_data, true);
            
            // Store manual data in data_config
            $validated['data_config'] = [
                'type' => 'manual',
                'x_label' => $manualData['x_label'] ?? 'Kategori',
                'y_label' => $manualData['y_label'] ?? 'Nilai',
                'data' => [
                    'labels' => $manualData['labels'] ?? [],
                    'values' => $manualData['values'] ?? []
                ]
            ];
        } elseif ($request->data_source === 'file') {
            // Clear manual data if switching to file
            $currentDataConfig = $visualisasi->data_config ?? [];
            if (isset($currentDataConfig['type']) && $currentDataConfig['type'] === 'manual') {
                $validated['data_config'] = null;
            }
        }

        // Remove manual_data from validated array as it's not a database column
        unset($validated['manual_data']);

        $visualisasi->update($validated);

        return redirect()->route('visualisasi.index')
            ->with('success', 'Visualisasi berhasil diperbarui.');
    }

    /**
     * Remove the specified visualisasi from storage.
     */
    public function destroy(Visualisasi $visualisasi)
    {
        $this->checkEditPermission();
        $this->checkVisualisasiAccess($visualisasi);
        
        // Delete associated file if exists
        if ($visualisasi->source_file && $visualisasi->fileExists()) {
            Storage::disk('public')->delete($visualisasi->source_file);
        }

        $visualisasi->delete();

        return redirect()->route('visualisasi.index')
            ->with('success', 'Visualisasi berhasil dihapus.');
    }

    /**
     * Export chart data as CSV
     */
    public function exportCsv(Visualisasi $visualisasi)
    {
        $this->checkDownloadPermission();
        
        $user = Auth::user();
        
        // Check authorization untuk export
        if ($user->hasRole('super-admin')) {
            // Super admin bisa export semua
        } elseif ($user->hasRole('opd')) {
            // OPD hanya bisa export miliknya sendiri atau yang publik
            if ($visualisasi->user_id !== $user->id && (!$visualisasi->is_active || !$visualisasi->is_public)) {
                abort(403, 'Anda tidak memiliki akses untuk mengekspor visualisasi ini.');
            }
        } elseif ($user->hasRole('penanggung-jawab')) {
            // Penanggung jawab bisa export yang aktif dan publik
            if (!$visualisasi->is_active || !$visualisasi->is_public) {
                abort(403, 'Anda tidak memiliki akses untuk mengekspor visualisasi ini.');
            }
        }
        
        $data = $visualisasi->getProcessedData();
        
        if (empty($data['labels']) || empty($data['values'])) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diekspor.');
        }

        $filename = Str::slug($visualisasi->nama) . '_data.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($data, $visualisasi) {
            $file = fopen('php://output', 'w');
            
            // Write headers
            $xLabel = $visualisasi->data_config['x_label'] ?? 'Label';
            $yLabel = $visualisasi->data_config['y_label'] ?? 'Value';
            fputcsv($file, [$xLabel, $yLabel]);
            
            // Write data
            for ($i = 0; $i < count($data['labels']); $i++) {
                fputcsv($file, [$data['labels'][$i], $data['values'][$i]]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export chart data as JSON
     */
    public function exportJson(Visualisasi $visualisasi)
    {
        $this->checkDownloadPermission();
        
        $user = Auth::user();
        
        // Check authorization untuk export (sama seperti exportCsv)
        if ($user->hasRole('super-admin')) {
            // Super admin bisa export semua
        } elseif ($user->hasRole('opd')) {
            // OPD hanya bisa export miliknya sendiri atau yang publik
            if ($visualisasi->user_id !== $user->id && (!$visualisasi->is_active || !$visualisasi->is_public)) {
                abort(403, 'Anda tidak memiliki akses untuk mengekspor visualisasi ini.');
            }
        } elseif ($user->hasRole('penanggung-jawab')) {
            // Penanggung jawab bisa export yang aktif dan publik
            if (!$visualisasi->is_active || !$visualisasi->is_public) {
                abort(403, 'Anda tidak memiliki akses untuk mengekspor visualisasi ini.');
            }
        }
        
        $data = $visualisasi->getProcessedData();
        
        if (empty($data['labels']) || empty($data['values'])) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diekspor.');
        }

        $filename = Str::slug($visualisasi->nama) . '_data.json';
        $exportData = [
            'visualisasi' => [
                'nama' => $visualisasi->nama,
                'tipe' => $visualisasi->tipe,
                'topic' => $visualisasi->topic,
                'created_at' => $visualisasi->created_at->toISOString(),
            ],
            'config' => $visualisasi->data_config,
            'data' => $data
        ];

        return response()->json($exportData)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
    
    public function downloadTemplate(Request $request)
    {
        $this->checkCreatePermission();
        
        $chartType = $request->get('type', 'default');
        
        $templates = [
            'bar_chart' => [
                'name' => 'Template_Bar_Chart',
                'description' => 'Template untuk grafik batang',
                'headers' => ['Kategori', 'Nilai'],
                'sample_data' => [
                    ['Januari', 100],
                    ['Februari', 150],
                    ['Maret', 120],
                    ['April', 180],
                    ['Mei', 200]
                ]
            ],
            'line_chart' => [
                'name' => 'Template_Line_Chart',
                'description' => 'Template untuk grafik garis',
                'headers' => ['Periode', 'Nilai'],
                'sample_data' => [
                    ['Q1 2024', 1250],
                    ['Q2 2024', 1850],
                    ['Q3 2024', 1650],
                    ['Q4 2024', 2100]
                ]
            ],
            'pie_chart' => [
                'name' => 'Template_Pie_Chart',
                'description' => 'Template untuk grafik lingkaran',
                'headers' => ['Kategori', 'Persentase'],
                'sample_data' => [
                    ['Desktop', 45.2],
                    ['Mobile', 32.8],
                    ['Tablet', 15.6],
                    ['Smart TV', 4.2],
                    ['Lainnya', 2.2]
                ]
            ],
            'area_chart' => [
                'name' => 'Template_Area_Chart',
                'description' => 'Template untuk grafik area',
                'headers' => ['Bulan', 'Volume'],
                'sample_data' => [
                    ['Jan', 12500],
                    ['Feb', 18500],
                    ['Mar', 16200],
                    ['Apr', 21000],
                    ['Mei', 24500],
                    ['Jun', 22800]
                ]
            ],
            'scatter_plot' => [
                'name' => 'Template_Scatter_Plot',
                'description' => 'Template untuk scatter plot',
                'headers' => ['Nilai X', 'Nilai Y'],
                'sample_data' => [
                    [10, 20],
                    [15, 35],
                    [20, 25],
                    [25, 45],
                    [30, 40]
                ]
            ],
            'histogram' => [
                'name' => 'Template_Histogram',
                'description' => 'Template untuk histogram',
                'headers' => ['Range', 'Frekuensi'],
                'sample_data' => [
                    ['0-10', 5],
                    ['11-20', 12],
                    ['21-30', 18],
                    ['31-40', 15],
                    ['41-50', 8]
                ]
            ]
        ];
        
        $template = $templates[$chartType] ?? [
            'name' => 'Template_Visualisasi',
            'description' => 'Template umum untuk visualisasi data',
            'headers' => ['Label', 'Nilai'],
            'sample_data' => [
                ['Item 1', 10],
                ['Item 2', 20],
                ['Item 3', 15],
                ['Item 4', 25],
                ['Item 5', 30]
            ]
        ];
        
        $filename = $template['name'] . '_' . date('Y-m-d') . '.xlsx';
        
        return Excel::download(
            new VisualisasiTemplateExport($template), 
            $filename
        );
    }
}