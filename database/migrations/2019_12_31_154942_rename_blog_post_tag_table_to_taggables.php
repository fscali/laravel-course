<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameBlogPostTagTableToTaggables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blog_post_tag', function (Blueprint $table) {
            //
            $table->dropForeign(['blog_post_id']);
            $table->dropColumn('blog_post_id');
        });

        Schema::rename('blog_post_tag', 'taggables'); // it's the name Laravel will figure out the name of the pivot table
        Schema::table('taggables', function (Blueprint $table) {
            $table->morphs('taggable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taggables', function (Blueprint $table) {
            $table->dropMorphs('taggable');
        });
        Schema::rename('taggables', 'blog_post_tag'); // it's the name Laravel will figure out the name of the pivot table

        Schema::disableForeignKeyConstraints();

        Schema::table('blog_post_tag', function (Blueprint $table) {
            $table->unsignedInteger('blog_post_id')->index();
            $table->foreign('blog_post_id')->references('id')
                ->on('blog_posts')
                ->onDelete('cascade');
        });

        Schema::enableForeignKeyConstraints();
    }
}
