<?php
namespace App\Services;

// Short, display-ready profile of each unit: brand colour, leader portrait, vision and highlights.
// Long texts stay in the CMS; this keeps the public pages brief for parents who skim.
class Units
{
    public const ORDER = ['mi', 'smp', 'sma', 'smk', 'pesantren'];

    // Ekstrakurikuler → [label, sticker file in assets/ekskul or null]
    public const EKSKUL = [
        'tahfidz' => ["Tahfidz Al-Qur'an", 'tahfidz'],
        'tilawah' => ['Tilawah', 'tilawah'],
        'kaligrafi' => ['Kaligrafi', 'kaligrafi'],
        'hadroh' => ['Marawis & Hadroh', 'hadroh'],
        'paduan-suara' => ['Paduan Suara', 'paduan-suara'],
        'angklung' => ['Angklung & Degung', 'angklung-degung'],
        'drumband' => ['Drumband', 'drumband'],
        'pramuka' => ['Pramuka', 'pramuka'],
        'math-club' => ['Math Club', 'math-club'],
        'sains-club' => ['Sains Club', 'sains-club'],
        'english-club' => ['English Club', 'english-club'],
        'komputer' => ['Komputer', 'komputer'],
        'futsal' => ['Futsal', 'futsal'],
        'handball' => ['Handball', 'handball'],
        'badminton' => ['Bulu Tangkis', 'badminton'],
        'panahan' => ['Panahan', 'panahan'],
        'karate' => ['Karate', 'karate'],
        'silat' => ['Pencak Silat GASMI', 'pencak-silat'],
        'menggambar' => ['Seni & Menggambar', 'menggambar'],
    ];

