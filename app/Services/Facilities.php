<?php
namespace App\Services;

// Facility and student-activity photos for the "Fasilitas & Kegiatan" page. Missing files are skipped.
class Facilities
{
    public const GROUPS = [
        ['mosque', 'Ibadah', 'fasilitas', [
            ['masjid-salapan-bentang', 'Masjid Salapan Bentang'],
            ['tempat-wudhu', 'Tempat wudhu'],
        ]],
        ['school', 'Gedung & lingkungan sekolah', 'fasilitas', [
            ['papan-nama', 'Ibnu Aqil Education Center'],
            ['gedung-depan', 'Gedung depan sekolah'],
            ['gedung-kolam', 'Gedung sekolah'],
            ['gedung-sekolah', 'Kawasan sekolah'],
            ['gedung-panjang', 'Selasar gedung'],
            ['gedung-baru', 'Gedung kelas'],
            ['gedung-belakang', 'Gedung belakang'],
        ]],
        ['book', 'Belajar', 'fasilitas', [
            ['lab-komputer', 'Laboratorium komputer'],
            ['lab-komputer-2', 'Lab komputer'],
            ['ruang-kelas', 'Ruang kelas'],
            ['mi-ibnu-aqil', 'MI Ibnu Aqil'],
            ['koridor-kelas', 'Koridor kelas'],
            ['lorong-sekolah', 'Lorong sekolah'],
            ['aula', 'Aula'],
        ]],
        ['bed', 'Asrama (ayo mondok, untuk SMP & SMA)', 'fasilitas', [
            ['asrama-putra', 'Kawasan asrama santri putra'],
            ['kawasan-asrama', 'Halaman asrama'],
            ['asrama-lapangan', 'Asrama & lapangan'],
            ['asrama-putri', 'Asrama santri putri'],
            ['asrama-santri', 'Gedung asrama'],
            ['lorong-asrama', 'Lorong asrama'],
            ['lorong-asrama-2', 'Selasar asrama'],
        ]],
        ['shield', 'Keamanan & layanan', 'fasilitas', [
            ['security', 'Security 24 jam'],
            ['pos-keamanan', 'Pos keamanan'],
            ['pusat-informasi', 'Pusat informasi & pelayanan'],
            ['wisma-tamu', 'Ruang tamu & penerimaan'],
        ]],
    ];

    public const ACTIVITIES = [
        ['star', 'Kegiatan MI Ibnu Aqil', [
            ['mi-market-day', 'Market Day'],
            ['mi-mahkota', 'Kreasi kostum Market Day'],
            ['mi-food-festival', 'Food festival & bazar'],
            ['mi-pentas-seni', 'Pentas seni'],
            ['mi-pramuka', 'Pramuka di aula'],
            ['mi-olahraga', 'Olahraga bersama'],
            ['mi-belajar', 'Belajar bersama'],
            ['mi-koridor', 'Belajar kelompok'],
            ['mi-penyambutan', 'Penyambutan siswa baru'],
            ['mi-market-day-2', 'Kelompok Market Day'],
        ]],
        ['people', 'Kegiatan siswa & santri', [
            ['lalaran', 'Lalaran kitab santri'],
            ['hadroh', 'Latihan hadroh'],
            ['mc-santri', 'Belajar jadi MC'],
            ['math-club', 'Math Club'],
            ['futsal', 'Futsal'],
            ['karate', 'Karate'],
        ]],
    ];

    public static function groups(): array
    {
        $out = [];
        foreach (self::GROUPS as [$icon, $title, $dir, $items]) {
            if ($photos = self::photos($dir, $items)) {
                $out[] = ['icon' => $icon, 'title' => $title, 'photos' => $photos];
            }
        }

        return $out;
    }

    public static function activities(): array
    {
        $out = [];
        foreach (self::ACTIVITIES as [$icon, $title, $items]) {
            if ($photos = self::photos('kegiatan-siswa', $items)) {
                $out[] = ['icon' => $icon, 'title' => $title, 'photos' => $photos];
            }
        }

        return $out;
    }

    private static function photos(string $dir, array $items): array
    {
        $photos = [];
        foreach ($items as [$file, $caption]) {
            $rel = 'assets/foto/'.$dir.'/'.$file.'.webp';
            $path = public_path($rel);
            if (is_file($path) && !is_link($path) && ($size = @getimagesize($path))) {
                $photos[] = ['src' => asset($rel), 'caption' => $caption, 'width' => $size[0], 'height' => $size[1]];
            }
        }

        return $photos;
    }
}
