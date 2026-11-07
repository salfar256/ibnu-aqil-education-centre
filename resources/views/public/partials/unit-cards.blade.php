{{-- Kartu sekolah berwarna: hanya logo, nama, dan satu baris. Dipakai di beranda dan halaman Tentang. --}}
<div class="ia-units">
    @foreach(\App\Services\Units::all() as $u)
    @continue(isset($only) && !in_array($u['slug'], $only, true))
    @php($full = \App\Services\Units::full($u['slug']))
    <a class="ia-unit ia-c-{{ $u['color'] }}{{ $full ? ' ia-unit-full' : '' }}" href="{{ route('public.unit', $u['slug']) }}">
        <span class="ia-unit-logo-wrap">@if($u['logo'])<img src="{{ $u['logo'] }}" alt="Logo {{ $u['name'] }}" width="120" height="120" loading="lazy" decoding="async">@else<strong>{{ $u['short'] }}</strong>@endif</span>
        <span class="ia-unit-text">
            <strong>{{ $u['name'] }}</strong>
            @if($full)<em class="ia-full-badge">Kuota SPMB sudah penuh</em><span>Terima kasih atas kepercayaannya.</span>@else<span>{{ $u['line'] }}</span>@endif
            <small>@include('public.partials.icon', ['name' => 'user', 'size' => 15]) {{ $u['who'] }}</small>
        </span>
        <span class="ia-unit-go" aria-hidden="true">@include('public.partials.icon', ['name' => 'arrow', 'size' => 20])</span>
    </a>
    @endforeach
</div>
