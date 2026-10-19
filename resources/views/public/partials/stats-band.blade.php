<section class="stats-band" aria-label="Ibnu Aqil dalam angka">
    <div class="container stats-grid">
        @foreach(\App\Services\SchoolProfile::STATS as [$value, $label])
        <div class="stat-item" data-reveal><strong>{{ $value }}</strong><span>{{ $label }}</span></div>
        @endforeach
    </div>
</section>
