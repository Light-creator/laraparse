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
            $table->string('project')->nullable();
            $table->string('categorie')->nullable();
            $table->string('tags')->nullable();
            $table->string('img')->nullable();
            $table->string('author')->nullable();
            $table->integer('likes')->nullable();
            $table->integer('views')->nullable();
            $table->longText('desc')->nullable();
            $table->longText('meta-tag-img')->nullable();
            $table->longText('meta-tags')->nullable();
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
