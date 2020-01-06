<?php

namespace App\Observers;

use App\BlogPost;
use Illuminate\Support\Facades\Cache;

class BlogPostObserver
{
    /**
     * Handle the blog post "created" event.
     *
     * @param  \App\BlogPost  $blogPost
     * @return void
     */
    public function created(BlogPost $blogPost)
    {
        //
    }

    /**
     * Handle the blog post "updated" event.
     *
     * @param  \App\BlogPost  $blogPost
     * @return void
     */
    public function updated(BlogPost $blogPost)
    {
        //
    }

    public function deleting(BlogPost $blogPost)
    {

        $blogPost->comments()->delete();
        Cache::tags(['blog-post'])->forget("blog-post-{$blogPost->id}");
    }

    public function updating(BlogPost $blogPost)
    {

        Cache::tags(['blog-post'])->forget("blog-post-{$blogPost->id}");
    }

    public function restoring(BlogPost $blogPost)
    {
        $blogPost->comments()->restore();
    }

    /**
     * Handle the blog post "deleted" event.
     *
     * @param  \App\BlogPost  $blogPost
     * @return void
     */
    // public function deleted(BlogPost $blogPost)
    // {
    //     //
    // }

    /**
     * Handle the blog post "restored" event.
     *
     * @param  \App\BlogPost  $blogPost
     * @return void
     */
    // public function restored(BlogPost $blogPost)
    // {
    //     //
    // }

    /**
     * Handle the blog post "force deleted" event.
     *
     * @param  \App\BlogPost  $blogPost
     * @return void
     */
    // public function forceDeleted(BlogPost $blogPost)
    // {
    //     //
    // }
}
