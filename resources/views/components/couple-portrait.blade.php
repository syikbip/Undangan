@props(['name', 'photo', 'side'])
<figure class="couple-portrait portrait-{{ $side }}">
    <div class="portrait-mount">
        @if ($photo)
            <img class="couple-photo" src="{{ asset('storage/'.$photo) }}" alt="Potret {{ $name }}" loading="lazy">
        @endif
        @foreach (['tl', 'tr', 'bl', 'br'] as $corner)
            <img class="mount-corner corner-{{ $corner }}" src="{{ asset('assets/wedding/photo-frame-corner.webp') }}" width="39" height="39" alt="">
        @endforeach
    </div>
    <figcaption>{{ $name }}</figcaption>
</figure>
