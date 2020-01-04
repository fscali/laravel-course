<?php

namespace App\Http\Controllers;

use App\BlogPost;
use App\Http\Requests\StoreComment;
use App\Jobs\NotifyUsersPostWasCommented;
use App\Mail\CommentPosted;
use App\Mail\CommentPostedMarkdown;
use Illuminate\Support\Facades\Mail;

class PostCommentController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth')->only(['store']);
    }
    //binding: it will call findOrFail automatically for the post
    public function store(BlogPost $post, StoreComment $request)
    {
        $comment =  $post->comments()->create([
            'content' => $request->input('content'),
            'user_id' => $request->user()->id
        ]);


        //note: better doing it in events, see later lecture
        //note: laravel recognizes automatically the email from the user
        // Mail::to($post->user)->send(new CommentPostedMarkdown($comment));


        //$when = now()->addMinutes(1);
        Mail::to($post->user)->queue(new CommentPostedMarkdown($comment));
        NotifyUsersPostWasCommented::dispatch($comment);

        //Mail::to($post->user)->later($when, new CommentPostedMarkdown($comment));


        // $request->session()->flash('status', 'Comment  was created!');
        return redirect()->back()->withStatus('Comment was created!');
    }
}
