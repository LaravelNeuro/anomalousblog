<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('CPM_blog_articles', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('project_id');
                $table->foreign('project_id')->references('id')->on('laravel_neuro_network_projects');
            $table->unsignedBigInteger('original');
                $table->foreign('original')->references('id')->on('CPM_news_articles');    
            $table->boolean('published')->default(true);
            $table->text("articleENraw")->nullable();
            $table->text("articleDEraw")->nullable();
            $table->text("articleEN")->nullable();
            $table->text("articleDE")->nullable();
            $table->text("articleENvo")->nullable();
            $table->text("articleDEvo")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_articles');
    }
};
