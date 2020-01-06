<?php

namespace App\Observers;

use App\Comment;
use App\BlogPost;
use Illuminate\Support\Facades\Cache;

class CommentObserver
{

    public function creating(Comment $comment)
    {


        if ($comment->commentable_type === BlogPost::class) {
            // Cache::tags(['blog-post'])->forget("blog-post-{$comment->blog_post_id}");
            Cache::tags(['blog-post'])->forget("blog-post-{$comment->commentable_id}");

            Cache::tags(['blog-post'])->forget("mostCommented");
        }
    }
    // /**
    //  * Handle the comment "created" event.
    //  *
    //  * @param  \App\Comment  $comment
    //  * @return void
    //  */
    // public function created(Comment $comment)
    // {
    //     //
    // }

    // /**
    //  * Handle the comment "updated" event.
    //  *
    //  * @param  \App\Comment  $comment
    //  * @return void
    //  */
    // public function updated(Comment $comment)
    // {
    //     //
    // }

    // /**
    //  * Handle the comment "deleted" event.
    //  *
    //  * @param  \App\Comment  $comment
    //  * @return void
    //  */
    // public function deleted(Comment $comment)
    // {
    //     //
    // }

    // /**
    //  * Handle the comment "restored" event.
    //  *
    //  * @param  \App\Comment  $comment
    //  * @return void
    //  */
    // public function restored(Comment $comment)
    // {
    //     //
    // }

    // /**
    //  * Handle the comment "force deleted" event.
    //  *
    //  * @param  \App\Comment  $comment
    //  * @return void
    //  */
    // public function forceDeleted(Comment $comment)
    // {
    //     //
    // }
}
