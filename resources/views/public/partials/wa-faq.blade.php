@php($faqItems = \App\Services\WhatsApp::faq($settings ?? [], $faqUnit ?? null))
@if($faqItems !== [])
<section class="section section-faq" aria-labelledby="faq-heading">
    <div class="container faq-grid">
        <div data-reveal>
            <p class="eyebrow">Pertanyaan orang tua</p>
            <h2 id="faq-heading">Yang sering<br><em>ditanyakan.</em></h2>
            <p class="faq-intro">Ketuk pertanyaan untuk melihat jawaban singkat. Butuh penjelasan lebih lanjut? Pertanyaan Anda langsung terkirim ke admin PPDB lewat WhatsApp.</p>
        </div>
        <div class="faq-list">
            @foreach($faqItems as $item)
            <details class="faq-item" data-reveal>
                <summary><span>{{ $item['question'] }}</span><i aria-hidden="true">+</i></summary>
                <div class="faq-answer"><p>{{ $item['answer'] }}</p><a class="faq-ask" href="{{ $item['href'] }}" target="_blank" rel="noopener noreferrer"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 11.8a8 8 0 0 1-11.9 7L4 20l1.2-4A8 8 0 1 1 20 11.8Z" fill="none" stroke="currentColor" stroke-width="1.7"/></svg>Tanyakan ke admin</a></div>
            </details>
            @endforeach
        </div>
    </div>
</section>
@endif
