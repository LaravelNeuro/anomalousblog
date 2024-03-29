<?php

namespace App\Corporations\CreepyPastaMachine\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

use LaravelNeuro\LaravelNeuro\Networking\Database\Models\NetworkProject;
use App\Corporations\CreepyPastaMachine\Database\Models\ScpWarning;

class BlogArticle extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = "CPM_blog_articles";
    protected $fillable = [
        'project_id',
        'original',
        'published',
        'articleENraw',
        'articleDEraw',
        'articleEN',
        'articleDE',
        'articleENvo',
        'articleDEvo'
    ];

    public function project() : BelongsTo
    {
        return $this->belongsTo(NetworkProject::class, 'project_id');
    }

    public function scpEN() : HasOne
    {
        return $this->hasOne(ScpWarning::class, 'blog_id')->where('lang', 'en');
    }

    public function scpDE() : HasOne
    {
        return $this->hasOne(ScpWarning::class, 'blog_id')->where('lang', 'de');
    }

    public function link() : BelongsTo
    {
        return $this->belongsTo(NewsArticle::class, 'original');
    }
}
