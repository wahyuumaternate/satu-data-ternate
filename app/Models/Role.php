<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // Table name (opsional kalau pakai konvensi)
    protected $table = 'roles';

    // Kolom yang bisa diisi
    protected $fillable = [
        'name',
        'label',
    ];

    /**
     * Relasi ke User
     * Satu Role bisa punya banyak User
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
