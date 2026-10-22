@php
    $pathSteps = [
        ['mi', 'MI', 'Madrasah Ibtidaiyah', 'Usia sekolah dasar. Pendidikan dasar dengan pembinaan karakter dan bimbingan baca tulis Al-Qur\'an.'],
        ['smp', 'SMP', 'Setelah lulus SD/MI', 'BTQ Plus, tahfidz, dan pilihan ekstrakurikuler untuk mengasah minat dan bakat.'],
    ];
    $pathBranches = [
        ['sma', 'SMA Boarding School', 'Untuk yang ingin kuliah', 'Bimbingan belajar intensif, English Conversation, dan pembinaan kepemimpinan di asrama.'],
        ['smk', 'SMK', 'Untuk yang ingin berkarya', 'Jurusan Farmasi, Akuntansi dan Keuangan Lembaga, serta Bisnis dan Pemasaran.'],
    ];
@endphp
<section class="section section-path" aria-labelledby="path-heading">
    <div class="container">
        <div class="section-heading" data-reveal><div><p class="eyebrow">Jalur pendidikan</p><h2 id="path-heading">Dari MI sampai lulus,<br><em>dalam satu lingkungan.</em></h2></div><p>Putra-putri dapat melanjutkan jenjang demi jenjang tanpa berpindah kawasan, dengan pembiasaan ibadah dan pesantren yang sama.</p></div>
        <ol class="path-track">
            @foreach($pathSteps as [$slug, $label, $when, $text])
            <li class="path-node" data-reveal><a href="{{ route('public.unit', $slug) }}"><span class="path-when">{{ $when }}</span><strong>{{ $label }}</strong><span class="path-text">{{ $text }}</span><span class="path-more">Lihat profil <span aria-hidden="true">→</span></span></a></li>
            @endforeach
            <li class="path-split" data-reveal>
                <span class="path-when">Setelah lulus SMP/MTs</span>
                <div class="path-branches">
                    @foreach($pathBranches as [$slug, $label, $when, $text])
                    <a class="path-branch" href="{{ route('public.unit', $slug) }}"><span class="path-when">{{ $when }}</span><strong>{{ $label }}</strong><span class="path-text">{{ $text }}</span><span class="path-more">Lihat profil <span aria-hidden="true">→</span></span></a>
                    @endforeach
                </div>
            </li>
        </ol>
        <a class="path-pesantren" href="{{ route('public.unit', 'pesantren') }}" data-reveal><span class="path-pesantren-mark" aria-hidden="true">✳</span><span><strong>Pondok Pesantren Ibnu Aqil</strong> berada di kawasan yang sama: kajian kitab kuning dan asrama putra-putri bagi santri yang ingin mondok.</span><span aria-hidden="true">↗</span></a>
    </div>
</section>
