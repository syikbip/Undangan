    @php
        $namaPria = $mempelai->nama_pria;
        $namaWanita = $mempelai->nama_wanita;
        $inisial = mb_strtoupper(mb_substr($namaPria, 0, 1).mb_substr($namaWanita, 0, 1));
        $teks = $mempelai->teksUndangan();
        $akadDate = \Carbon\CarbonImmutable::parse($akad->tanggal, 'Asia/Jakarta')->locale('id');
        $mulai = $akad?->waktu_mulai ? $akadDate->setTimeFromTimeString($akad->waktu_mulai) : $akadDate;
        $formKembali = session('success_ucapan') ? 'ucapan' : ((session('success') || session('error')) ? 'rsvp' : old('form_section'));
    @endphp

<!DOCTYPE html>
<html lang="id" class="js">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Pernikahan | {{ $namaPria }} &amp; {{ $namaWanita }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&amp;family=Bodoni+Moda:wght@500&amp;family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&amp;family=Source+Sans+3:wght@400;600&amp;display=swap" rel="stylesheet">
    @vite('resources/css/invitation.css')
</head>

<body>



    <x-invitation-cover :gallery-photo="$galeris->first()?->foto" :date="$akadDate" :couple="$mempelai" :guest="is_string(request('to')) ? request('to') : null" />

    <div class="invitation-content" id="invitationContent" inert aria-hidden="true">

        <x-invitation-letter :message="$teks['pembuka']" :start="$mulai" :couple="$mempelai" />

    <!-- Mempelai -->
    <section class="section" id="mempelai" tabindex="-1" aria-labelledby="judul-mempelai">
        <h2 id="judul-mempelai">Mempelai</h2>
        <div class="couple-divider" aria-hidden="true">
            <img src="{{ asset('assets/wedding/couple-gold-branch.webp') }}" width="183" height="57" alt="">
        </div>
        @if(filled($teks['pengantar_mempelai']))
            <p class="couple-intro">{{ $teks['pengantar_mempelai'] }}</p>
        @endif
        <div class="couple-portraits">
            <x-couple-portrait :name="$namaPria" :photo="$mempelai?->foto_pria" side="groom" />
            <img class="couple-separator" src="{{ asset('assets/wedding/couple-gold-ampersand.webp') }}" width="108" height="109" alt="">
            <x-couple-portrait :name="$namaWanita" :photo="$mempelai?->foto_wanita" side="bride" />
        </div>
    </section>

    <!-- COUNTDOWN -->
    <section class="section countdown-section">

        <h2>Menuju Hari Pernikahan</h2>

        @if($mulai)
        <div class="countdown" data-start="{{ $mulai->toIso8601String() }}">
            <div class="count-box">
                <strong id="days">00</strong>
                <span>Hari</span>
            </div>

            <div class="count-box">
                <strong id="hours">00</strong>
                <span>Jam</span>
            </div>

            <div class="count-box">
                <strong id="minutes">00</strong>
                <span>Menit</span>
            </div>

            <div class="count-box">
                <strong id="seconds">00</strong>
                <span>Detik</span>
            </div>
        </div>
        @else
            <p class="intro">Tanggal acara akan segera diumumkan.</p>
        @endif
    </section>

    <!-- Kisah Cinta -->
    <section class="section" id="story" aria-labelledby="story-title">
        @if($mempelai->story_photo || $galeris->isNotEmpty())
        <div class="story-mount">
            <img class="story-image" src="{{ asset('storage/'.($mempelai->story_photo ?: $galeris->first()->foto)) }}" alt="{{ $namaPria }} dan {{ $namaWanita }}" loading="lazy">
            @foreach (['tl', 'tr', 'bl', 'br'] as $corner)
                <img class="story-corner {{ $corner }}" src="{{ asset('assets/wedding/photo-frame-corner.webp') }}" width="39" height="39" alt="">
            @endforeach
        </div>
        @endif
        <div class="story-copy">
            <header class="story-heading">
                <h2 id="story-title">Kisah Cinta</h2>
                <div class="story-divider" aria-hidden="true">
                    <img src="{{ asset('assets/wedding/couple-gold-branch.webp') }}" width="183" height="57" alt="">
                </div>
            </header>
            <div class="story-prose">
                @foreach(preg_split('/\R\s*\R/u', (string) $mempelai->deskripsi, -1, PREG_SPLIT_NO_EMPTY) as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
                @if(filled($teks['kutipan']))
                    <blockquote>{{ $teks['kutipan'] }}</blockquote>
                @endif
                <p class="story-signature">{{ $namaPria }} &amp; {{ $namaWanita }}</p>
            </div>
        </div>
    </section>

    <!-- Galeri -->
    <section class="section" id="galeri" aria-labelledby="gallery-title">
        <header class="gallery-heading">
            <h2 id="gallery-title">Galeri</h2>
            <div class="gallery-divider" aria-hidden="true">
                <img src="{{ asset('assets/wedding/couple-gold-branch.webp') }}" width="183" height="57" alt="">
            </div>
            @if(filled($teks['pengantar_galeri']))
                <p>{{ $teks['pengantar_galeri'] }}</p>
            @endif
        </header>
        <div class="gallery-pages">
            @forelse ($galeris->chunk(4) as $spread)
                <div class="gallery-spread spread-{{ $spread->count() }}">
                    @foreach ($spread as $galeri)
                        <x-gallery-photo :photo="$galeri->foto" :alt="$galeri->judul ?: 'Foto '.$namaPria.' dan '.$namaWanita" />
                    @endforeach
                </div>
            @empty
                <p class="gallery-empty">Foto galeri belum tersedia.</p>
            @endforelse
        </div>
    </section>

    <!-- Acara -->
    <section class="section" id="acara" aria-labelledby="akad-heading">
        <h2 id="akad-heading">Acara Pernikahan</h2>
        <div class="akad-divider" aria-hidden="true">
            <img src="{{ asset('assets/wedding/event-floral-divider.webp') }}" alt="" width="240" height="82">
        </div>
        @if(filled($teks['pengantar_acara']))
            <p class="akad-intro">{{ str_replace('{acara}', mb_strtolower($akad->nama_acara), $teks['pengantar_acara']) }}</p>
        @endif
        <x-wedding-event :event="$akad" :date="$akadDate" :location-url="$akad->maps_url" />
    </section>

    @include('components.wedding-guestbook')
    <x-wedding-gift :settings="$mempelai->tanda_kasih ?? []" />

    <!-- FOOTER -->
    <footer class="wedding-closing">
        @if($inisial === 'IK')
        <img class="closing-monogram" src="{{ asset('assets/wedding/footer-monogram-ik.webp') }}"
             alt="Monogram IK" width="212" height="153" loading="lazy">
        @else
            <span class="closing-signature" aria-label="Monogram {{ $inisial }}">{{ $inisial }}</span>
        @endif
        @if(filled($teks['penutup']))
            <p class="closing-message">{{ $teks['penutup'] }}</p>
        @endif
        <p class="closing-signature">{{ $namaPria }} &amp; {{ $namaWanita }}</p>
        <time datetime="{{ $akadDate->toDateString() }}">{{ $akadDate->translatedFormat('d F Y') }}</time>
    </footer>

    <x-invitation-music :couple="$mempelai" />

    <!-- JAVASCRIPT -->
    <script>
        const openingCover = document.getElementById('openingCover');
        const openInvitation = document.getElementById('openInvitation');
        const invitationContent = document.getElementById('invitationContent');
        const invitationStart = document.getElementById('surat-undangan');

        openInvitation.addEventListener('click', () => {
            document.dispatchEvent(new CustomEvent('invitation:opened', { detail: { fromClick: true } }));
            openInvitation.disabled = true;
            openInvitation.setAttribute('aria-expanded', 'true');
            invitationContent.removeAttribute('inert');
            invitationContent.removeAttribute('aria-hidden');
            document.body.classList.add('invitation-open');

            window.setTimeout(() => {
                openingCover.hidden = true;
                invitationStart.focus({ preventScroll: true });
                invitationStart.scrollIntoView({
                    behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
                    block: 'start',
                });
            }, 460);
        });

        const returnSection = @js($formKembali) || location.hash.slice(1);
        if (['acara', 'rsvp', 'ucapan'].includes(returnSection)) {
            document.body.classList.add('invitation-open');
            document.dispatchEvent(new CustomEvent('invitation:opened', { detail: { fromClick: false } }));
            openingCover.hidden = true;
            invitationContent.removeAttribute('inert');
            invitationContent.removeAttribute('aria-hidden');
            document.getElementById(returnSection).scrollIntoView();
        }

        // Countdown pernikahan
        const countdown = document.querySelector('.countdown');
        const weddingDate = countdown ? Date.parse(countdown.dataset.start) : null;

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = weddingDate - now;

            if (distance <= 0) {
                document.getElementById('days').textContent = '00';
                document.getElementById('hours').textContent = '00';
                document.getElementById('minutes').textContent = '00';
                document.getElementById('seconds').textContent = '00';
                return;
            }

            document.getElementById('days').textContent =
                Math.floor(distance / (1000 * 60 * 60 * 24));

            document.getElementById('hours').textContent =
                Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

            document.getElementById('minutes').textContent =
                Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

            document.getElementById('seconds').textContent =
                Math.floor((distance % (1000 * 60)) / 1000);
        }

        if (countdown) {
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }
    </script>

    </div>
</body>
</html>
