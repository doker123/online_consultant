<?php

namespace Model;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{

    protected $table = 'pages';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'is_published',
        'created_at',
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($page) {
            $page->created_at = date('Y-m-d H:i:s');
        });

        static::updating(function ($page) {
            $page->updated_at = date('Y-m-d H:i:s');
        });
    }

    public static function generateSlug(string $title): string
    {
        $translit = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        ];

        $slug = mb_strtolower(trim($title));
        $slug = strtr($slug, $translit);
        $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        if (empty($slug)) {
            $slug = 'page-' . time();
        }

        return $slug;
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', 1);
    }
}
