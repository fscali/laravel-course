<?php

namespace Tests;

use App\BlogPost;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\User;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function user()
    {
        return factory(User::class)->create();
    }

    protected function newPost()
    {
        return factory(BlogPost::class)->create([
            'user_id' => $this->user()->id
        ]);
    }
}
