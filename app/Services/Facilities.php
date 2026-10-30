<?php
namespace App\Services;

// Facility and student-activity photos for the "Fasilitas & Kegiatan" page. Missing files are skipped.
class Facilities
{
    public const GROUPS = [
        ['mosque', 'Ibadah', 'fasilitas', [
            ['masjid-salapan-bentang', 'Masjid Salapan Bentang'],
            ['masjid-3-lantai', 'Masjid Salapan Bentang 3 lantai'],
            ['tempat-wudlu', 'Tempat wudlu'],
        ]],
        ['school', 'Gedung & lingkungan sekolah', 'fasilitas', [
            ['tulisan-ibnu-aqil', 'Papan nama Ibnu Aqil'],
            ['papan-nama', 'Ibnu Aqil Education Center'],
            ['gedung-depan', 'Gedung depan sekolah'],
            ['lapangan-sekolah', 'Lapangan sekolah'],
            ['atrium', 'Atrium gedung sekolah'],
            ['gedung-sekolah', 'Kawasan sekolah'],
            ['parkiran', 'Parkiran luas'],
            ['gedung-belakang', 'Gedung belakang'],
        ]],
        ['book', 'Belajar', 'fasilitas', [
            ['lab-komputer', 'Laboratorium komputer'],
            ['lab-komputer-2', 'Lab komputer'],
            ['ruang-kelas', 'Ruang kelas'],
            ['lorong-kelas', 'Lorong kelas'],
            ['lorong-kelas-mi', 'Lorong kelas MI'],
            ['mi-ibnu-aqil', 'MI Ibnu Aqil'],
            ['kelas-maqbaroh', 'Kelas Maqbaroh'],
            ['lorong-sekolah', 'Lorong sekolah'],
        ]],
        ['ball', 'Olahraga & prestasi', 'fasilitas', [
            ['lapangan-semi-indoor', 'Lapangan semi-indoor'],
            ['lapangan-olahraga', 'Lapangan olahraga beratap'],
            ['lemari-prestasi', 'Lemari piala prestasi'],
            ['lemari-prestasi-2', 'Piala-piala siswa'],
        ]],
        ['bed', 'Asrama (pilihan untuk siswa SMP & SMA, tidak wajib)', 'fasilitas', [
            ['asrama-putra', 'Kawasan asrama santri putra'],
            ['lapangan-asrama-putra', 'Lapangan asrama putra'],
            ['asrama-lapangan', 'Asrama & lapangan'],
            ['asrama-lantai-2', 'Asrama lantai 2'],
            ['asrama-santri', 'Gedung asrama'],
            ['asrama-putri', 'Asrama santri putri'],
            ['serambi-asrama-putri', 'Serambi asrama putri'],
            ['lorong-pesantren', 'Lorong pesantren'],
            ['lorong-asrama', 'Lorong asrama'],
            ['lorong-asrama-lantai-2', 'Lorong asrama lantai 2'],
            ['wc-asrama-putra', 'WC asrama putra'],
        ]],
        ['shield', 'Keamanan, kebersihan & layanan', 'fasilitas', [
            ['security', 'Security 24 jam'],
            ['pos-keamanan', 'Pos keamanan'],
            ['pusat-informasi', 'Pusat informasi & pelayanan'],
            ['wisma-tamu', 'Ruang tamu & penerimaan'],
            ['wc-umum', 'WC umum'],
            ['kamar-mandi', 'Kamar mandi bersih'],
        ]],
    ];

    public const ACTIVITIES = [
        ['star', 'Kegiatan MI Ibnu Aqil', [
            ['mi-market-day', 'Market Day'],
            ['mi-mahkota', 'Kreasi kostum Market Day'],
            ['mi-food-festival', 'Food festival & bazar'],
            ['mi-pentas-seni', 'Pentas seni'],
            ['mi-pramuka', 'Shalat Dhuha bersama'],
            ['mi-olahraga', 'Olahraga bersama'],
            ['mi-belajar', 'Belajar bersama'],
            ['mi-koridor', 'Belajar kelompok'],
            ['mi-penyambutan', 'Penyambutan siswa baru'],
            ['mi-market-day-2', 'Kelompok Market Day'],
        ]],
        ['people', 'Kegiatan siswa & santri', [
            ['mengaji-bersama', 'Mengaji bersama'],
            ['lalaran', 'Lalaran kitab santri'],
            ['hadroh', 'Latihan hadroh'],
            ['mc-santri', 'Belajar jadi MC'],
            ['math-club', 'Math Club'],
            ['bulu-tangkis', 'Bulu tangkis'],
            ['olahraga-lapangan', 'Olahraga di lapangan'],
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
