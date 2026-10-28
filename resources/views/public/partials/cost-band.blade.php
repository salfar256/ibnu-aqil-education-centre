@php($costHref = \App\Services\WhatsApp::link($settings ?? [], \App\Services\WhatsApp::costMessage($costUnit ?? null)))
@if($costHref)
<section class="cost-band" aria-labelledby="cost-heading">
    <div class="container cost-band-inner" data-reveal>
        @if(is_file(public_path('assets/foto/web/siswa-buku.webp')))<img class="cost-figure float-figure" src="{{ asset('assets/foto/web/siswa-buku.webp') }}" alt="" width="366" height="900" loading="lazy" decoding="async">@else<span class="cost-mark" aria-hidden="true">✳</span>@endif
        <div>
            <p class="eyebrow">Biaya pendidikan</p>
            <h2 id="cost-heading">Ingin tahu rincian biaya{{ !empty($costUnit) ? ' '.\App\Services\WhatsApp::unitName($costUnit) : '' }}?</h2>
            <p>Rincian biaya terbaru dijelaskan langsung oleh admin PPDB, menyesuaikan unit dan pilihan mondok. Tanyakan juga ketentuan pembayaran yang berlaku.</p>
        </div>
        <a class="button button-whatsapp" href="{{ $costHref }}" target="_blank" rel="noopener noreferrer">Tanya rincian biaya <span aria-hidden="true">↗</span></a>
    </div>
</section>
@endif
