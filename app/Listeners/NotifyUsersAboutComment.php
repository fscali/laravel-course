<?php

namespace App\Listeners;

use App\Events\CommentPosted;
use App\Jobs\NotifyUsersPostWasCommented;
use App\Jobs\ThrottledMail;
use App\Mail\CommentPostedMarkdown;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyUsersAboutComment
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(CommentPosted $event)
    {

        $comment = $event->comment;
        ThrottledMail::dispatch(
            new CommentPostedMarkdown($comment),
            $comment->commentable->user
        )
            ->onQueue('high');
        NotifyUsersPostWasCommented::dispatch($comment)
            ->onQueue('low');
    }
}
