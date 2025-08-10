<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'headers',
        'data',
        'total_rows'
    ];

    protected $casts = [
        'headers' => 'array',
        'data' => 'array',
        'total_rows' => 'integer'
    ];

    // Accessor untuk mendapatkan data dengan pagination
    public function getPaginatedData($perPage = 10, $page = 1)
    {
        $data = $this->data ?? [];
        $total = count($data);
        $offset = ($page - 1) * $perPage;
        
        return [
            'data' => array_slice($data, $offset, $perPage),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
}