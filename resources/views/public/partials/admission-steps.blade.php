@php($ppdbUrl = preg_match('~^https://wa\.me/[0-9]{9,15}$~', (string) ($settings['whatsapp_url'] ?? '')) ? $settings['whatsapp_url'] : null)
<div class="admission-steps">
    <ol class="step-list">
        @foreach(\App\Services\SchoolProfile::ADMISSION_STEPS as [$title, $text])
        <li data-reveal><span class="step-number">{{ $loop->iteration }}</span><div><h3>{{ $title }}</h3><p>{{ $text }}</p></div></li>
        @endforeach
    </ol>
    <aside class="admission-card" data-reveal>
        <p class="eyebrow">PPDB 2026/2027</p>
        <h3>Konsultasikan pilihan unit untuk putra-putri Anda.</h3>
        <dl class="schedule-list">@foreach(\App\Services\SchoolProfile::SCHEDULE as [$label, $value])<div><dt>{{ $label }}</dt><dd>{{ $value }}</dd></div>@endforeach</dl>
        @if($ppdbUrl)<a class="button button-whatsapp" href="{{ $ppdbUrl }}" target="_blank" rel="noopener noreferrer">Chat WhatsApp PPDB <span aria-hidden="true">↗</span></a>@endif
        <p class="admission-note">Kuota setiap unit terbatas. Rincian biaya terbaru disampaikan langsung oleh admin PPDB.</p>
    </aside>
</div>
