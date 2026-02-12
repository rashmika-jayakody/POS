<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'status',
        'plan',
    ];

    /**
     * Generate a unique slug from company name (e.g. "Acme Grocers" -> "acme-grocers" or "acme-grocers-2" if taken).
     */
    public static function generateUniqueSlug(string $companyName): string
    {
        $base = Str::slug(Str::limit($companyName, 40, ''));
        if ($base === '') {
            $base = 'store';
        }
        $slug = $base;
        $counter = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }
        return $slug;
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
