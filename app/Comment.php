<?php

namespace App;

use App\Traits\Taggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Support\Facades\Cache;

class Comment extends Model
{
    use SoftDeletes, Taggable;

    protected $fillable = ['user_id', 'content'];




    // These fields will be ignored when serializing the model in JSON for REST APIs
    protected $hidden = [
        'deleted_at', 'commentable_type', 'commentable_id', 'user_id'
    ];

    public function commentable()
    {
        return $this->morphTo();
    }

    // blog_post_id
    // public function blogPost()
    // {
    //     // return $this->belongsTo('App\BlogPost', 'post_id', 'blog_post_id');
    //     return $this->belongsTo('App\BlogPost');
    // }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    // public function tags()
    // {
    //     return $this->morphToMany('App\Tag', 'taggable')->withTimestamps();
    // }

    public function scopeLatest(Builder $query)
    {
        return $query->orderBy(static::CREATED_AT, 'desc');
    }

    // public static function boot()
    // {
    //     parent::boot();

    //     // static::addGlobalScope(new LatestScope);

    //     // static::creating(function (Comment $comment) {


    //     //     if ($comment->commentable_type === BlogPost::class) {
    //     //         // Cache::tags(['blog-post'])->forget("blog-post-{$comment->blog_post_id}");
    //     //         Cache::tags(['blog-post'])->forget("blog-post-{$comment->commentable_id}");

    //     //         Cache::tags(['blog-post'])->forget("mostCommented");
    //     //     }
    //     // });
    // }
}
