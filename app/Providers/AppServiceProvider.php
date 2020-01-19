<?php

namespace App\Providers;

use App\BlogPost;
use App\Comment;
use App\Http\ViewComposers\ActivityComposer;
use App\Observers\BlogPostObserver;
use App\Observers\CommentObserver;
use App\Services\Counter;
use App\Services\DummyCounter;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use App\Http\Resources\Comment as CommentResource;
use Illuminate\Http\Resources\Json\Resource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191); //not to run in problems with key length (see polimorph images table)
        Blade::component('components.badge', 'badge');
        Blade::component('components.updated', 'updated');
        Blade::component('components.card', 'card');
        Blade::component('components.tags', 'tags');
        Blade::component('components.errors', 'errors');
        Blade::component('components.comment-form', 'commentForm');
        Blade::component('components.comment-list', 'commentList');




        // to have data available in every view
        // view()->composer('*', ActivityComposer::class); 

        view()->composer(['posts.index', 'posts.show'], ActivityComposer::class); //the array contains  the views where we need the composer

        BlogPost::observe(BlogPostObserver::class);
        Comment::observe(CommentObserver::class);
        $this->app->singleton(Counter::class, function ($app) {
            return new Counter(
                $app->make('Illuminate\Contracts\Cache\Factory'),
                $app->make('Illuminate\Contracts\Session\Session'),


                env('COUNTER_TIMEOUT', 5)
            );
        });

        $this->app->bind(
            'App\Contracts\CounterContract',
            Counter::class
        );

        //it doesn't wrap it anymore in a "data" object
        // CommentResource::withoutWrapping();

        Resource::withoutWrapping();

        // $this->app->when(Counter::class)
        // ->needs('$timeout')
        // ->give(env('COUNTER_TIMEOUT'));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
