<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// KBIHU is no longer presented on the public website (user request, 22 October 2026).
// Existing databases keep the record as a draft so it can be restored from the admin panel.
// Founding year is confirmed as 1992 and the kitab count is shown as "30+".
return new class extends Migration {
    public function up(): void
    {
        DB::table('content_pages')->where('slug', 'kbihu')->where('type', 'unit')->update(['status' => 'draft', 'updated_at' => now()]);

        foreach (DB::table('content_pages')->whereIn('slug', ['tentang', 'pesantren'])->get(['id', 'summary', 'body']) as $page) {
            DB::table('content_pages')->where('id', $page->id)->update([
                'summary' => $this->revise((string) $page->summary),
                'body' => $this->revise((string) $page->body),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('content_pages')->where('slug', 'kbihu')->where('type', 'unit')->update(['status' => 'published', 'updated_at' => now()]);
    }

    private function revise(string $text): string
    {
        return strtr($text, [
            'sejak 1994' => 'sejak 1992',
            'tahun 1994' => 'tahun 1992',
            '31 kitab kuning' => '30+ kitab kuning',
            ', pondok pesantren, dan KBIHU.' => ', dan pondok pesantren.',
            ', serta KBIHU Ibnu Aqil.' => ' dalam satu lingkungan.',
        ]);
    }
};
