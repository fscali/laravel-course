<?php

namespace Tests\Feature;

use App\BlogPost;
use App\Comment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiPostCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function testNewBlogPostDoesNotHaveComments()
    {
        $this->newPost();
        $response = $this->json('GET', '/api/v1/posts/1/comments');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']) //we check that there are the "data", "links" and "meta" keys
            ->assertJsonCount(0, 'data'); //we check that under the "data" key there are 0 elements
    }

    public function testBlogPostHas10Comments()
    {
        $this->newPost()->each(function (BlogPost $post) {
            $post->comments()->saveMany(
                factory(Comment::class, 10)->make([
                    'user_id' => $this->user()->id
                ])
            );
        });

        $response = $this->json('GET', '/api/v1/posts/2/comments');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'comment_id',
                        'content',
                        'created_at',
                        'updated_at',
                        'user' => [
                            'id',
                            'name'
                        ]
                    ]
                ],
                'links',
                'meta'
            ]) //we check that there are the "data", "links" and "meta" keys
            ->assertJsonCount(10, 'data'); //we check that under the "data" key there are 0 elements
    }

    public function testAddingCommentsWhenNotAuthenticated()
    {
        $this->newPost();
        $response = $this->json('POST', '/api/v1/posts/3/comments', [
            'content' => 'Hello'
        ]);

        $response->assertStatus(401);
    }

    public function testAddingCommentsWhenAuthenticated()
    {
        $this->newPost();
        $response = $this->actingAs($this->user(), 'api')->json('POST', '/api/v1/posts/4/comments', [
            'content' => 'Hello'
        ]);

        $response->assertStatus(201);
    }

    public function testAddingCommentWithInvalidData()
    {
        $this->newPost();
        $response = $this->actingAs($this->user(), 'api')->json('POST', '/api/v1/posts/5/comments', []);

        $response->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors"  => [
                    "content" => [
                        "The content field is required."
                    ]
                ]
            ]);
    }
}
