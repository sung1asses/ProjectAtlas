<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $columns = array_filter([
            Schema::hasColumn('projects', 'title') ? 'title' : null,
            Schema::hasColumn('projects', 'summary') ? 'summary' : null,
        ]);

        if ($columns === []) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) use ($columns): void {
            $table->dropColumn($columns);
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            if (! Schema::hasColumn('projects', 'title')) {
                $table->string('title')->after('slug');
            }

            if (! Schema::hasColumn('projects', 'summary')) {
                $table->text('summary')->nullable()->after('title_translations');
            }
        });
    }
};
