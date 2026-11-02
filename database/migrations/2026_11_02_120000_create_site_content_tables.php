<?php

use App\Support\ContentCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_pages', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('h1');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('button_label')->nullable();
            $table->string('button_url')->nullable();
            $table->string('works_title');
            $table->json('works');
            $table->string('who_title');
            $table->text('who_text');
            $table->text('who_extra')->nullable();
            $table->string('directions_title');
            $table->string('cta_title')->nullable();
            $table->text('cta_text')->nullable();
            $table->string('cta_button_label')->nullable();
            $table->string('cta_button_url')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('key')->nullable()->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->string('h1');
            $table->text('lead')->nullable();
            $table->json('blocks');
            $table->string('card_link_label')->nullable();
            $table->unsignedInteger('sort')->default(0)->index();
            $table->boolean('is_published')->default(true);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
        });

        Schema::create('site_options', function (Blueprint $table) {
            $table->id();
            $table->string('logo_text');
            $table->string('logo_url')->nullable();
            $table->string('header_button_label')->nullable();
            $table->string('header_button_url')->nullable();
            $table->text('footer_lead')->nullable();
            $table->json('footer_links');
            $table->string('copyright')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->text('requisites')->nullable();
            $table->text('contact_extra')->nullable();
            $table->string('contacts_title')->nullable();
            $table->string('contacts_h1')->nullable();
            $table->text('contacts_lead')->nullable();
            $table->string('contacts_seo_title')->nullable();
            $table->text('contacts_seo_description')->nullable();
            $table->string('site_name')->nullable();
            $table->string('default_seo_title')->nullable();
            $table->text('default_seo_description')->nullable();
            $table->timestamps();
        });

        ContentCatalog::seed();
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
        Schema::dropIfExists('home_pages');
        Schema::dropIfExists('site_options');
    }
};
