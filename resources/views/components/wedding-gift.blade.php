@props(['settings'])
@php
    $options = [];
    foreach (['bank', 'dana'] as $method) {
        $entry = $settings[$method] ?? [];
        $bank = $method === 'bank';
        $number = (string) ($entry['number'] ?? '');
        $label = $bank ? ($entry['name'] ?? '') : 'Nomor DANA';
        if (empty($entry['active']) || blank($label) || blank($entry['recipient'] ?? null)
            || !preg_match('/^[0-9]{5,}$/D', $number)) {
            continue;
        }
        $prefix = $bank ? '' : substr($number, 0, min(2, strlen($number) - 5));
        $hiddenDigits = strlen($number) - strlen($prefix) - 4;
        $groups = mb_str_split($prefix.str_repeat('•', $hiddenDigits), 4);
        $options[] = [
            'bank' => $bank, 'title' => $bank ? 'Transfer Bank' : 'DANA',
            'label' => $label, 'recipient' => $entry['recipient'], 'number' => $number,
            'masked' => implode(' ', $groups).' '.substr($number, -4),
            'action' => $bank ? 'Salin Nomor Rekening' : 'Salin Nomor DANA',
        ];
    }
@endphp
@if ($options)
<section class="gift" aria-labelledby="gift-title">
    <x-gift-frame />
    <h2 id="gift-title">Tanda Kasih</h2>
    <img class="gift-divider" src="{{ asset('assets/wedding/couple-gold-branch.webp') }}" alt="">
    <p class="gift-intro">Kehadiran dan doa Anda sudah sangat berarti bagi kami. Jika berkenan, Anda dapat mengirim tanda kasih melalui pilihan berikut.</p>
    <button class="gift-toggle" type="button" aria-controls="gift-options" aria-expanded="true" hidden><span>Tutup pilihan</span> <i aria-hidden="true"></i></button>
    <div class="gift-panel" id="gift-options">
        <x-gift-frame />
        @foreach ($options as $option)
            <article class="gift-option">
                <svg class="gift-icon" viewBox="0 0 64 58" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linejoin="round" aria-hidden="true">
                    @if ($option['bank'])
                        <path d="M2 20 32 3l30 17zM3 51h58v5H3zM7 24h12m-10 0v22m8-22v22M7 47h12m7-23h12m-10 0v22m8-22v22m-10 1h12m7-23h12m-10 0v22m8-22v22m-10 1h12"/>
                    @else
                        <path d="M5 17 46 6q4-1 5 3l3 9M59 32v-9q0-5-5-5H8q-5 0-5 5v27q0 5 5 5h46q5 0 5-5v-8M60 31H47a6 6 0 0 0 0 12h13z"/><circle cx="47" cy="37" r="1"/>
                    @endif
                </svg>
                <h3>{{ $option['title'] }}</h3>
                <p>{{ $option['label'] }}</p>
                <strong class="gift-number">{{ $option['masked'] }}</strong>
                <p>a.n. {{ $option['recipient'] }}</p>
                <button type="button" class="gift-copy" data-number="{{ $option['number'] }}" @disabled(!$option['number'])>
                    <svg viewBox="0 0 24 24" width="28" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="6" width="14" height="16" rx="2"/><path d="M8 6V3q0-1 2-1h10q1 0 1 2v13q0 1-2 1h-2"/></svg>
                    {{ $option['action'] }}
                </button>
                <output class="gift-feedback" aria-live="polite"></output>
                @if ($option['number'])
                    <noscript>{{ $option['number'] }}</noscript>
                @endif
            </article>
        @endforeach
    </div>
</section>
<script>
    (() => {
        const panel = document.getElementById('gift-options');
        const toggle = document.querySelector('.gift-toggle');
        toggle.hidden = false;
        toggle.addEventListener('click', () => {
            panel.hidden = !panel.hidden;
            toggle.setAttribute('aria-expanded', String(!panel.hidden));
            toggle.querySelector('span').textContent = panel.hidden ? 'Lihat pilihan' : 'Tutup pilihan';
        });
        panel.querySelectorAll('.gift-copy:enabled').forEach(button => {
            button.addEventListener('click', async () => {
                const feedback = button.nextElementSibling;
                try {
                    await navigator.clipboard.writeText(button.dataset.number);
                    feedback.textContent = 'Nomor berhasil disalin.';
                } catch {
                    feedback.textContent = 'Salin manual: ' + button.dataset.number;
                }
            });
        });
    })();
</script>
@endif
