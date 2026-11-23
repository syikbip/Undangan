@props(['start', 'couple', 'message'])
<header class="invitation-letter" id="surat-undangan" tabindex="-1" aria-labelledby="letter-title">
    @foreach (['embossed-top-left', 'embossed-bottom-right', 'floral-top-right', 'floral-bottom-left'] as $decoration)
        <img class="letter-detail {{ $decoration }}" src="{{ asset('assets/wedding/'.$decoration.'.webp') }}" alt="">
    @endforeach
    <img class="letter-envelope" src="{{ asset('assets/wedding/envelope-wax-seal.webp') }}" width="749" height="606" alt="Amplop ivory dengan segel burgundy bermonogram IK">
    <div class="letter-copy">
        <p class="letter-label">Pernikahan</p>
        <h2 id="letter-title">{{ $couple->nama_pria }} <i>&amp;</i> {{ $couple->nama_wanita }}</h2>
        @if(filled($message))
            <p class="letter-message">{{ $message }}</p>
        @endif
        <div class="letter-divider" aria-hidden="true"><span></span></div>
        @if ($start)
            <time class="letter-date" datetime="{{ $start->toDateString() }}">{{ $start->translatedFormat('l, d F Y') }}</time>
            <a class="letter-calendar" href="{{ route('undangan.kalender') }}" download>Simpan Tanggal</a>
        @else
            <p class="letter-date">Tanggal acara akan segera diumumkan.</p>
        @endif
        <nav class="letter-shortcuts" aria-label="Pintasan undangan">
            <a href="#acara">Lihat Acara</a>
            <a href="#rsvp">Konfirmasi Kehadiran</a>
        </nav>
    </div>
</header>