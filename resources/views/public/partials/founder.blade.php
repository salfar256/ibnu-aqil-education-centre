@php($founder = \App\Services\SchoolProfile::founder())
<section class="founder-section">
    <p class="calligraphy-watermark calligraphy-watermark-light" lang="ar" dir="rtl" aria-hidden="true">المحافظة على القديم الصالح</p>
    <div class="container founder-grid">
        @if($founder)<figure class="founder-photo" data-reveal><img src="{{ $founder['src'] }}" alt="KH. Agus Salim, S.Sos., M.M., pendiri Pondok Pesantren Ibnu Aqil" width="{{ $founder['width'] }}" height="{{ $founder['height'] }}" loading="lazy" decoding="async"></figure>@endif
        <div class="founder-copy" data-reveal>
            <p class="eyebrow">Sejak 1992</p>
            <h2>Menjaga tradisi yang baik,<br><em>mengambil yang lebih baik.</em></h2>
            <p class="founder-arabic" lang="ar" dir="rtl">المحافظة على القديم الصالح والأخذ بالجديد الأصلح</p>
            <p>Pondok Pesantren Ibnu Aqil didirikan oleh <strong>KH. Agus Salim, S.Sos., M.M.</strong> pada tahun 1992 sebagai lembaga pendidikan Islam putra dan putri berhaluan Ahlussunnah wal Jama'ah an-Nahdliyyah. Tujuannya mencetak generasi yang siap menghadapi kemajuan zaman dengan akhlak dan ilmu yang mumpuni.</p>
            <p>Kini, di bawah Yayasan Haji Agus Salim Bogor, Ibnu Aqil menaungi MI, SMP, SMA Boarding School, SMK, dan pondok pesantren dalam satu lingkungan belajar.</p>
            <a class="text-link" href="{{ route('public.unit', 'pesantren') }}">Kenali Pondok Pesantren Ibnu Aqil <span aria-hidden="true">→</span></a>
        </div>
    </div>
</section>
