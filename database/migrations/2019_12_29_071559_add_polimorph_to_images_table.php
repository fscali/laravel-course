<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPolimorphToImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('images', function (Blueprint $table) {
            //
            $table->dropColumn('blog_post_id');

            // $table->unsignedInteger('imageable_id');
            // $table->string('imageable_type');
            $table->morphs('imageable'); //it creates the two columns and one index to run the polymorphic faster
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->unsignedInteger('blog_post_id')->nullable();
            $table->dropMorphs('imageable');
        });
    }
}
