<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Permission;

class ParseArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'project',
        'categorie',
        'tags',
        'img',
        'author',
        'likes',
        'views',
        'desc',
        'meta-tag-img',
        'meta-tags',
    ];

    /**
     * Get all of the comments for the ParseArticle
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->belongsTo(Permission::class);
    }
}
