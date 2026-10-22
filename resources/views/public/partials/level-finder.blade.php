@php
    use App\Services\WhatsApp;
    $finderWa = WhatsApp::number($settings ?? []);
    $finderResults = [
        'mi' => ['MI Ibnu Aqil', 'Pendidikan dasar dengan program kepesantrenan, pembinaan karakter, dan bimbingan baca tulis Al-Qur\'an.'],
        'smp' => ['SMP Ibnu Aqil', 'BTQ Plus, tahfidz, dan beragam pilihan ekstrakurikuler, terakreditasi A.'],
        'sma' => ['SMA Ibnu Aqil Boarding School', 'Persiapan kuliah dengan bimbingan belajar intensif, English Conversation, dan pembinaan kepemimpinan di asrama.'],
        'smk' => ['SMK Ibnu Aqil', 'Keahlian siap kerja di jurusan Farmasi, Akuntansi dan Keuangan Lembaga, atau Bisnis dan Pemasaran.'],
        'smasmk' => ['SMA Boarding School atau SMK', 'Dua jalan setelah SMP: SMA untuk persiapan kuliah, SMK untuk keahlian siap kerja. Admin dapat membantu menimbang yang paling sesuai.'],
    ];
@endphp
@if($finderWa)
<section class="section section-finder" aria-labelledby="finder-heading" data-finder-section hidden>
    <div class="container finder-grid">
        <div class="finder-intro" data-reveal>
            <p class="eyebrow">Temukan jenjang</p>
            <h2 id="finder-heading">Jenjang mana yang<br><em>tepat untuknya?</em></h2>
            <p>Jawab dua sampai tiga pertanyaan singkat. Kami tunjukkan unit yang sesuai, lalu Anda bisa langsung bertanya kepada admin PPDB.</p>
        </div>
        <div class="finder" data-finder data-wa="https://wa.me/{{ $finderWa }}" data-greeting="{{ WhatsApp::GREETING }}" data-reveal>
            <div class="finder-step" data-step="stage">
                <p class="finder-question"><span>1</span>Saat ini putra-putri Anda…</p>
                <div class="finder-options">
                    <button type="button" data-answer="mi">Akan masuk sekolah dasar</button>
                    <button type="button" data-answer="smp">Kelas 6 SD/MI atau baru lulus</button>
                    <button type="button" data-answer="next">Kelas 9 SMP/MTs atau baru lulus</button>
                </div>
            </div>
            <div class="finder-step" data-step="goal" hidden>
                <p class="finder-question"><span>2</span>Setelah lulus nanti, ia ingin…</p>
                <div class="finder-options">
                    <button type="button" data-answer="sma">Melanjutkan kuliah</button>
                    <button type="button" data-answer="smk">Punya keahlian siap kerja</button>
                    <button type="button" data-answer="smasmk">Belum tahu</button>
                </div>
            </div>
            <div class="finder-step" data-step="board" hidden>
                <p class="finder-question"><span data-board-number>3</span>Apakah ingin sekalian mondok di pesantren?</p>
                <div class="finder-options">
                    <button type="button" data-answer="ya">Ya, ingin mondok</button>
                    <button type="button" data-answer="pertimbangkan">Masih dipertimbangkan</button>
                    <button type="button" data-answer="tidak">Tidak, pulang ke rumah</button>
                </div>
            </div>
            @foreach($finderResults as $key => [$name, $text])
            <div class="finder-result" data-result="{{ $key }}" data-name="{{ $name }}" hidden>
                <p class="eyebrow">Rekomendasi untuk Anda</p>
                <h3>{{ $name }}</h3>
                <p>{{ $text }}</p>
                <p class="finder-board-note" hidden>Pondok Pesantren Ibnu Aqil berada di kawasan yang sama, dengan asrama putra dan putri.</p>
                <div class="finder-actions">
                    <a class="button button-whatsapp" data-finder-wa href="https://wa.me/{{ $finderWa }}" target="_blank" rel="noopener noreferrer">Tanya admin tentang ini <span aria-hidden="true">↗</span></a>
                    @if($key === 'smasmk')
                    <a class="text-link" href="{{ route('public.unit', 'sma') }}">Profil SMA <span aria-hidden="true">→</span></a><a class="text-link" href="{{ route('public.unit', 'smk') }}">Profil SMK <span aria-hidden="true">→</span></a>
                    @else
                    <a class="text-link" href="{{ route('public.unit', $key) }}">Lihat profil unit <span aria-hidden="true">→</span></a>
                    @endif
                </div>
            </div>
            @endforeach
            <button type="button" class="finder-restart" data-finder-restart hidden>↺ Ulangi pertanyaan</button>
        </div>
    </div>
</section>
@endif