    public const UNITS = [
        'mi' => [
            'short' => 'MI', 'name' => 'MI Ibnu Aqil', 'color' => 'mi',
            'who' => 'Usia 6–12 tahun', 'line' => 'Belajar ceria, akhlak terbina.',
            'leader' => ['Dr. Hj. Fuzna Nur Aqilah, M.Pd.', 'Kepala Madrasah'],
            'vision' => 'Generasi Qur\'ani yang ceria, berakhlak mulia, dan cinta belajar sejak dini.',
            'highlights' => [['book', 'Baca tulis Al-Qur\'an'], ['star', 'Pembinaan karakter'], ['compass', 'Outing class'], ['mosque', 'Program kepesantrenan']],
            'schedule' => 'Senin–Jumat · 07.25–14.00 WIB',
            'boarding' => null,
            'ekskul' => [],
        ],
        'smp' => [
            'short' => 'SMP', 'name' => 'SMP Ibnu Aqil', 'color' => 'smp',
            'who' => 'Lulusan SD/MI', 'line' => 'Tumbuh berprestasi, kuat mengaji.',
            'leader' => ['Ade Irawan, S.Pd.I.', 'Kepala Sekolah'],
            'vision' => 'Pelajar berakhlak, berprestasi, dan cakap digital dengan jiwa santri.',
            'highlights' => [['medal', 'Terakreditasi A'], ['book', 'BTQ Plus & tahfidz'], ['mosque', 'Dhuha & tadarus setiap hari'], ['check', 'Absensi elektronik']],
            'schedule' => 'Senin–Jumat · 07.15–14.20 WIB',
            'boarding' => 'Bisa sambil mondok (pilihan)',
            'ekskul' => ['pramuka', 'futsal', 'handball', 'badminton', 'panahan', 'hadroh', 'karate', 'silat', 'angklung', 'komputer', 'paduan-suara', 'math-club', 'english-club', 'sains-club', 'kaligrafi', 'tilawah'],
        ],
        'sma' => [
            'short' => 'SMA', 'name' => 'SMA Ibnu Aqil Boarding School', 'color' => 'sma',
            'who' => 'Lulusan SMP/MTs', 'line' => 'Siap kuliah, siap memimpin.',
            'leader' => ['Hidmatullah, S.E., M.Pd.', 'Kepala Sekolah'],
            'vision' => 'Melahirkan pemimpin muda yang berakhlak, unggul akademik, dan siap bersaing di tingkat global.',
            'highlights' => [['medal', 'Terakreditasi A'], ['school', 'Bimbingan belajar intensif'], ['people', 'English Conversation'], ['sparkle', 'Santripreneur & kepemimpinan']],
            'schedule' => 'Senin–Jumat · 07.15–14.20 WIB',
            'boarding' => 'Bisa sambil mondok (pilihan)',
            'ekskul' => ['paduan-suara', 'badminton', 'silat', 'pramuka', 'handball', 'futsal'],
        ],
        'smk' => [
            'short' => 'SMK', 'name' => 'SMK Ibnu Aqil', 'color' => 'smk',
            'who' => 'Lulusan SMP/MTs', 'line' => 'Terampil, siap kerja, siap kuliah.',
            'leader' => ['Hj. Eva Fauziah, M.Pd.', 'Kepala Sekolah'],
            'vision' => 'Lulusan kompeten, jujur, dan produktif yang siap berkarya di dunia kerja maupun kuliah.',
            'highlights' => [['medal', 'Terakreditasi A'], ['form', 'Farmasi · Akuntansi · Bisnis'], ['compass', 'PKL & kunjungan industri'], ['sparkle', 'Peluang kerja ke Jerman, Jepang, Korea']],
            'schedule' => 'Senin–Jumat · 07.15–14.20 WIB',
            'boarding' => null,
            'ekskul' => ['futsal', 'silat', 'handball', 'badminton', 'menggambar'],
        ],
        'pesantren' => [
            'short' => 'Pesantren', 'name' => 'Pondok Pesantren Ibnu Aqil', 'color' => 'pesantren',
            'who' => 'Siswa SMP & SMA yang ingin mondok', 'line' => 'Mondok itu pilihan, bukan kewajiban.',
            'leader' => ['Hj. Mia Hamidiyah, S.Sos., M.M.', 'Pengasuh Ma\'had (MDIIA)'],
            'vision' => 'Santri yang alim, berakhlak karimah, dan menghidupkan tradisi kitab kuning di era digital.',
            'highlights' => [['book', '30+ kitab kuning'], ['mosque', 'Tahfidz Al-Qur\'an & 1000 hadis'], ['bed', 'Asrama putra & putri'], ['people', 'Public speaking & bahtsul masail']],
            'schedule' => 'Khusus siswa SMP dan SMA · opsional',
            'boarding' => 'Opsional untuk siswa SMP & SMA',
            'ekskul' => [],
        ],
    ];

    public static function get(string $slug): ?array
    {
        $unit = self::UNITS[$slug] ?? null;
        if (!$unit) {
            return null;
        }
        $unit['slug'] = $slug;
        $logo = PublicPhotos::unitLogo($slug);
        $unit['logo'] = $logo ? asset($logo) : null;
        $rel = 'assets/foto/pimpinan/'.$slug.'.webp';
        $path = public_path($rel);
        $unit['leader_photo'] = is_file($path) && !is_link($path) && ($size = @getimagesize($path))
            ? ['src' => asset($rel), 'width' => $size[0], 'height' => $size[1]] : null;

        return $unit;
    }

    public static function all(): array
    {
        return array_map(fn ($slug) => self::get($slug), self::ORDER);
    }

    public static function ekskul(array $keys): array
    {
        $out = [];
        foreach ($keys as $key) {
            if (!isset(self::EKSKUL[$key])) {
                continue;
            }
            [$label, $file] = self::EKSKUL[$key];
            $rel = $file ? 'assets/ekskul/'.$file.'.webp' : null;
            $out[] = ['key' => $key, 'label' => $label, 'src' => $rel && is_file(public_path($rel)) ? asset($rel) : null];
        }

        return $out;
    }
}
