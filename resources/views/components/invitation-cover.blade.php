@props(['date', 'couple', 'guest' => null, 'galleryPhoto' => null])
<header id="openingCover" class="cover">
    <div class="stationery">
        <div class="crest" aria-hidden="true">
            <span>{{ mb_strtoupper(mb_substr(trim($couple->nama_pria), 0, 1)) }}</span>
            <span>{{ mb_strtoupper(mb_substr(trim($couple->nama_wanita), 0, 1)) }}</span>
        </div>
        <p class="kicker">Undangan Pernikahan</p>
        <div class="portrait">
            <div class="arch">
                @if($couple?->foto_pria && $couple?->foto_wanita)
                    <img src="{{ asset('storage/'.$couple->foto_pria) }}" alt="{{ $couple->nama_pria }}">
                    <img src="{{ asset('storage/'.$couple->foto_wanita) }}" alt="{{ $couple->nama_wanita }}">
                @elseif($galleryPhoto || $couple->foto_pria || $couple->foto_wanita)
                    <img class="together" src="{{ asset('storage/'.($galleryPhoto ?: $couple->foto_pria ?: $couple->foto_wanita)) }}" alt="{{ $couple->nama_pria }} dan {{ $couple->nama_wanita }}">
                @endif
            </div>
            <img class="flower flower-top" src="{{ asset('assets/wedding/cover-flower-top-left.webp') }}" alt="">
            <img class="flower flower-base" src="{{ asset('assets/wedding/cover-flower-center-bottom.webp') }}" alt="">
        </div>
        <img class="flower flower-corner" src="{{ asset('assets/wedding/cover-flower-bottom-right.webp') }}" alt="">
        <div class="details">
            <div class="rule rule-first" aria-hidden="true"><b></b></div>
            <h1>{{ $couple->nama_pria }} <i>&amp;</i> {{ $couple->nama_wanita }}</h1>
            <p class="date">{{ $date ? \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y') : 'Tanggal pernikahan segera diumumkan' }}</p>
            <div class="rule" aria-hidden="true"><b></b></div>
            @if(filled($guest))
                <p class="guest">Kepada Yth.<br><strong>{{ $guest }}</strong></p>
            @endif
            <button class="open-button" id="openInvitation" type="button" aria-controls="invitationContent" aria-expanded="false"><span>Buka Undangan</span></button>
        </div>
    </div>
</header>