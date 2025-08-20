<?php

namespace App\Http\Controllers;

use App\Models\Infografis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InfografisController extends Controller
{
    private function checkViewPermission()
    {
        $user = Auth::user();
        
        if ($user->hasRole('reviewer')) {
            abort(403, 'Reviewer tidak memiliki akses ke infografis management.');
        }
    }

    private function checkCreatePermission()
    {
        $user = Auth::user();
        
        if ($user->hasRole('reviewer')) {
            abort(403, 'Reviewer tidak memiliki akses ke infografis management.');
        }
        
        if ($user->hasRole('penanggung-jawab')) {
            abort(403, 'Penanggung jawab hanya dapat melihat dan mendownload infografis.');
        }
        
        if (!$user->hasRole(['super-admin', 'opd'])) {
            abort(403, 'Hanya Super Admin dan OPD yang dapat membuat infografis.');
        }
    }

    private function checkEditPermission()
    {
        $user = Auth::user();
        
        if ($user->hasRole('reviewer')) {
            abort(403, 'Reviewer tidak memiliki akses ke infografis management.');
        }
        
        if ($user->hasRole('penanggung-jawab')) {
            abort(403, 'Penanggung jawab hanya dapat melihat dan mendownload infografis.');
        }
        
        if (!$user->hasRole(['super-admin', 'opd'])) {
            abort(403, 'Hanya Super Admin dan OPD yang dapat mengedit infografis.');
        }
    }

    private function checkInfografisAccess($infografis)
    {
        $user = Auth::user();
        
        // Super admin bisa akses semua
        if ($user->hasRole('super-admin')) {
            return;
        }
        
        // OPD hanya bisa akses data miliknya
        if ($user->hasRole('opd')) {
            if ($infografis->user_id !== $user->id) {
                abort(403, 'Anda hanya dapat mengakses infografis milik Anda sendiri.');
            }
            return;
        }
        
        // Penanggung jawab hanya bisa akses data yang aktif dan publik
        if ($user->hasRole('penanggung-jawab')) {
            if (!$infografis->is_active || !$infografis->is_public) {
                abort(403, 'Anda hanya dapat mengakses infografis yang aktif dan publik.');
            }
            return;
        }
    }

    private function checkDownloadPermission()
    {
        $user = Auth::user();
        
        if ($user->hasRole('reviewer')) {
            abort(403, 'Reviewer tidak memiliki akses untuk download infografis.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkViewPermission();
        
        $user = Auth::user();
        $query = Infografis::with('user');

        // Filter berdasarkan role
        if ($user->hasRole('super-admin')) {
            // Super admin bisa melihat semua
            $query->active()->public();
        } elseif ($user->hasRole('opd')) {
            // OPD hanya bisa melihat miliknya sendiri
            $query->where('user_id', $user->id);
        } elseif ($user->hasRole('penanggung-jawab')) {
            // Penanggung jawab bisa melihat semua data aktif dan publik
            $query->active()->public();
        }

        // Filter berdasarkan topic
        if ($request->filled('topic')) {
            $query->byTopic($request->topic);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter berdasarkan tags
        if ($request->filled('tag')) {
            $query->whereJsonContains('tags', $request->tag);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'popular':
                $query->popular();
                break;
            case 'downloads':
                $query->mostDownloaded();
                break;
            case 'oldest':
                $query->oldest();
                break;
            case 'name':
                $query->orderBy('nama', 'asc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        // Pagination
        $perPage = $request->get('per_page', 12);
        $infografis = $query->paginate($perPage)->withQueryString();

        // Data untuk filters (hanya untuk yang bisa melihat semua data)
        $topics = [];
        $popularTags = [];
        $stats = [];
        $featured = collect();

        if ($user->hasRole(['super-admin', 'penanggung-jawab'])) {
            $topics = $this->getTopicsWithCount();
            $popularTags = $this->getPopularTags();
            $stats = $this->getStats();
            
            // Featured/Popular infografis
            $featured = Infografis::active()
                ->public()
                ->popular()
                ->limit(6)
                ->get();
        }

        return view('infografis.index', compact(
            'infografis',
            'topics',
            'popularTags',
            'stats',
            'featured'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkCreatePermission();

        $topics = Infografis::TOPICS;
        return view('infografis.create', compact('topics'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkCreatePermission();

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'topic' => 'nullable|in:' . implode(',', Infografis::TOPICS),
            'data_sources' => 'nullable|array',
            'data_sources.*' => 'string|max:255',
            'metodologi' => 'nullable|string',
            'periode_data_mulai' => 'nullable|date',
            'periode_data_selesai' => 'nullable|date|after_or_equal:periode_data_mulai',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
        ]);

        // Handle file upload
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . Str::slug($validated['nama']) . '.' . $file->getClientOriginalExtension();
            $validated['gambar'] = $file->storeAs('infografis', $filename, 'public');
        }

        // Set user_id
        $validated['user_id'] = auth()->id();

        // Generate unique slug
        $validated['slug'] = $this->generateUniqueSlug($validated['nama']);

        $infografis = Infografis::create($validated);

        return redirect()
            ->route('infografis.show', $infografis)
            ->with('success', 'Infografis berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Infografis $infografis)
    {
        $this->checkViewPermission();
        $this->checkInfografisAccess($infografis);

        // Increment views
        $infografis->incrementViews();

        // Load user relationship
        $infografis->load('user');

        // Similar infografis based on topic
        $similar = Infografis::active()
            ->public()
            ->where('id', '!=', $infografis->id)
            ->where('topic', $infografis->topic)
            ->latest()
            ->limit(4)
            ->get();

        // Related by tags
        $related = collect();
        if ($infografis->tags && count($infografis->tags) > 0) {
            $related = Infografis::active()
                ->public()
                ->where('id', '!=', $infografis->id)
                ->where(function($query) use ($infografis) {
                    foreach ($infografis->tags as $tag) {
                        $query->orWhereJsonContains('tags', $tag);
                    }
                })
                ->latest()
                ->limit(4)
                ->get();
        }

        return view('infografis.show', compact('infografis', 'similar', 'related'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Infografis $infografis)
    {
        $this->checkEditPermission();
        $this->checkInfografisAccess($infografis);
        
        $topics = Infografis::TOPICS;
        return view('infografis.edit', compact('infografis', 'topics'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Infografis $infografis)
    {
        $this->checkEditPermission();
        $this->checkInfografisAccess($infografis);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'topic' => 'nullable|in:' . implode(',', Infografis::TOPICS),
            'data_sources' => 'nullable|array',
            'data_sources.*' => 'string|max:255',
            'metodologi' => 'nullable|string',
            'periode_data_mulai' => 'nullable|date',
            'periode_data_selesai' => 'nullable|date|after_or_equal:periode_data_mulai',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
        ]);

        // Handle file upload
        if ($request->hasFile('gambar')) {
            // Delete old image
            if ($infografis->gambar && Storage::disk('public')->exists($infografis->gambar)) {
                Storage::disk('public')->delete($infografis->gambar);
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . Str::slug($validated['nama']) . '.' . $file->getClientOriginalExtension();
            $validated['gambar'] = $file->storeAs('infografis', $filename, 'public');
        }

        // Update slug if name changed
        if ($validated['nama'] !== $infografis->nama) {
            $validated['slug'] = $this->generateUniqueSlug($validated['nama'], $infografis->id);
        }

        $infografis->update($validated);

        return redirect()
            ->route('infografis.show', $infografis)
            ->with('success', 'Infografis berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Infografis $infografis)
    {
        $this->checkEditPermission();
        $this->checkInfografisAccess($infografis);

        // Delete associated file
        if ($infografis->gambar && Storage::disk('public')->exists($infografis->gambar)) {
            Storage::disk('public')->delete($infografis->gambar);
        }

        $infografis->delete();

        return redirect()
            ->route('infografis.index')
            ->with('success', 'Infografis berhasil dihapus.');
    }

    /**
     * Download infografis
     */
    public function download(Infografis $infografis)
    {
        $this->checkDownloadPermission();
        
        $user = Auth::user();

        // Check authorization untuk download
        if ($user->hasRole('super-admin')) {
            // Super admin bisa download semua
        } elseif ($user->hasRole(['opd', 'penanggung-jawab'])) {
            // OPD dan Penanggung jawab bisa download yang aktif dan publik
            if (!$infografis->is_active || !$infografis->is_public) {
                abort(404, 'File tidak dapat diunduh.');
            }
        }

        $filePath = storage_path('app/public/' . $infografis->gambar);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // Increment downloads
        $infografis->incrementDownloads();

        // Get file info
        $fileName = $infografis->slug . '.' . pathinfo($infografis->gambar, PATHINFO_EXTENSION);

        return response()->download($filePath, $fileName);
    }

    /**
     * Download template for creating infografis
     */
    public function downloadTemplate()
    {
        $this->checkCreatePermission();

        $templatePath = resource_path('templates/infografis_template.csv');
        
        if (!file_exists($templatePath)) {
            // Create CSV template content
            $csvContent = "nama,deskripsi,topic,data_sources,metodologi,periode_data_mulai,periode_data_selesai,tags\n";
            $csvContent .= "Contoh Infografis,Deskripsi infografis ini,Ekonomi,\"BPS,Bank Indonesia\",Analisis data sekunder,2024-01-01,2024-12-31,\"ekonomi,pertumbuhan\"\n";
            
            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="template_infografis.csv"');
        }

        return response()->download($templatePath, 'template_infografis.csv');
    }

    /**
     * Export metadata of infografis
     */
    public function exportMetadata(Infografis $infografis)
    {
        $this->checkDownloadPermission();
        
        $user = Auth::user();

        // Check authorization untuk export metadata
        if ($user->hasRole('super-admin')) {
            // Super admin bisa export semua
        } elseif ($user->hasRole('opd')) {
            // OPD hanya bisa export miliknya sendiri atau yang publik
            if ($infografis->user_id !== $user->id && (!$infografis->is_active || !$infografis->is_public)) {
                abort(403, 'Anda tidak memiliki akses untuk mengekspor metadata ini.');
            }
        } elseif ($user->hasRole('penanggung-jawab')) {
            // Penanggung jawab bisa export yang aktif dan publik
            if (!$infografis->is_active || !$infografis->is_public) {
                abort(403, 'Anda tidak memiliki akses untuk mengekspor metadata ini.');
            }
        }

        $metadata = [
            'id' => $infografis->id,
            'nama' => $infografis->nama,
            'slug' => $infografis->slug,
            'deskripsi' => $infografis->deskripsi,
            'topic' => $infografis->topic,
            'data_sources' => $infografis->data_sources,
            'metodologi' => $infografis->metodologi,
            'periode_data_mulai' => $infografis->periode_data_mulai?->format('Y-m-d'),
            'periode_data_selesai' => $infografis->periode_data_selesai?->format('Y-m-d'),
            'tags' => $infografis->tags,
            'views' => $infografis->views,
            'downloads' => $infografis->downloads,
            'created_at' => $infografis->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $infografis->updated_at->format('Y-m-d H:i:s'),
        ];

        $filename = 'metadata_' . $infografis->slug . '.json';

        return response()->json($metadata, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Export info of infografis as text
     */
    public function exportInfo(Infografis $infografis)
    {
        $this->checkDownloadPermission();
        
        $user = Auth::user();

        // Check authorization untuk export info (sama seperti export metadata)
        if ($user->hasRole('super-admin')) {
            // Super admin bisa export semua
        } elseif ($user->hasRole('opd')) {
            // OPD hanya bisa export miliknya sendiri atau yang publik
            if ($infografis->user_id !== $user->id && (!$infografis->is_active || !$infografis->is_public)) {
                abort(403, 'Anda tidak memiliki akses untuk mengekspor informasi ini.');
            }
        } elseif ($user->hasRole('penanggung-jawab')) {
            // Penanggung jawab bisa export yang aktif dan publik
            if (!$infografis->is_active || !$infografis->is_public) {
                abort(403, 'Anda tidak memiliki akses untuk mengekspor informasi ini.');
            }
        }

        $info = "INFORMASI INFOGRAFIS\n";
        $info .= "===================\n\n";
        $info .= "Nama: " . $infografis->nama . "\n";
        $info .= "Deskripsi: " . ($infografis->deskripsi ?: '-') . "\n";
        $info .= "Topik: " . ($infografis->topic ?: '-') . "\n";
        $info .= "Periode Data: " . $infografis->getPeriodeText() . "\n";
        $info .= "Sumber Data: " . $infografis->getDataSourcesString() . "\n";
        $info .= "Metodologi: " . ($infografis->metodologi ?: '-') . "\n";
        $info .= "Tags: " . $infografis->getTagsString() . "\n";
        $info .= "Views: " . number_format($infografis->views) . "\n";
        $info .= "Downloads: " . number_format($infografis->downloads) . "\n";
        $info .= "Dibuat: " . $infografis->created_at->format('d M Y H:i') . "\n";

        $filename = 'info_' . $infografis->slug . '.txt';

        return response($info)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * API endpoint for search suggestions
     */
    public function suggestions(Request $request)
    {
        $this->checkViewPermission();
        
        $user = Auth::user();
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $infografisQuery = Infografis::where('nama', 'ilike', "%{$query}%")
            ->select('nama', 'slug', 'topic')
            ->limit(10);

        // Filter berdasarkan role
        if ($user->hasRole('super-admin')) {
            $infografisQuery->active()->public();
        } elseif ($user->hasRole('opd')) {
            $infografisQuery->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere(function($q2) {
                      $q2->where('is_active', true)->where('is_public', true);
                  });
            });
        } elseif ($user->hasRole('penanggung-jawab')) {
            $infografisQuery->active()->public();
        }

        $suggestions = $infografisQuery->get();

        return response()->json($suggestions);
    }

    /**
     * Get infografis by topic (for AJAX)
     */
    public function byTopic($topic)
    {
        $this->checkViewPermission();
        
        $user = Auth::user();
        $query = Infografis::with('user')->byTopic($topic);

        // Filter berdasarkan role
        if ($user->hasRole('super-admin')) {
            $query->active()->public();
        } elseif ($user->hasRole('opd')) {
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere(function($q2) {
                      $q2->where('is_active', true)->where('is_public', true);
                  });
            });
        } elseif ($user->hasRole('penanggung-jawab')) {
            $query->active()->public();
        }

        $infografis = $query->latest()->paginate(12);

        if (request()->ajax()) {
            return response()->json([
                'data' => $infografis->items(),
                'pagination' => [
                    'current_page' => $infografis->currentPage(),
                    'last_page' => $infografis->lastPage(),
                    'per_page' => $infografis->perPage(),
                    'total' => $infografis->total(),
                ]
            ]);
        }

        return redirect()->route('infografis.index', ['topic' => $topic]);
    }

    /**
     * API search endpoint
     */
    public function search(Request $request)
    {
        $this->checkViewPermission();
        
        $user = Auth::user();
        $query = $request->get('q');
        $topic = $request->get('topic');
        $limit = $request->get('limit', 20);

        $infografisQuery = Infografis::with('user');

        // Filter berdasarkan role
        if ($user->hasRole('super-admin')) {
            $infografisQuery->active()->public();
        } elseif ($user->hasRole('opd')) {
            $infografisQuery->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere(function($q2) {
                      $q2->where('is_active', true)->where('is_public', true);
                  });
            });
        } elseif ($user->hasRole('penanggung-jawab')) {
            $infografisQuery->active()->public();
        }

        if ($query) {
            $infografisQuery->search($query);
        }

        if ($topic) {
            $infografisQuery->byTopic($topic);
        }

        $infografis = $infografisQuery->latest()->limit($limit)->get();

        return response()->json([
            'data' => $infografis,
            'total' => $infografis->count()
        ]);
    }

    /**
     * Toggle status (active/inactive) for AJAX
     */
    public function toggleStatus(Request $request, Infografis $infografis)
    {
        $this->checkEditPermission();
        $this->checkInfografisAccess($infografis);

        $field = $request->get('field', 'is_active');
        
        if (!in_array($field, ['is_active', 'is_public'])) {
            return response()->json(['error' => 'Invalid field'], 400);
        }

        $infografis->update([
            $field => !$infografis->{$field}
        ]);

        return response()->json([
            'success' => true,
            'status' => $infografis->{$field},
            'message' => 'Status berhasil diubah.'
        ]);
    }

    /**
     * Get topics with count
     */
    private function getTopicsWithCount()
    {
        return DB::table('infografis')
            ->select('topic', DB::raw('count(*) as count'))
            ->where('is_active', true)
            ->where('is_public', true)
            ->whereNotNull('topic')
            ->groupBy('topic')
            ->orderBy('count', 'desc')
            ->pluck('count', 'topic');
    }

    /**
     * Get popular tags
     */
    private function getPopularTags($limit = 20)
    {
        $tags = DB::table('infografis')
            ->where('is_active', true)
            ->where('is_public', true)
            ->whereNotNull('tags')
            ->pluck('tags');

        $allTags = [];
        foreach ($tags as $tagJson) {
            $tagArray = json_decode($tagJson, true);
            if (is_array($tagArray)) {
                $allTags = array_merge($allTags, $tagArray);
            }
        }

        $tagCounts = array_count_values($allTags);
        arsort($tagCounts);

        return array_slice($tagCounts, 0, $limit, true);
    }

    /**
     * Get statistics
     */
    private function getStats()
    {
        return [
            'total' => Infografis::active()->public()->count(),
            'total_views' => Infografis::active()->public()->sum('views'),
            'total_downloads' => Infografis::active()->public()->sum('downloads'),
            'topics_count' => Infografis::active()->public()->distinct('topic')->count('topic'),
            'this_month' => Infografis::active()->public()->whereMonth('created_at', now()->month)->count(),
        ];
    }

    /**
     * Generate unique slug
     */
    private function generateUniqueSlug($name, $excludeId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        $query = Infografis::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
            
            $query = Infografis::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }
}