<?php
namespace App\Services;

// Facility photos grouped for the Fasilitas page. Files live in public/assets/foto/fasilitas; missing files are skipped.
class Facilities
{
    public const GROUPS = [
        ['mosque', 'Ibadah', [
            ['masjid-depan', 'Masjid Salapan Bentang (Cahaya Ilahi)'],
            ['masjid-dalam', 'Ruang utama masjid'],
            ['masjid-mihrab', 'Mihrab masjid'],
            ['serambi-masjid', 'Mengaji di serambi masjid'],
            ['manasik', 'Area praktik manasik haji'],
        ]],
        ['school', 'Belajar', [
            ['gedung-sekolah', 'Gedung sekolah'],
            ['gedung-lapangan', 'Kawasan sekolah'],
            ['kelas-mi', 'Ruang kelas MI'],
            ['lab-komputer', 'Laboratorium komputer'],
            ['lab-komputer-praktik', 'Praktik di lab komputer'],
            ['kelas-tik', 'Pembelajaran TIK'],
            ['apotek-simulasi', 'Apotek simulasi (Farmasi SMK)'],
            ['perpustakaan', 'Perpustakaan'],
        ]],
        ['ball', 'Olahraga', [
            ['semi-indoor-luas', 'Lapangan semi indoor'],
            ['semi-indoor', 'Lapangan serbaguna beratap'],
            ['bulutangkis', 'Lapangan bulu tangkis'],
            ['lapangan-rumput', 'Lapangan terbuka'],
        ]],
        ['bed', 'Asrama (pilihan untuk SMP & SMA)', [
            ['asrama-putra', 'Kawasan asrama santri putra'],
            ['asrama-kolam', 'Gedung asrama'],
            ['lorong-asrama', 'Lorong asrama'],
            ['kamar-mandi', 'Kamar mandi'],
        ]],
        ['shield', 'Layanan & penunjang', [
            ['gerbang-ibnu-aqil', 'Gerbang Ibnu Aqil'],
            ['gedung-depan', 'Gedung layanan depan'],
            ['pusat-informasi', 'Pusat informasi & pelayanan'],
            ['kantor-administrasi', 'Kantor administrasi'],
            ['koperasi', 'Koperasi siswa'],
            ['lemari-piala', 'Lemari piala prestasi'],
            ['pos-keamanan', 'Pos keamanan'],
        ]],
    ];

    public static function groups(): array
    {
        $out = [];
        foreach (self::GROUPS as [$icon, $title, $items]) {
            $photos = [];
            foreach ($items as [$file, $caption]) {
                $rel = 'assets/foto/fasilitas/'.$file.'.webp';
                $path = public_path($rel);
                if (is_file($path) && !is_link($path) && ($size = @getimagesize($path))) {
                    $photos[] = ['src' => asset($rel), 'caption' => $caption, 'width' => $size[0], 'height' => $size[1]];
                }
            }
            if ($photos !== []) {
                $out[] = ['icon' => $icon, 'title' => $title, 'photos' => $photos];
            }
        }

        return $out;
    }
}
