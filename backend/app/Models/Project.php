<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Project extends Model
{
    use HasFactory;

    public const AVAILABLE_LOCALES = ['en', 'ru'];
    public const DEFAULT_LOCALE = 'en';

    protected $fillable = [
        'slug',
        'title_translations',
        'summary_translations',
        'preview_image_path',
        'gallery_images',
        'repo_owner',
        'repo_name',
        'default_branch',
        'tags',
        'is_featured',
        'is_published',
        'sort_order',
        'readme_html',
        'languages',
        'last_commit_at',
        'synced_at',
        'github_meta',
    ];

    protected $casts = [
        'tags' => 'array',
        'title_translations' => 'array',
        'summary_translations' => 'array',
        'gallery_images' => 'array',
        'languages' => 'array',
        'github_meta' => 'array',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'last_commit_at' => 'datetime',
        'synced_at' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function getRepositoryAttribute(): string
    {
        return $this->repo_owner . '/' . $this->repo_name;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getTranslation(string $field, ?string $locale = null, bool $fallback = true): ?string
    {
        $locale = $locale && in_array($locale, self::AVAILABLE_LOCALES, true)
            ? $locale
            : app()->getLocale();

        if (! in_array($locale, self::AVAILABLE_LOCALES, true)) {
            $locale = self::DEFAULT_LOCALE;
        }

        $translations = (array) $this->getAttribute($field . '_translations');
        $value = Arr::get($translations, $locale);

        if ($value !== null && $value !== '') {
            return $value;
        }

        if ($fallback) {
            return Arr::first(array_filter($translations, fn ($item) => $item !== null && $item !== ''));
        }

        return null;
    }

    public function setTranslation(string $field, string $locale, ?string $value): self
    {
        if (! in_array($locale, self::AVAILABLE_LOCALES, true)) {
            return $this;
        }

        $translations = (array) $this->getAttribute($field . '_translations');

        if ($value === null || $value === '') {
            unset($translations[$locale]);
        } else {
            $translations[$locale] = $value;
        }

        $this->setAttribute($field . '_translations', array_filter($translations, fn ($item) => $item !== null && $item !== ''));

        return $this;
    }
}
