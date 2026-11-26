@props(['couple'])

@if ($couple->music_enabled && $couple->music_file && Storage::disk('public')->exists($couple->music_file))
    <audio id="invitationMusic" loop preload="none" src="{{ asset('storage/'.$couple->music_file) }}"
           data-volume="{{ $couple->music_volume }}" data-start-on-open="{{ (int) $couple->music_autoplay_on_open }}"></audio>
    <button type="button" class="invitation-music" id="musicControl" aria-label="Putar musik" aria-pressed="false" hidden>
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
            <path class="music-play" d="M6 3.2Q6 2.4 6.8 2.9L21 11.5Q21.8 12 21 12.5L6.8 21.1Q6 21.6 6 20.8Z"/>
            <path class="music-pause" d="M5 3h5v18H5zM15 3h5v18h-5z"/>
        </svg>
    </button>
    <script>
        (() => {
            const audio = document.getElementById('invitationMusic');
            const button = document.getElementById('musicControl');
            audio.volume = Math.min(1, Math.max(0, Number(audio.dataset.volume) / 100));

            function showPlayback(playing) {
                button.setAttribute('aria-pressed', String(playing));
                button.setAttribute('aria-label', playing ? 'Jeda musik' : 'Putar musik');
            }

            async function play() {
                try {
                    await audio.play();
                } catch {
                    if (audio.paused || audio.error) showPlayback(false);
                }
            }

            audio.addEventListener('playing', () => showPlayback(!audio.paused));
            for (const event of ['pause', 'ended', 'emptied']) {
                audio.addEventListener(event, () => showPlayback(false));
            }
            audio.addEventListener('error', () => {
                showPlayback(false);
                button.hidden = true;
            });
            button.addEventListener('click', () => {
                if (audio.paused) play();
                else audio.pause();
            });
            document.addEventListener('invitation:opened', (event) => {
                if (audio.error) return;
                button.hidden = false;
                if (event.detail.fromClick && audio.dataset.startOnOpen === '1') play();
            }, { once: true });
        })();
    </script>
@endif
