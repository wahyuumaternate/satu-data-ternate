<?php

namespace App\Http\Controllers;

use App\Models\Mapset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MapsetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Mapset::with('user')
            ->where('user_id', Auth::id())
            ->active();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        // Filter by topic
        if ($request->has('topic') && !empty($request->topic)) {
            $query->byTopic($request->topic);
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        switch ($sortBy) {
            case 'nama':
                $query->orderBy('nama', $sortOrder);
                break;
            case 'views':
                $query->orderBy('views', $sortOrder);
                break;
            case 'topic':
                $query->orderBy('topic', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        $mapsets = $query->paginate(12);
        $topics = Mapset::getTopics();

        return view('mapset.index', compact('mapsets', 'topics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $topics = Mapset::getTopics();
        return view('mapset.create', compact('topics'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'topic' => 'required|in:' . implode(',', array_keys(Mapset::getTopics())),
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'geojson_file' => 'nullable|file|mimes:json,geojson',
            'geojson_data' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $mapset = new Mapset();
            $mapset->user_id = Auth::id();
            $mapset->nama = $request->nama;
            $mapset->deskripsi = $request->deskripsi;
            $mapset->topic = $request->topic;
            $mapset->is_visible = $request->has('is_visible');
            $mapset->is_active = true;

            // Handle gambar upload
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = time() . '_' . Str::slug($request->nama) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/mapsets', $filename);
                $mapset->gambar = $filename;
            }

            $mapset->save();

            // Handle GeoJSON data
            $geojsonData = null;
            
            if ($request->hasFile('geojson_file')) {
                $geojsonContent = file_get_contents($request->file('geojson_file')->getRealPath());
                $geojsonData = json_decode($geojsonContent, true);
            } elseif ($request->geojson_data) {
                $geojsonData = json_decode($request->geojson_data, true);
            }

            if ($geojsonData) {
                // Validate GeoJSON structure
                if (!isset($geojsonData['type']) || !isset($geojsonData['geometry'])) {
                    throw new \Exception('Invalid GeoJSON format');
                }

                $geometry = $geojsonData['geometry'] ?? $geojsonData;
                $geometryJson = json_encode($geometry);

                // Insert geometry using PostGIS
                DB::statement("UPDATE mapsets SET geom = ST_GeomFromGeoJSON(?) WHERE id = ?", 
                    [$geometryJson, $mapset->id]);
            }

            DB::commit();

            return redirect()->route('mapset.index')
                ->with('success', 'Mapset berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollback();
            
            // Delete uploaded file if exists
            if (isset($filename)) {
                Storage::delete('public/mapsets/' . $filename);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat mapset: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Mapset $mapset)
    {
        // Check authorization
        if ($mapset->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Increment views
        $mapset->incrementViews();

        // Get GeoJSON data
        $geojson = $mapset->getCoordinates();
        $bounds = $mapset->getBounds();

        return view('mapset.show', compact('mapset', 'geojson', 'bounds'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mapset $mapset)
    {
        // Check authorization
        if ($mapset->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $topics = Mapset::getTopics();
        $geojson = $mapset->getCoordinates();

        return view('mapset.edit', compact('mapset', 'topics', 'geojson'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mapset $mapset)
    {
        // Check authorization
        if ($mapset->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'topic' => 'required|in:' . implode(',', array_keys(Mapset::getTopics())),
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'geojson_file' => 'nullable|file|mimes:json,geojson',
            'geojson_data' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $mapset->nama = $request->nama;
            $mapset->deskripsi = $request->deskripsi;
            $mapset->topic = $request->topic;
            $mapset->is_visible = $request->has('is_visible');

            // Handle gambar upload
            if ($request->hasFile('gambar')) {
                // Delete old image
                if ($mapset->gambar) {
                    Storage::delete('public/mapsets/' . $mapset->gambar);
                }

                $file = $request->file('gambar');
                $filename = time() . '_' . Str::slug($request->nama) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/mapsets', $filename);
                $mapset->gambar = $filename;
            }

            $mapset->save();

            // Handle GeoJSON data update
            $geojsonData = null;
            
            if ($request->hasFile('geojson_file')) {
                $geojsonContent = file_get_contents($request->file('geojson_file')->getRealPath());
                $geojsonData = json_decode($geojsonContent, true);
            } elseif ($request->geojson_data) {
                $geojsonData = json_decode($request->geojson_data, true);
            }

            if ($geojsonData) {
                $geometry = $geojsonData['geometry'] ?? $geojsonData;
                $geometryJson = json_encode($geometry);

                DB::statement("UPDATE mapsets SET geom = ST_GeomFromGeoJSON(?) WHERE id = ?", 
                    [$geometryJson, $mapset->id]);
            }

            DB::commit();

            return redirect()->route('mapset.index')
                ->with('success', 'Mapset berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui mapset: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mapset $mapset)
    {
        // Check authorization
        if ($mapset->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        try {
            // Delete associated image
            if ($mapset->gambar) {
                Storage::delete('public/mapsets/' . $mapset->gambar);
            }

            $mapset->delete();

            return redirect()->route('mapset.index')
                ->with('success', 'Mapset berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus mapset: ' . $e->getMessage());
        }
    }

    /**
     * Download GeoJSON data
     */
    public function downloadGeojson(Mapset $mapset)
    {
        // Check authorization
        if ($mapset->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $geojson = $mapset->getCoordinates();
        
        if (!$geojson) {
            return redirect()->back()->with('error', 'Tidak ada data geometry untuk didownload');
        }

        $filename = Str::slug($mapset->nama) . '.geojson';
        
        return response()->json($geojson)
            ->header('Content-Type', 'application/geo+json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Get mapset data for API/AJAX
     */
    public function getMapsetData(Mapset $mapset)
    {
        // Check if mapset is public or user owns it
        if (!$mapset->is_visible && $mapset->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $geojson = $mapset->getCoordinates();
        $bounds = $mapset->getBounds();

        return response()->json([
            'mapset' => $mapset,
            'geojson' => $geojson,
            'bounds' => $bounds
        ]);
    }
}