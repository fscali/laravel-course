<?php

namespace App\Http\Controllers;

use App\BlogPost;
use App\Events\BlogPostPosted;
use App\Http\Requests\StorePost;
use App\Image;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

// use Illuminate\Support\Facades\DB;

// [
//     'show' => 'view',
//     'create' => 'create',
//     'store' => 'create',
//     'edit' => 'update',
//     'update' => 'update',
//     'destroy' => 'delete',
// ]
class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')
            ->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // DB::connection()->enableQueryLog();

        // $posts = BlogPost::with('comments')->get();

        // foreach ($posts as $post) {
        //     foreach ($post->comments as $comment) {
        //         echo $comment->content;
        //     }
        // }

        // dd(DB::getQueryLog());

        // comments_count

        // $mostCommented  = Cache::tags(['blog-post'])->remember('blog-post-commented', 60, function () {
        //     return  BlogPost::mostCommented()->take(5)->get();
        // });

        // $mostActive  = Cache::tags(['blog-post'])->remember('users-most-active', 60, function () {
        //     return  User::withMostBlogPosts()->take(5)->get();
        // });

        // $mostActiveLastMonth  = Cache::tags(['blog-post'])->remember('users-most-active-last-month', 60, function () {
        //     return  User::withMostBlogPostsLastMonth()->take(5)->get();
        // });

        return view(
            'posts.index',
            [
                'posts' => BlogPost::latestWithRelations()->get(),
                // 'mostCommented' => BlogPost::mostCommented()->take(5)->get(),
                // 'mostCommented' => $mostCommented,
                // 'mostActive' => $mostActive,
                // 'mostActiveLastMonth' => $mostActiveLastMonth
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // return view('posts.show', [
        //     'post' => BlogPost::with(['comments' => function ($query) {
        //         return $query->latest();
        //     }])->findOrFail($id),
        // ]);
        $blogPost = Cache::tags(['blog-post'])->remember("blog-post-{$id}", 60, function () use ($id) {
            return BlogPost::with('comments', 'tags', 'user', 'comments.user')
                // ->with('tags')
                // ->with('user')
                // ->with('comments.user') //we fetch not only comments but also the user who commented
                ->findOrFail($id);
        });

        $sessionId = session()->getId();
        $counterKey = "blog-post-{$id}-counter";
        $usersKey = "blog-post-{$id}-users";

        $users = Cache::tags(['blog-post'])->get($usersKey, []);
        $usersUpdate = [];
        $difference = 0;
        $now = now();

        foreach ($users as $session => $lastVisit) {
            if ($now->diffInMinutes($lastVisit) >= 1) {
                $difference--;
            } else {
                $usersUpdate[$session] = $lastVisit;
            }
        }

        if (
            !array_key_exists($sessionId, $users)

            || $now->diffInMinutes($users[$sessionId]) >= 1
        ) {
            $difference++;
        }

        $usersUpdate[$sessionId] = $now;

        Cache::tags(['blog-post'])->forever($usersKey, $usersUpdate);
        if (!Cache::tags(['blog-post'])->has($counterKey)) {
            Cache::tags(['blog-post'])->forever($counterKey, 1);
        } else {
            Cache::tags(['blog-post'])->increment($counterKey, $difference);
        }


        $counter = Cache::tags(['blog-post'])->get($counterKey);
        return view('posts.show', [
            'post' => $blogPost,
            'counter' => $counter
        ]);
    }

    public function create()
    {
        // $this->authorize('posts.create');
        return view('posts.create');
    }

    public function store(StorePost $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = $request->user()->id;
        $blogPost = BlogPost::create($validatedData);

        // dump($request->hasFile('thumbnail'));
        $hasFile = $request->hasFile('thumbnail');
        if ($hasFile) {
            $path = $request->file('thumbnail')->store('thumbnails');
            $blogPost->image()->save(
                //Image::create(['path' => $path])
                Image::make(['path' => $path]) //so the polymorphic attributes are handled automatically by laravel

            );
            // dump($file);
            // dump($file->getClientMimeType());
            // dump($file->getClientOriginalExtension());


            // $fileName = $file->store('thumbnails'); // shortcut for using the storage facade
            //  dump($fileName);  //unique random name, for example: thumbnails/TiLQueks6Gigr7oIRybaLgMd2nhwVemyesIv52Zn.pdf
            // Storage::disk('public')->put('thumbnails', $file);


            // $name1 =  $file->storeAs('thumbnails', $blogPost->id . "." . $file->guessExtension());
            // dump(Storage::url($name1));
        };
        // die;
        event(new BlogPostPosted($blogPost));

        $request->session()->flash('status', 'Blog post was created!');

        return redirect()->route('posts.show', ['post' => $blogPost->id]);
    }

    public function edit($id)
    {
        $post = BlogPost::findOrFail($id);
        $this->authorize($post);

        return view('posts.edit', ['post' => $post]);
    }

    public function update(StorePost $request, $id)
    {
        $post = BlogPost::findOrFail($id);


        // if (Gate::denies('update-post', $post)) {
        //     abort(403, "You can't edit this blog post!");
        // }
        $this->authorize($post);

        $validatedData = $request->validated();
        $hasFile = $request->hasFile('thumbnail');
        if ($hasFile) {
            $path = $request->file('thumbnail')->store('thumbnails');

            if ($post->image) {
                Storage::delete($post->image->path);
                $post->image->path = $path;
                $post->image->save();
            } else {

                $post->image()->save(
                    // Image::create(['path' => $path])
                    Image::make(['path' => $path])
                );
            }
        }

        $post->fill($validatedData);
        $post->save();
        $request->session()->flash('status', 'Blog post was updated!');

        return redirect()->route('posts.show', ['post' => $post->id]);
    }

    public function destroy(Request $request, $id)
    {
        $post = BlogPost::findOrFail($id);

        // if (Gate::denies('delete-post', $post)) {
        //     abort(403, "You can't delete this blog post!");
        // }
        $this->authorize($post);

        $post->delete();

        // BlogPost::destroy($id);

        $request->session()->flash('status', 'Blog post was deleted!');

        return redirect()->route('posts.index');
    }
}
