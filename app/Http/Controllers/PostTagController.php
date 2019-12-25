<?php

namespace App\Http\Controllers;

use App\Tag;
use Illuminate\Http\Request;

class PostTagController extends Controller
{
    public function index($tagId)
    {
        $tag = Tag::findOrFail($tagId);
        return view('posts.index', [
            'posts' => $tag->blogPosts()->latestWithRelations()->get()
            // 'mostCommented' => [],
            //  'mostActiveLastMonth' => [],
            //   'mostActive' => []
        ]);
    }
}
