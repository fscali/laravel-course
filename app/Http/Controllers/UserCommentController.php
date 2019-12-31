<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComment;
use App\User;

class UserCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['store']);
    }
    //binding: it will call findOrFail automatically for the post
    public function store(User $user, StoreComment $request)
    {
        $user->commentsOn()->create([
            'content' => $request->input('content'),
            'user_id' => $request->user()->id
        ]);

        // $request->session()->flash('status', 'Comment  was created!');
        return redirect()->back()
            ->withStatus('Comment was created');
    }
}
