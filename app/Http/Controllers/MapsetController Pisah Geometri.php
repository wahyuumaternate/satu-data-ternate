<?php

namespace App\Http\Controllers;

use App\Models\Mapset;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Shapefile\ShapefileReader;
use Illuminate\Support\Facades\Validator;
use ZipArchive;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Auth;

class MapsetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Mapset::with('user')
            ->where('user_id', Auth::id())
            ->where('is_active', true);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama', 'like', "%{$searchTerm}%")
                  ->orWhere('deskripsi', 'like', "%{$searchTerm}%")
                  ->orWhereRaw("dbf_attributes::text ILIKE ?", ["%{$searchTerm}%"]);
            });
        }

        // Filter by topic
        if ($request->has('topic') && !empty($request->topic)) {
            $query->where('topic', $request->topic);
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
        
        // Get available topics from enum
        $topics = [
            'Ekonomi' => 'Ekonomi',
            'Infrastruktur' => 'Infrastruktur',
            'Kemiskinan' => 'Kemiskinan',
            'Kependudukan' => 'Kependudukan',
            'Kesehatan' => 'Kesehatan',
            'Lingkungan Hidup' => 'Lingkungan Hidup',
            'Pariwisata & Kebudayaan' => 'Pariwisata & Kebudayaan',
            'Pemerintah & Desa' => 'Pemerintah & Desa',
            'Pendidikan' => 'Pendidikan',
            'Sosial' => 'Sosial'
        ];

        return view('mapset.index', compact('mapsets', 'topics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $topics = [
            'Ekonomi' => 'Ekonomi',
            'Infrastruktur' => 'Infrastruktur',
            'Kemiskinan' => 'Kemiskinan',
            'Kependudukan' => 'Kependudukan',
            'Kesehatan' => 'Kesehatan',
            'Lingkungan Hidup' => 'Lingkungan Hidup',
            'Pariwisata & Kebudayaan' => 'Pariwisata & Kebudayaan',
            'Pemerintah & Desa' => 'Pemerintah & Desa',
            'Pendidikan' => 'Pendidikan',
            'Sosial' => 'Sosial'
        ];
        
        return view('mapset.create', compact('topics'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi dasar
        $rules = [
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'topic' => 'required|in:Ekonomi,Infrastruktur,Kemiskinan,Kependudukan,Kesehatan,Lingkungan Hidup,Pariwisata & Kebudayaan,Pemerintah & Desa,Pendidikan,Sosial',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'input_type' => 'required|in:shapefile,coordinates,kmz',
        ];

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $recordCount = 0;
            $inputType = $request->input('input_type');

            switch ($inputType) {
                case 'shapefile':
                    $recordCount = $this->processShapefileInput($request);
                    break;
                case 'coordinates':
                    $recordCount = $this->processCoordinatesInput($request);
                    break;
                case 'kmz':
                    $recordCount = $this->processKmzInput($request);
                    break;
                default:
                    throw new \Exception('Jenis input tidak valid');
            }

            DB::commit();

            $message = "Berhasil menyimpan {$recordCount} mapset dengan metode {$inputType}.";
            
            return redirect()->route('mapset.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing mapset: ' . $e->getMessage());
            return back()->withErrors(['Gagal menyimpan mapset: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        // dd($uuid);
        $mapset = Mapset::where('uuid', $uuid)->firstOrFail();

        // Check authorization - only owner or public mapsets
        if (!$mapset->is_visible && $mapset->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Increment views only if not owner
        if ($mapset->user_id !== Auth::id()) {
            $mapset->increment('views');
        }

        // Get GeoJSON data
        $geojsonQuery = DB::select("SELECT ST_AsGeoJSON(geom) as geojson FROM mapsets WHERE id = ?", [$mapset->id]);
        $geojson = $geojsonQuery ? json_decode($geojsonQuery[0]->geojson, true) : null;

        // Get bounds
        $boundsQuery = DB::select("SELECT ST_AsText(ST_Envelope(geom)) as bounds FROM mapsets WHERE id = ?", [$mapset->id]);
        $bounds = $boundsQuery ? $boundsQuery[0]->bounds : null;

        return view('mapset.show', compact('mapset', 'geojson', 'bounds'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $mapset = Mapset::where('uuid', $uuid)->firstOrFail();

        // Check authorization
        if ($mapset->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $topics = [
            'Ekonomi' => 'Ekonomi',
            'Infrastruktur' => 'Infrastruktur',
            'Kemiskinan' => 'Kemiskinan',
            'Kependudukan' => 'Kependudukan',
            'Kesehatan' => 'Kesehatan',
            'Lingkungan Hidup' => 'Lingkungan Hidup',
            'Pariwisata & Kebudayaan' => 'Pariwisata & Kebudayaan',
            'Pemerintah & Desa' => 'Pemerintah & Desa',
            'Pendidikan' => 'Pendidikan',
            'Sosial' => 'Sosial'
        ];

        // Get existing GeoJSON
        $geojsonQuery = DB::select("SELECT ST_AsGeoJSON(geom) as geojson FROM mapsets WHERE id = ?", [$mapset->id]);
        $geojson = $geojsonQuery ? json_decode($geojsonQuery[0]->geojson, true) : null;

        return view('mapset.edit', compact('mapset', 'topics', 'geojson'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $mapset = Mapset::where('uuid', $uuid)->firstOrFail();

        // Check authorization
        if ($mapset->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'topic' => 'required|in:Ekonomi,Infrastruktur,Kemiskinan,Kependudukan,Kesehatan,Lingkungan Hidup,Pariwisata & Kebudayaan,Pemerintah & Desa,Pendidikan,Sosial',
            'dbf_attributes' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048'
        ], [
            'nama.required' => 'Nama mapset harus diisi',
            'topic.required' => 'Topic harus dipilih',
            'gambar.image' => 'File harus berupa gambar',
            'gambar.mimes' => 'Format gambar harus jpeg, jpg, png, atau gif',
            'gambar.max' => 'Ukuran gambar maksimal 2MB'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Handle DBF Attributes
            $dbfAttributes = [];
            if ($request->dbf_attributes) {
                $json = $request->dbf_attributes;
                $dbfAttributes = json_decode($json, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return redirect()->back()
                        ->withErrors(['dbf_attributes' => 'Format JSON atribut tidak valid: ' . json_last_error_msg()])
                        ->withInput();
                }
            }

            // Handle Image Upload
            $imagePath = $mapset->gambar; // Keep existing image path
            
            if ($request->hasFile('gambar')) {
                // Delete old image if exists
                if ($mapset->gambar && Storage::disk('public')->exists('mapsets/' . $mapset->gambar)) {
                    Storage::disk('public')->delete('mapsets/' . $mapset->gambar);
                }
                
                // Store new image
                $file = $request->file('gambar');
                $fileName = time() . '_' . Str::slug($request->nama) . '.' . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs('mapsets', $fileName, 'public');
            }

            // Update mapset
            $mapset->nama = $request->nama;
            $mapset->deskripsi = $request->deskripsi;
            $mapset->topic = $request->topic;
            $mapset->dbf_attributes = $dbfAttributes;
            $mapset->gambar = $imagePath;
            $mapset->is_visible = $request->has('is_visible');
            $mapset->save();

            Log::info('Mapset updated successfully', [
                'id' => $mapset->id,
                'uuid' => $uuid,
                'attributes_count' => count($dbfAttributes),
                'image_uploaded' => $request->hasFile('gambar')
            ]);

            return redirect()->route('mapset.index')
                ->with('success', 'Mapset berhasil diperbarui!');
                
        } catch (\Exception $e) {
            // If there was an error and a new image was uploaded, delete it
            if ($request->hasFile('gambar') && isset($imagePath) && $imagePath !== $mapset->gambar) {
                Storage::disk('public')->delete($imagePath);
            }
            
            Log::error('Error updating mapset: ' . $e->getMessage(), [
                'id' => $mapset->id,
                'uuid' => $uuid,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui mapset: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $mapset = Mapset::where('uuid', $uuid)->firstOrFail();

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
    public function downloadGeojson($uuid)
    {
        $mapset = Mapset::where('uuid', $uuid)->firstOrFail();

        // Check authorization
        if ($mapset->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $geojsonQuery = DB::select("SELECT ST_AsGeoJSON(geom) as geojson FROM mapsets WHERE id = ?", [$mapset->id]);
        $geojson = $geojsonQuery ? json_decode($geojsonQuery[0]->geojson, true) : null;
        
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
    public function getMapsetData($uuid)
    {
        $mapset = Mapset::where('uuid', $uuid)->firstOrFail();

        // Check if mapset is public or user owns it
        if (!$mapset->is_visible && $mapset->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $geojsonQuery = DB::select("SELECT ST_AsGeoJSON(geom) as geojson FROM mapsets WHERE id = ?", [$mapset->id]);
        $geojson = $geojsonQuery ? json_decode($geojsonQuery[0]->geojson, true) : null;

        $boundsQuery = DB::select("SELECT ST_AsText(ST_Envelope(geom)) as bounds FROM mapsets WHERE id = ?", [$mapset->id]);
        $bounds = $boundsQuery ? $boundsQuery[0]->bounds : null;

        return response()->json([
            'mapset' => $mapset,
            'geojson' => $geojson,
            'bounds' => $bounds
        ]);
    }

    // === PROCESSING INPUT METHODS ===
    
    private function processShapefileInput(Request $request)
    {
        $request->validate([
            'shp_file' => 'required|file',
            'shx_file' => 'required|file',
            'dbf_file' => 'required|file',
        ]);

        $folder = storage_path('app/shapefiles');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }
        File::cleanDirectory($folder);

        // Simpan file
        $request->file('shp_file')->move($folder, 'data.shp');
        $request->file('shx_file')->move($folder, 'data.shx');
        $request->file('dbf_file')->move($folder, 'data.dbf');

        $shpPath = "$folder/data.shp";

        if (!file_exists($shpPath)) {
            throw new \Exception('Gagal menyimpan file shapefile.');
        }

        $reader = new ShapefileReader($shpPath);
        $recordCount = 0;

        while ($geometry = $reader->fetchRecord()) {
            if ($geometry->isDeleted()) continue;

            $wkt = $geometry->getWKT();
            $dbfData = $geometry->getDataArray();

            // Bersihkan dan normalisasi data DBF
            $cleanDbfData = $this->cleanDbfData($dbfData);

            // Tentukan nama dari DBF data
            $namaMapset = $this->determineMapsetName($request->nama, $cleanDbfData, $recordCount + 1);

            // Proses geometri
            $processedWkt = $this->processGeometryDimensions($wkt);
            $this->validateGeometryCoordinates($processedWkt);

            // Simpan mapset
            $this->saveMapset($request, $namaMapset, $cleanDbfData, $processedWkt);

            $recordCount++;
        }

        if ($recordCount === 0) {
            throw new \Exception('Shapefile tidak berisi data geometrik yang valid.');
        }

        return $recordCount;
    }

    private function processCoordinatesInput(Request $request)
    {
        $request->validate([
            'coordinates' => 'required|array|min:1',
            'coordinates.*.latitude' => 'required|numeric',
            'coordinates.*.longitude' => 'required|numeric',
            'coordinates.*.name' => 'nullable|string|max:255',
        ]);

        $coordinates = $request->input('coordinates');
        $recordCount = 0;

        foreach ($coordinates as $index => $coord) {
            if (empty($coord['latitude']) || empty($coord['longitude'])) {
                continue;
            }

            $lat = (float) $coord['latitude'];
            $lng = (float) $coord['longitude'];
            $name = $coord['name'] ?? ($request->nama . ' - ' . ($index + 1));

            // Buat WKT Point
            $wkt = "POINT({$lng} {$lat})";

            // Buat DBF attributes dari input koordinat
            $dbfAttributes = [
                'NAMA' => $name,
                'LATITUDE' => $lat,
                'LONGITUDE' => $lng,
                'INPUT_TYPE' => 'manual_coordinates'
            ];

            // Simpan mapset
            $this->saveMapset($request, $name, $dbfAttributes, $wkt);

            $recordCount++;
        }

        if ($recordCount === 0) {
            throw new \Exception('Tidak ada koordinat valid yang dapat disimpan.');
        }

        return $recordCount;
    }

    private function processKmzInput(Request $request)
    {
        $request->validate([
            'kmz_file' => 'required|file',
        ]);

        $file = $request->file('kmz_file');
        $fileName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        $tempDir = storage_path('app/temp_kmz');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        File::cleanDirectory($tempDir);

        $kmlContent = null;

        if ($extension === 'kmz') {
            // Extract KMZ file
            $kmzPath = $tempDir . '/temp.kmz';
            $file->move($tempDir, 'temp.kmz');

            $zip = new ZipArchive;
            if ($zip->open($kmzPath) === TRUE) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    if (pathinfo($filename, PATHINFO_EXTENSION) === 'kml') {
                        $kmlContent = $zip->getFromIndex($i);
                        break;
                    }
                }
                $zip->close();
            } else {
                throw new \Exception('Gagal membuka file KMZ.');
            }
        } else {
            // Direct KML file
            $kmlContent = file_get_contents($file->getRealPath());
        }

        if (!$kmlContent) {
            throw new \Exception('Tidak dapat menemukan file KML dalam arsip.');
        }

        return $this->parseKmlContent($kmlContent, $request);
    }

    private function parseKmlContent($kmlContent, $request)
    {
        $dom = new DOMDocument();
        $dom->loadXML($kmlContent);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('kml', 'http://www.opengis.net/kml/2.2');

        $recordCount = 0;
        $placemarks = $xpath->query('//kml:Placemark');

        foreach ($placemarks as $placemark) {
            $name = $xpath->query('.//kml:name', $placemark)->item(0);
            $description = $xpath->query('.//kml:description', $placemark)->item(0);
            
            $nameText = $name ? trim($name->textContent) : ($request->nama . ' - ' . ($recordCount + 1));
            $descText = $description ? trim($description->textContent) : '';

            // Parse geometri
            $geometries = $this->parseKmlGeometry($xpath, $placemark);

            foreach ($geometries as $geometry) {
                $dbfAttributes = [
                    'NAMA' => $nameText,
                    'DESCRIPTION' => $descText,
                    'INPUT_TYPE' => 'kmz_import',
                    'ORIGINAL_FILE' => $request->file('kmz_file')->getClientOriginalName()
                ];

                $this->saveMapset($request, $nameText, $dbfAttributes, $geometry);

                $recordCount++;
            }
        }

        if ($recordCount === 0) {
            throw new \Exception('File KMZ/KML tidak berisi data geometrik yang valid.');
        }

        return $recordCount;
    }

    // === HELPER METHODS ===
    
    private function saveMapset(Request $request, $nama, $dbfAttributes, $wkt)
    {
        
        try {
            $mapset = new Mapset();
            $mapset->user_id = Auth::id();
            $mapset->uuid = Str::uuid();
            $mapset->nama = $nama;
            $mapset->deskripsi = $request->deskripsi;
            $mapset->topic = $request->topic;
            $mapset->dbf_attributes = $dbfAttributes;
            $mapset->is_visible = $request->has('is_visible');
            $mapset->is_active = true;
            $mapset->views = 0;

            // Handle gambar upload (only for first record if multiple)
            if ($request->hasFile('gambar') && !Mapset::where('user_id', Auth::id())->whereDate('created_at', today())->exists()) {
                $file = $request->file('gambar');
                $filename = time() . '_' . Str::slug($nama) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/mapsets', $filename);
                $mapset->gambar = $filename;
            }

            $mapset->save();

            // Insert geometry using PostGIS
            DB::statement("UPDATE mapsets SET geom = ST_GeomFromText(?, 4326) WHERE id = ?", 
                [$wkt, $mapset->id]);

        } catch (\Exception $e) {
            Log::error('Failed to save Mapset: ' . $e->getMessage());
            throw $e;
        }
    }

    private function parseKmlGeometry($xpath, $placemark)
    {
        $geometries = [];

        // Parse Point
        $points = $xpath->query('.//kml:Point/kml:coordinates', $placemark);
        foreach ($points as $point) {
            $coords = trim($point->textContent);
            $coordArray = explode(',', $coords);
            if (count($coordArray) >= 2) {
                $lng = trim($coordArray[0]);
                $lat = trim($coordArray[1]);
                if (is_numeric($lng) && is_numeric($lat)) {
                    $geometries[] = "POINT({$lng} {$lat})";
                }
            }
        }

        // Parse LineString
        $lineStrings = $xpath->query('.//kml:LineString/kml:coordinates', $placemark);
        foreach ($lineStrings as $lineString) {
            $coords = trim($lineString->textContent);
            $wkt = $this->convertKmlCoordsToLineString($coords);
            if ($wkt) {
                $geometries[] = $wkt;
            }
        }

        // Parse Polygon
        $polygons = $xpath->query('.//kml:Polygon', $placemark);
        foreach ($polygons as $polygon) {
            $outerBoundary = $xpath->query('.//kml:outerBoundaryIs/kml:LinearRing/kml:coordinates', $polygon)->item(0);
            if ($outerBoundary) {
                $coords = trim($outerBoundary->textContent);
                $wkt = $this->convertKmlCoordsToPolygon($coords);
                if ($wkt) {
                    $geometries[] = $wkt;
                }
            }
        }

        return $geometries;
    }

    private function convertKmlCoordsToLineString($coordsText)
    {
        $points = preg_split('/\s+/', trim($coordsText));
        $wktPoints = [];

        foreach ($points as $point) {
            $coords = explode(',', $point);
            if (count($coords) >= 2) {
                $lng = trim($coords[0]);
                $lat = trim($coords[1]);
                if (is_numeric($lng) && is_numeric($lat)) {
                    $wktPoints[] = "{$lng} {$lat}";
                }
            }
        }

        if (count($wktPoints) >= 2) {
            return "LINESTRING(" . implode(',', $wktPoints) . ")";
        }

        return null;
    }

    private function convertKmlCoordsToPolygon($coordsText)
    {
        $points = preg_split('/\s+/', trim($coordsText));
        $wktPoints = [];

        foreach ($points as $point) {
            $coords = explode(',', $point);
            if (count($coords) >= 2) {
                $lng = trim($coords[0]);
                $lat = trim($coords[1]);
                if (is_numeric($lng) && is_numeric($lat)) {
                    $wktPoints[] = "{$lng} {$lat}";
                }
            }
        }

        if (count($wktPoints) >= 4) {
            // Pastikan polygon tertutup
            if ($wktPoints[0] !== $wktPoints[count($wktPoints) - 1]) {
                $wktPoints[] = $wktPoints[0];
            }
            return "POLYGON((" . implode(',', $wktPoints) . "))";
        }

        return null;
    }

    private function cleanDbfData($dbfData)
    {
        $cleanDbfData = [];
        foreach ($dbfData as $key => $value) {
            $cleanKey = trim($key);
            $cleanValue = is_string($value) ? trim($value) : $value;
            
            if (is_string($cleanValue) && !mb_check_encoding($cleanValue, 'UTF-8')) {
                $cleanValue = mb_convert_encoding($cleanValue, 'UTF-8', 'auto');
            }
            
            $cleanDbfData[$cleanKey] = $cleanValue;
        }
        return $cleanDbfData;
    }

    private function determineMapsetName($baseName, $dbfData, $index)
    {
        // Cari nama dari DBF data
        $possibleNameFields = ['NAMA_OBJEK', 'NAMOBJ', 'NAMA', 'NAME'];
        foreach ($possibleNameFields as $field) {
            if (isset($dbfData[$field]) && !empty($dbfData[$field])) {
                return $dbfData[$field];
            }
        }

        // Jika tidak ada, gunakan base name + index
        return $baseName . ' - ' . $index;
    }

    private function validateGeometryCoordinates($wkt)
    {
        if (preg_match('/POINT\s*\(([\d\.\-]+)\s+([\d\.\-]+)\)/i', $wkt, $matches)) {
            $lng = (float) $matches[1];
            $lat = (float) $matches[2];

            if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
                throw new \Exception("Koordinat POINT berada di luar jangkauan WGS 84: ({$lng}, {$lat})");
            }
        }
    }

    private function processGeometryDimensions($wkt)
    {
        try {
            if (strpos($wkt, 'ZM') !== false) {
                return $wkt;
            } elseif (strpos($wkt, 'Z ') !== false || strpos($wkt, 'M ') !== false) {
                return $wkt;
            }
            
            return $this->stripGeometryDimensions($wkt);
            
        } catch (\Exception $e) {
            Log::warning("Gagal memproses geometri: " . $e->getMessage());
            return $this->stripGeometryDimensions($wkt);
        }
    }

   private function stripGeometryDimensions($wkt)
    {
        // Hapus suffix ZM, Z, atau M dari tipe geometri
        $wkt = preg_replace('/\b(MULTIPOLYGON|POLYGON|MULTIPOINT|POINT|MULTILINESTRING|LINESTRING|GEOMETRYCOLLECTION)(ZM|Z|M)\b/i', '$1', $wkt);
        
        $wkt = preg_replace_callback('/(\-?\d+\.?\d*)\s+(\-?\d+\.?\d*)\s+(\-?\d+\.?\d*)\s+(\-?\d+\.?\d*)/', function($matches) {
            return $matches[1] . ' ' . $matches[2];
        }, $wkt);
        
        $wkt = preg_replace_callback('/(\-?\d+\.?\d*)\s+(\-?\d+\.?\d*)\s+(\-?\d+\.?\d*)(?!\s+\-?\d)/', function($matches) {
            return $matches[1] . ' ' . $matches[2];
        }, $wkt);
        
        return $wkt;
    }
    }