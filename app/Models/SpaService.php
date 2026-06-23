<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SpaService extends Model
{
    protected $fillable = [
        'name', 'slug', 'price', 'description', 'image', 'icon', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'price' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function priceLabel(): string
    {
        return '₦'.number_format($this->price);
    }

    public function imageUrl(): ?string
    {
        return $this->resolve($this->image);
    }

    public function iconUrl(): ?string
    {
        return $this->resolve($this->icon);
    }

    protected function resolve(?string $path): ?string
    {
        if (! $path) {
            return null;
        }
        if (str_starts_with($path, 'images/')) {
            return str_replace(' ', '%20', asset($path));
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}
