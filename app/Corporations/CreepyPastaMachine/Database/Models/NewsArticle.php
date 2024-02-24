<?php

namespace App\Corporations\CreepyPastaMachine\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class NewsArticle extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = "CPM_news_articles";
    protected $fillable = [
        'title',
        'link',
        'content',
        'description'
    ];

}
