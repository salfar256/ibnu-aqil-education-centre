<?php
namespace App\Services;

// Short, display-ready profile of each unit: brand colour, leader portrait, vision, brochure programs,
// student photos, ekstrakurikuler stickers and downloadable brochures. Programs follow the 2027/2028 brochures.
class Units
{
    public const ORDER = ['mi', 'smp', 'sma', 'smk', 'pesantren'];

    public const PPDB_YEAR = '2027/2028';

    // Ekstrakurikuler → label and sticker file name (inside assets/ekskul/formal, assets/ekskul/lucu or assets/ekskul/smk).
    public const EKSKUL = [
        'tahfidz' => ["Tahfidz Al-Qur'an", 'tahfidz'],
        'tilawah' => ['Tilawah', 'tilawah'],
        'kaligrafi' => ['Kaligrafi', 'kaligrafi'],
        'paduan-suara' => ['Musik & Paduan Suara', 'paduan-suara'],
        'seni' => ['Seni', 'paduan-suara'],
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
        'silat' => ['Pencak Silat', 'pencak-silat'],
        'silat-gasmi' => ['Pencak Silat GASMI', 'pencak-silat'],
        'pagar-nusa' => ['Bela Diri Pagar Nusa', 'pencak-silat'],
        'degung' => ['Seni Musik Degung', 'angklung-degung'],
        'mewarnai' => ['Mewarnai', 'mewarnai'],
        'calistung' => ['Calistung (kelas 1–2)', 'calistung'],
        'paskibra' => ['Paskibra', 'paskibra'],
        'seni-tari' => ['Seni Tari', 'seni-tari'],
        'sastra-club' => ['Sastra Club', 'sastra-club'],
        'btq' => ['BTQ', 'btq'],
        'jerman-club' => ['Bahasa Jerman', 'jerman-club'],
        'accounting-club' => ['Accounting Club', 'accounting-club'],
    ];

