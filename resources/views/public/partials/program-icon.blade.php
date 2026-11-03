{{-- Ikon program: "img:folder/nama" memakai gambar di assets/icons, selain itu ikon garis bawaan. --}}
@if(str_starts_with($icon, 'img:') && is_file(public_path('assets/icons/'.substr($icon, 4).'.webp')))
<span class="ia-program-icon ia-program-img"><img src="{{ asset('assets/icons/'.substr($icon, 4).'.webp') }}" alt="" width="64" height="64" loading="lazy" decoding="async"></span>
@else
<span class="ia-program-icon">@include('public.partials.icon', ['name' => str_starts_with($icon, 'img:') ? 'star' : $icon, 'size' => 26])</span>
@endif
