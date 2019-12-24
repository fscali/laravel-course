<?php

use App\Tag;
use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $tagCount = (int) $this->command->ask('How many tags would you like?', 10);
        // $posts = App\BlogPost::all();

        // factory(App\Tag::class, $tagCount)->make()->each(function ($tag) use ($posts) {
        //     $tag->save();
        //     $tag->blogPosts()->attach($posts->random(5)->pluck('id'));
        // });
        $tags = collect(['Science', 'Sport', 'Politics', 'Entartainment', 'Economy']);
        $tags->each(function ($tagName) {
            $tag = new Tag();
            $tag->name = $tagName;
            $tag->save();
        });
    }
}
