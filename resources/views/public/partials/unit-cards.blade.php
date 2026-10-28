{{-- Kartu sekolah berwarna: hanya logo, nama, dan satu baris. Dipakai di beranda dan halaman Tentang. --}}
<div class="ia-units">
    @foreach(\App\Services\Units::all() as $u)
    @continue(isset($only) && !in_array($u['slug'], $only, true))
    <a class="ia-unit ia-c-{{ $u['color'] }}" href="{{ route('public.unit', $u['slug']) }}">
        <span class="ia-unit-logo-wrap">@if($u['logo'])<img src="{{ $u['logo'] }}" alt="Logo {{ $u['name'] }}" width="120" height="120" loading="lazy" decoding="async">@else<strong>{{ $u['short'] }}</strong>@endif</span>
        <span class="ia-unit-text">
            <strong>{{ $u['name'] }}</strong>
            <span>{{ $u['line'] }}</span>
            <small>@include('public.partials.icon', ['name' => 'user', 'size' => 15]) {{ $u['who'] }}</small>
        </span>
        <span class="ia-unit-go" aria-hidden="true">@include('public.partials.icon', ['name' => 'arrow', 'size' => 20])</span>
    </a>
    @endforeach
</div>
