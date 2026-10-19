@php($socialLinks = \App\Services\SocialLinks::social($settings ?? []))
@if($socialLinks !== [])
<div class="social-links">
    @foreach($socialLinks as $key => $link)
    <a class="social-link" href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $link['label'] }} Ibnu Aqil {{ $link['handle'] }} (tab baru)">
        @switch($key)
            @case('instagram_url')<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="3.5" width="17" height="17" rx="5" fill="none" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="1.7"/><circle cx="17.2" cy="6.8" r="1.1" fill="currentColor"/></svg>@break
            @case('youtube_url')<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="2.5" y="5.5" width="19" height="13" rx="4" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="m10 9 5 3-5 3Z" fill="currentColor"/></svg>@break
            @default<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.5 3.5v11.2a3.3 3.3 0 1 1-3.3-3.3m3.3-7.9c.4 2.6 2.2 4.4 4.8 4.6" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
        @endswitch
        <span>{{ $link['label'] }}</span>
    </a>
    @endforeach
</div>
@endif
