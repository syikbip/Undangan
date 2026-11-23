@props(['photo', 'alt', 'position' => '50% 50%'])
<figure class="gallery-print">
    <img class="gallery-photo" src="{{ asset('storage/'.$photo) }}" alt="{{ $alt }}" style="object-position: {{ $position }}" loading="lazy">
    @foreach (['tl', 'tr', 'bl', 'br'] as $corner)
        <img class="gallery-corner {{ $corner }}" src="{{ asset('assets/wedding/photo-frame-corner.webp') }}" width="39" height="39" alt="">
    @endforeach
</figure>
