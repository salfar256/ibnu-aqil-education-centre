<?php
namespace App\Services;

// Builds WhatsApp PPDB links with a prefilled message, so the admin knows which unit or topic a parent is asking about.
// The number always comes from the verified `whatsapp_url` setting; no link is produced when it is missing or malformed.
class WhatsApp {
    public const GREETING = 'Assalamu\'alaikum, Admin PPDB Ibnu Aqil. ';

    public const UNITS = [
        'mi' => ['MI', 'MI Ibnu Aqil'],
        'smp' => ['SMP', 'SMP Ibnu Aqil'],
        'sma' => ['SMA', 'SMA Ibnu Aqil Boarding School'],
        'smk' => ['SMK', 'SMK Ibnu Aqil'],
        'pesantren' => ['Pesantren', 'Pondok Pesantren Ibnu Aqil'],
    ];

    public static function number(array $settings): ?string
    {
        $value = (string) ($settings['whatsapp_url'] ?? $settings['whatsapp'] ?? '');

        return preg_match('~^https://wa\.me/([0-9]{9,15})$~', $value, $match) ? $match[1] : null;
    }

    public static function link(array $settings, string $message = ''): ?string
    {
        $number = self::number($settings);
        if ($number === null) {
            return null;
        }

        return 'https://wa.me/'.$number.($message !== '' ? '?text='.rawurlencode(self::GREETING.$message) : '');
    }

    public static function unitName(string $slug): string
    {
        return self::UNITS[$slug][1] ?? 'Ibnu Aqil';
    }

    public static function unitMessage(string $slug): string
    {
        return 'Saya ingin bertanya tentang pendaftaran di '.self::unitName($slug).'.';
    }

    public static function costMessage(?string $slug = null): string
    {
        $target = $slug ? self::unitName($slug) : 'unit Ibnu Aqil';

        return 'Saya ingin mengetahui rincian biaya pendaftaran '.$target.' tahun ajaran 2027/2028, serta apakah ada program keringanan atau cicilan.';
    }

    // Topics shown in the floating WhatsApp menu on every page.
    public static function menu(array $settings): array
    {
        $items = [];
        foreach (['mi', 'smp', 'sma', 'smk', 'pesantren'] as $slug) {
            $items[] = ['label' => self::UNITS[$slug][1], 'href' => self::link($settings, self::unitMessage($slug))];
        }
        $items[] = ['label' => 'Rincian biaya', 'href' => self::link($settings, self::costMessage())];
        $items[] = ['label' => 'Kunjungi sekolah', 'href' => self::link($settings, 'Saya ingin berkunjung ke sekolah Ibnu Aqil. Kapan waktu yang tepat?')];

        return array_values(array_filter($items, fn ($item) => $item['href'] !== null));
    }

    // Questions parents often ask. Answers only restate verified admissions information; anything else is routed to the admin.
    public static function faq(array $settings, ?string $slug = null): array
    {
        $unit = $slug ? self::unitName($slug) : 'Ibnu Aqil';
        $items = [
            ['Berapa biaya masuk dan biaya bulanannya?', 'Rincian biaya terbaru untuk tahun ajaran 2027/2028 disampaikan langsung oleh admin PPDB, sesuai unit dan pilihan mondok.', self::costMessage($slug)],
            ['Dokumen apa saja yang perlu disiapkan?', $slug === 'mi'
                ? 'Untuk MI: formulir pendaftaran, pas foto berwarna 3x4, fotokopi KTP orang tua atau wali, dan fotokopi kartu keluarga.'
                : 'Umumnya formulir pendaftaran, fotokopi ijazah yang dilegalisir, pas foto 3x4, fotokopi KTP orang tua atau wali, kartu keluarga, akta kelahiran, kartu NISN (jika ada), dan surat keterangan berkelakuan baik dari sekolah asal.',
                'Dokumen apa saja yang perlu disiapkan untuk mendaftar di '.$unit.'?'],
            ['Apakah siswa wajib mondok?', 'Siswa SMA Ibnu Aqil Boarding School wajib mondok. Bagi siswa SMP, mondok bersifat pilihan: boleh pulang-pergi dari rumah atau tinggal di asrama. Asrama putra dan putri di Pondok Pesantren Ibnu Aqil terpisah.', 'Saya ingin tahu tentang ketentuan mondok untuk siswa SMP/SMA di Ibnu Aqil. Bagaimana ketentuannya?'],
            ['Apakah kuota masih tersedia?', 'Kuota setiap unit terbatas. Ketersediaan tempat dan periode pendaftaran dipastikan langsung oleh admin PPDB.', 'Apakah kuota pendaftaran '.$unit.' masih tersedia?'],
            ['Bisakah saya melihat sekolah terlebih dahulu?', 'Bisa. Kantor pendaftaran melayani setiap hari kerja pukul 08.00–15.00 WIB di sekolah Ibnu Aqil, Laladon, Ciomas, Bogor.', 'Saya ingin berkunjung ke sekolah untuk melihat '.$unit.'. Kapan waktu yang tepat?'],
            ['Bagaimana jadwal tes dan wawancara?', 'Tes seleksi dan wawancara dilaksanakan sesuai jadwal dari panitia PPDB setelah formulir dan dokumen diserahkan.', 'Kapan jadwal tes dan wawancara untuk '.$unit.'?'],
        ];

        return array_values(array_filter(array_map(fn ($item) => ['question' => $item[0], 'answer' => $item[1], 'href' => self::link($settings, $item[2])], $items), fn ($item) => $item['href'] !== null));
    }
}
