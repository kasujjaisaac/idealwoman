<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 20)->default('user');
        });
        Schema::create('content_sections', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('kind', 20)->default('page')->index();
            $table->string('label');
            $table->json('draft');
            $table->json('published')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
        });
        Schema::create('content_revisions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('content_section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 40);
            $table->json('snapshot');
            $table->timestamps();
        });
        Schema::create('enquiries', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('email');
            $table->string('organisation', 180)->nullable();
            $table->string('enquiry_type');
            $table->text('message');
            $table->string('status', 20)->default('new')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiries');
        Schema::dropIfExists('content_revisions');
        Schema::dropIfExists('content_sections');
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('role');
        });
    }
};
