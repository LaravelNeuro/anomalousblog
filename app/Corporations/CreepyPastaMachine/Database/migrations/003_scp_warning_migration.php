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
        Schema::create('CPM_scp_warnings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('blog_id');
            $table->foreign('blog_id')->references('id')->on('CPM_blog_articles');   
            $table->string('lang')->default('en');
            $table->string('containment')->nullable();
            $table->string('clearance')->nullable();
            $table->string('risk')->nullable();
            $table->string('threat')->nullable();
            $table->string('disruption')->nullable();
            $table->string('vo_file')->nullable();
            $table->text('assessment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scp_warnings');
    }
};
