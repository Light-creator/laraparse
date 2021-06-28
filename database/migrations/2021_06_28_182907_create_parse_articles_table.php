<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParseArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parse_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('url');
            $table->string('project');
            $table->string('categorie');
            $table->string('tags');
            $table->string('img');
            $table->string('author');
            $table->integer('likes');
            $table->integer('views');
            $table->longText('desc');
            $table->longText('meta-tag-img');
            $table->longText('meta-tags');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parse_articles');
    }
}
