<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->json('title_translations')->nullable()->after('title');
            $table->json('summary_translations')->nullable()->after('summary');
        });

        DB::table('projects')->select('id', 'title', 'summary')->orderBy('id')->chunkById(100, function ($projects) {
            foreach ($projects as $project) {
                DB::table('projects')
                    ->where('id', $project->id)
                    ->update([
                        'title_translations' => json_encode(['en' => $project->title]),
                        'summary_translations' => $project->summary ? json_encode(['en' => $project->summary]) : null,
                    ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['title_translations', 'summary_translations']);
        });
    }
};
