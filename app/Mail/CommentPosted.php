<?php

namespace App\Mail;

use App\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;


class CommentPosted extends Mailable
{
    use Queueable, SerializesModels;

    public $comment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "Comment was posted on your {$this->comment->commentable->title} blog post";
        return $this
            // ->attach(
            //     storage_path('app/public') . '/' . $this->comment->user->image->path,
            //     [
            //         'as' => 'profile_picture.png',
            //         'mime'  => 'image/png'
            //     ]
            // )
            // ->attachFromStorage($this->comment->user->image->path, 'profile_picture.png', ['mime' => 'image/png'])
            // ->attachFromStorageDisk('public', $this->comment->user->image->path, 'profile_picture.png', ['mime' => 'image/png'])
            ->attachData(Storage::get($this->comment->user->image->path), 'profile_picture_from_data.png', ['mime' => 'image/png'])
            ->subject($subject)
            // ->from("pippo@pippo.it", "Pippo di Topolinia") // overrides the default value 
            ->view('emails.posts.commented');
    }
}
