@props(['event' => null, 'date', 'locationUrl' => null])

@php
    $address = trim((string) $event?->alamat);
    $venue = trim((string) $event?->lokasi);
    $destination = trim($venue.' '.$address);
    $url = $locationUrl ?: ($destination !== '' ? 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($destination) : null);
@endphp

<article class="akad-card" aria-labelledby="akad-title">
    <svg class="akad-frame" viewBox="0 0 1160 535" preserveAspectRatio="none" aria-hidden="true">
        <path d="M45 2 H1115 Q1120 32 1158 39 V496 Q1126 499 1115 533 H45 Q35 500 2 496 V39 Q35 32 45 2Z" fill="none" stroke="#fffbef" stroke-width="3"/>
        <path d="M48 12 H1112 Q1121 37 1147 45 V489 Q1121 496 1111 522 H49 Q39 497 13 489 V45 Q39 38 48 12Z" fill="none" stroke="#b47c28" stroke-width="1.6"/>
    </svg>
    <time class="akad-date" datetime="{{ $date->toDateString() }}">
        <span>{{ $date->translatedFormat('l') }}</span>
        <b>{{ $date->format('d') }}</b>
        <span>{{ $date->translatedFormat('F Y') }}</span>
    </time>
    <div class="akad-details">
        <h3 id="akad-title">{{ $event->nama_acara }}</h3>
        <div class="akad-info">
            <svg viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="16" r="13.5"/><path d="M16 7v9l7 3"/></svg>
            <span>
                @if ($event?->waktu_mulai)
                    {{ substr($event->waktu_mulai, 0, 5) }}{{ $event->waktu_selesai ? ' ? '.substr($event->waktu_selesai, 0, 5) : '' }} WIB
                @else
                    [Waktu {{ str_contains(mb_strtolower($event->nama_acara), 'akad') ? 'akad' : 'acara' }}]
                @endif
            </span>
            <svg viewBox="0 0 32 36" aria-hidden="true"><path d="M16 34S3 20 3 14a13 13 0 0 1 26 0c0 6-13 20-13 20Z"/><circle cx="16" cy="14" r="5"/></svg>
            <span>{{ $venue ?: '[Nama tempat]' }}<br>{{ $address ?: '[Alamat lengkap]' }}</span>
        </div>
        <figure class="akad-map">
            <img src="{{ asset('assets/wedding/event-map-illustration.webp') }}" width="544" height="163" alt="">
            <figcaption>Ilustrasi denah</figcaption>
        </figure>
        @if($url)
            <a class="akad-location" href="{{ $url }}" target="_blank" rel="noopener">Lihat Lokasi</a>
        @else
            <button type="button" class="akad-location" aria-expanded="false" aria-controls="akad-location-status" onclick="const status = document.getElementById('akad-location-status'); status.hidden = !status.hidden; this.setAttribute('aria-expanded', String(!status.hidden))">Lihat Lokasi</button>
            <p id="akad-location-status" hidden>Lokasi akad belum tersedia.</p>
        @endif
    </div>
</article>