    public const UNITS = [
        'mi' => [
            'short' => 'MI', 'name' => 'MI Ibnu Aqil', 'color' => 'mi',
            'who' => 'Usia 6–12 tahun', 'line' => 'Madrasah lebih baik, lebih baik madrasah.',
            'leader' => ['Dr. Hj. Fuzna Nur Aqilah, M.Pd.', 'Kepala Madrasah'],
            'vision' => 'Generasi Qur\'ani yang ceria, berakhlak mulia, dan cinta belajar sejak dini.',
            'motto' => 'Faqih · Fashih · Shalih',
            'programs' => [
                ['book', 'Bimbingan baca tulis Al-Qur\'an'],
                ['mosque', 'Shalat Dhuha & Dzuhur berjamaah'],
                ['sun', 'Tadarus bersama'],
                ['heart', 'Layanan bimbingan & konseling'],
                ['star', 'Kepesantrenan (kitab kuning dasar)'],
                ['people', 'Character building'],
                ['compass', 'Outing class'],
                ['cart', 'Market day'],
                ['chef', 'Cooking class'],
                ['mic', 'Pentas seni'],
                ['tent', 'Perkemahan pramuka'],
                ['leaf', 'Adiwiyata'],
            ],
            'schedule' => 'Senin–Jumat · 07.25–14.00 WIB',
            'boarding' => null,
            'ekskul_style' => 'lucu',
            'ekskul' => ['pagar-nusa', 'degung', 'english-club', 'mewarnai', 'pramuka', 'tahfidz', 'futsal', 'calistung'],
            'students' => ['mi-1', 'mi-2', 'mi-3'],
            'brochure' => ['pages' => 2, 'drive' => 'https://drive.google.com/drive/folders/1HR7xfHbaDrTaDwnyYcQaz27yCIl9UW-v?usp=drive_link'],
        ],
        'smp' => [
            'short' => 'SMP', 'name' => 'SMP Ibnu Aqil', 'color' => 'smp',
            'who' => 'Lulusan SD/MI', 'line' => 'Berakhlak mulia, berilmu, berprestasi.',
            'leader' => ['Ade Irawan, S.Pd.I.', 'Kepala Sekolah'],
            'vision' => 'Pelajar berakhlak, berprestasi, dan cakap digital dengan jiwa santri.',
            'motto' => 'Berakhlak Mulia · Berilmu · Berprestasi',
            'programs' => [
                ['medal', 'Terakreditasi A'],
                ['book', 'BTQ Plus metode Tilawati'],
                ['star', 'Tahfidz Al-Qur\'an'],
                ['sun', 'Dhuha & tadarus setiap hari'],
                ['mosque', 'Muatan lokal keagamaan & pesantren'],
                ['check', 'Absensi elektronik'],
                ['shield', 'Keamanan 24 jam'],
                ['ball', '16 pilihan ekstrakurikuler'],
            ],
            'schedule' => 'Senin–Jumat · 07.15–14.20 WIB',
            'boarding' => 'Ayo sambil mondok di pesantren!',
            'ekskul_style' => 'formal',
            'ekskul' => ['futsal', 'handball', 'badminton', 'panahan', 'silat', 'karate', 'pramuka', 'tahfidz', 'kaligrafi', 'tilawah', 'sains-club', 'math-club', 'english-club', 'komputer', 'drumband', 'paduan-suara'],
            'students' => ['smp-1', 'smp-2'],
            'brochure' => ['pages' => 1, 'drive' => 'https://drive.google.com/drive/folders/143UuA-_LvFdWcTkaxfSvZ34NsfFa9lSP?usp=drive_link'],
        ],
        'sma' => [
            'short' => 'SMA', 'name' => 'SMA Ibnu Aqil Boarding School', 'color' => 'sma',
            'who' => 'Lulusan SMP/MTs', 'line' => 'Siap kuliah, siap memimpin.',
            'leader' => ['Hidmatullah, S.E., M.Pd.', 'Kepala Sekolah'],
            'vision' => 'Melahirkan pemimpin muda yang berakhlak, unggul akademik, dan siap bersaing di tingkat global.',
            'motto' => 'Islamic Boarding School',
            'programs' => [
                ['medal', 'Terakreditasi A'],
                ['mosque', 'Bimbingan praktik ibadah & Dhuha bersama'],
                ['school', 'Bimbingan belajar intensif'],
                ['laptop', 'Penguasaan TIK'],
                ['book', 'Hafalan Al-Qur\'an & hadis'],
                ['star', 'BTQ metode Tilawati'],
                ['sparkle', 'Pengembangan life skill'],
                ['mic', 'Bimbingan dakwah Islamiyah'],
                ['sun', 'Pembiasaan tadarus Al-Qur\'an'],
                ['book', 'Program baca kitab kuning'],
                ['people', 'Pelatihan profesi mengajar (kelas akhir)'],
            ],
            'schedule' => 'Senin–Jumat · 07.15–14.20 WIB',
            'boarding' => 'Ayo sambil mondok di pesantren!',
            'ekskul_style' => 'formal',
            'ekskul' => ['paduan-suara', 'badminton', 'silat-gasmi', 'pramuka', 'handball', 'futsal'],
            'students' => ['sma-1', 'sma-2'],
            'brochure' => ['pages' => 2, 'drive' => 'https://drive.google.com/drive/folders/1HuzLAXhcfCX4RqkH8mnv-dao7P6qJy4b?usp=drive_link'],
            'note' => 'Tidak menerima siswa pindahan.',
        ],
        'smk' => [
            'short' => 'SMK', 'name' => 'SMK Ibnu Aqil', 'color' => 'smk',
            'who' => 'Lulusan SMP/MTs', 'line' => '3 jurusan: Farmasi, Akuntansi, Pemasaran.',
            'leader' => ['Hj. Eva Fauziah, M.Pd.', 'Kepala Sekolah'],
            'vision' => 'Terwujudnya lingkungan belajar yang holistik dan menghasilkan lulusan yang kompeten di bidang kompetensinya dengan karakter utama, amanah, cerdas, mampu bekerja produktif, berwawasan global dan peduli lingkungan.',
            'motto' => 'Religius · Produktif · Mendunia',
            'majors' => [
                ['pill', 'Teknologi Farmasi', 'Bidang kesehatan & ilmu obat, didukung laboratorium farmasi.'],
                ['calc', 'Akuntansi & Keuangan Lembaga', 'Keterampilan praktis dunia keuangan dan akuntansi.'],
                ['cart', 'Bisnis Ritel (Pemasaran)', 'Pemasaran, penjualan, dan pengelolaan usaha ritel.'],
            ],
            'programs' => [
                ['medal', 'Terakreditasi A'],
                ['book', 'Bimbingan belajar baca tulis Al-Qur\'an'],
                ['mosque', 'Pembiasaan shalat Dhuha & Dzuhur'],
                ['sun', 'Pembiasaan tadarus bersama'],
                ['people', 'Program character building'],
                ['briefcase', 'Goes to Industries and Campus (GTIC)'],
                ['mic', 'Ibnu Aqil Festival (BAQFEST/Pensi)'],
                ['school', 'Training of Trainers (TOT)'],
            ],
            'achievements' => [
                'Juara 2 Kategori KU-15, Tropeo Bandung Handball Club Invitation 2026',
                'Juara 1 Handball Putri "Shark Handball", SOT Fest 3.0 SMAN Pesat Kota Bogor 2026',
                'Juara 1 Lomba Kriya, Gebyar FLS3SN tingkat KCD Wilayah 1 Kabupaten Bogor 2026',
                'Juara 2 Lomba Monolog, Gebyar FLS3SN tingkat KCD Wilayah 1 Kabupaten Bogor 2026',
                'Juara 2 Lomba Membaca Puisi, Gebyar FLS3SN tingkat KCD Wilayah 1 Kabupaten Bogor 2026',
                'Juara 1 Lomba Menulis Cerpen, Gebyar FLS3SN tingkat KCD Wilayah 1 Kabupaten Bogor 2026',
                'Juara 3 Festival Kompetensi Siswa Nasional tingkat KCD Wilayah 1 Kabupaten Bogor 2026',
                'Juara 3 Accounting Competition KBOF 2026',
                'Juara 2 Lomba Puisi tingkat SMA 2025',
                'Juara 2 Lomba Cerdas Cermat Akuntansi AFEST 2025 Universitas Pakuan',
            ],
            'note' => 'SPMB 2027/2028 dibuka 7 November 2026 sampai kuota terpenuhi, pukul 08.00–14.00 WIB. Pendaftaran dilakukan secara offline di sekolah. Admin SMK: 0858-1762-4288.',
            'schedule' => 'Senin–Jumat · 07.15–14.20 WIB',
            'boarding' => null,
            'ekskul_style' => 'smk',
            'ekskul' => ['futsal', 'handball', 'badminton', 'seni-tari', 'paskibra', 'sastra-club', 'silat', 'tahfidz', 'tilawah', 'english-club', 'jerman-club', 'btq', 'accounting-club'],
            'students' => ['smk-1', 'smk-2', 'smk-3'],
            'brochure' => ['pages' => 3, 'drive' => 'https://drive.google.com/drive/folders/1Hp4Z9W3y0jd_zdQ6F2FyU8ut9_yrwwFz?usp=drive_link'],
        ],
        'pesantren' => [
            'short' => 'Pesantren', 'name' => 'Pondok Pesantren Ibnu Aqil', 'color' => 'pesantren',
            'who' => 'Santri dari SMP & SMA', 'line' => 'Ayo mondok! Ilmu, adab, dan kemandirian.',
            'leader' => ['Hj. Mia Hamidiyah, S.Sos., M.M.', 'Pengasuh Ma\'had (MDIIA)'],
            'vision' => 'Santri yang alim, berakhlak karimah, dan menghidupkan tradisi kitab kuning di era digital.',
            'motto' => 'Menjaga tradisi yang baik, mengambil yang lebih baik',
            'programs' => [
                ['book', '30+ kitab kuning'],
                ['star', 'Tahsin & tahfidz Al-Qur\'an'],
                ['book', 'Tahfidz & fahmu 1000 hadis'],
                ['globe', 'Bahasa Arab & Inggris'],
                ['mosque', 'Pembiasaan ibadah, zikir & riyadhah'],
                ['mic', 'Public speaking'],
                ['people', 'Bahtsul masail'],
                ['bed', 'Asrama putra & putri terpisah'],
            ],
            'schedule' => 'Untuk santri SMP & SMA yang ingin mondok',
            'boarding' => 'Ayo mondok bagi santri SMP & SMA!',
            'ekskul_style' => 'formal',
            'ekskul' => [],
            'people' => 'Santri',
            'students' => ['smp-1', 'sma-3'],
            'brochure' => null,
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
        $unit['leader_photo'] = self::image('assets/foto/pimpinan/'.$slug.'.webp');
        $unit['student_photos'] = array_values(array_filter(array_map(fn ($k) => self::image('assets/foto/siswa/'.$k.'.webp'), $unit['students'] ?? [])));
        $unit['brochure_files'] = null;
        if (!empty($unit['brochure'])) {
            $pdf = 'assets/brosur/brosur-'.$slug.'-2027-2028.pdf';
            $pages = [];
            for ($i = 1; $i <= $unit['brochure']['pages']; $i++) {
                $preview = self::image('assets/brosur/brosur-'.$slug.'-'.$i.'.jpg');
                $hd = 'assets/brosur/brosur-'.$slug.'-'.$i.'-hd.jpg';
                if ($preview) {
                    $pages[] = $preview + ['hd' => is_file(public_path($hd)) ? asset($hd) : $preview['src']];
                }
            }
            $unit['brochure_files'] = [
                'pdf' => is_file(public_path($pdf)) ? asset($pdf) : null,
                'pdf_kb' => is_file(public_path($pdf)) ? (int) round(filesize(public_path($pdf)) / 1024) : null,
                'pages' => $pages,
                'drive' => $unit['brochure']['drive'] ?? null,
            ];
        }

        return $unit;
    }

    public static function all(): array
    {
        return array_map(fn ($slug) => self::get($slug), self::ORDER);
    }

    public static function ekskul(array $keys, string $style = 'formal'): array
    {
        $out = [];
        foreach ($keys as $key) {
            if (!isset(self::EKSKUL[$key])) {
                continue;
            }
            [$label, $file] = self::EKSKUL[$key];
            $rel = 'assets/ekskul/'.$style.'/'.$file.'.webp';
            $out[] = ['key' => $key, 'label' => $label, 'src' => is_file(public_path($rel)) ? asset($rel) : null];
        }

        return $out;
    }

    private static function image(string $rel): ?array
    {
        $path = public_path($rel);

        return is_file($path) && !is_link($path) && ($size = @getimagesize($path))
            ? ['src' => asset($rel), 'width' => $size[0], 'height' => $size[1]] : null;
    }
}
