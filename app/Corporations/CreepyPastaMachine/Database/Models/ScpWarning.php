<?php

namespace App\Corporations\CreepyPastaMachine\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

use App\Corporations\CreepyPastaMachine\Database\Models\BlogArticle;

class ScpWarning extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = "CPM_scp_warnings";
    protected $fillable = [
        'blog_id',
        'lang',
        'containment',
        'clearance',
        'risk',
        'threat',
        'assessment',
        'disruption',
        'vo_file'
    ];
    
    public function project() : BelongsTo
    {
        return $this->belongsTo(BlogArticle::class, 'blog_id');
    }

    
}