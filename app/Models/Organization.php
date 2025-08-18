<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'slug',
        'code',
        'description',
        'website'
    ];

    // Mutators
    public function setNameAttribute($value)
{
    $this->attributes['name'] = $value;

    // Generate slug otomatis dari name
    $this->attributes['slug'] = Str::slug($value, '-');

    // Generate code hanya kalau kosong dan ini record baru
    if (empty($this->attributes['code']) && !$this->exists) {
        $this->attributes['code'] = $this->generateUniqueCode();
    }
}

    // Accessors
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return asset('images/default-organization.png');
    }

    public function getFormattedWebsiteAttribute()
    {
        if ($this->website) {
            if (!str_starts_with($this->website, 'http://') && !str_starts_with($this->website, 'https://')) {
                return 'https://' . $this->website;
            }
            return $this->website;
        }
        return null;
    }

    // Scopes
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'ilike', "%{$search}%")
              ->orWhere('code', 'ilike', "%{$search}%")
              ->orWhere('description', 'ilike', "%{$search}%");
        });
    }

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Methods
    private function generateUniqueCode()
    {
        // Get the latest organization to determine the next number
        $lastOrganization = static::whereNotNull('code')
            ->where('code', 'like', 'SDT-%')
            ->orderByRaw("CAST(SUBSTRING(code FROM 5) AS INTEGER) DESC")
            ->first();
        
        if ($lastOrganization) {
            // Extract number from the last code (e.g., SDT-001 -> 1)
            $lastNumber = (int) substr($lastOrganization->code, 4);
            $nextNumber = $lastNumber + 1;
        } else {
            // Start from 1 if no organizations exist
            $nextNumber = 1;
        }
        
        // Format with leading zeros (3 digits)
        $code = 'SDT-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        
        // Double check for uniqueness (in case of race conditions)
        while (static::where('code', $code)->exists()) {
            $nextNumber++;
            $code = 'SDT-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }
        
        return $code;
    }

    /**
     * Generate next available SDT code
     */
    public static function generateNextCode()
    {
        $instance = new static();
        return $instance->generateUniqueCode();
    }

    /**
     * Get organization statistics
     */
    public static function getStats()
    {
        return [
            'total' => static::count(),
            'this_month' => static::whereMonth('created_at', now()->month)->count(),
            'with_website' => static::whereNotNull('website')->count(),
            'with_logo' => static::whereNotNull('logo')->count(),
        ];
    }

    /**
     * Get users count for this organization
     */
    public function getUsersCountAttribute()
    {
        return $this->users()->count();
    }
}