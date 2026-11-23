<section id="rsvp" class="guestbook" aria-labelledby="guestbook-title">
    <header>
        <h2 id="guestbook-title">Konfirmasi Kehadiran &amp; Ucapan</h2>
        <div class="guestbook-divider" aria-hidden="true"><img src="{{ asset('assets/wedding/event-floral-divider.webp') }}" alt="" width="128" height="46"></div>
        @if(filled($teks['pengantar_rsvp']))
            <p>{{ $teks['pengantar_rsvp'] }}</p>
        @endif
    </header>

    <form class="guestbook-form" action="{{ route('rsvp.store') }}" method="post">
        @csrf
        <input type="hidden" name="acara_id" value="{{ $akad->id }}">
        <input type="hidden" name="form_section" value="rsvp">
        <input type="hidden" name="submission_id" value="{{ old('submission_id', (string) \Illuminate\Support\Str::uuid()) }}">
        <div class="guestbook-fields">
            <div>
                <label for="guest-name">Nama tamu</label>
                <input id="guest-name" name="nama" placeholder="Nama Anda" autocomplete="name" required maxlength="255" value="{{ old('nama') }}" aria-describedby="err-nama">
                <small id="err-nama">{{ $errors->first('nama') }}</small>
            </div>
            <div>
                <label for="guest-count">Jumlah tamu</label>
                <select name="jumlah_hadir" id="guest-count" required aria-describedby="err-jumlah_hadir">
                    @foreach(range(1,20) as $count)
                        <option value="{{ $count }}" @selected(old('jumlah_hadir',1) == $count)>{{ $count }} orang</option>
                    @endforeach
                </select>
                <small id="err-jumlah_hadir">{{ $errors->first('jumlah_hadir') }}</small>
            </div>
        </div>
        <fieldset class="guestbook-attendance">
            <legend>Kehadiran</legend>
            @foreach(['hadir' => 'Hadir', 'tidak_hadir' => 'Berhalangan hadir'] as $value => $label)
                <label><input type="radio" name="status" value="{{ $value }}" @checked(old('status','hadir') === $value) required aria-describedby="err-status"> {{ $label }}</label>
            @endforeach
        </fieldset>
        <small id="err-status">{{ $errors->first('status') }}</small>
        <label for="guest-message">Ucapan &amp; doa <span>(opsional)</span></label>
        <textarea id="guest-message" name="pesan" maxlength="1000" placeholder="Tulis pesan untuk {{ $namaPria }} dan {{ $namaWanita }}..." aria-describedby="guest-hint err-pesan">{{ old('pesan') }}</textarea>
        <small id="err-pesan">{{ $errors->first('pesan') }}</small>
        <p id="guest-hint">Ucapan Anda akan tampil di buku tamu.</p>
        <button type="submit">Kirim</button>
        <p class="guestbook-feedback" role="status" aria-live="polite">{{ session('success') ?: (session('error') ?: $errors->first('form')) }}</p>
    </form>

    <section id="ucapan" class="guestbook-entries" aria-labelledby="wishes-title">
        <h3 id="wishes-title">Buku Tamu</h3>
        @if(session('success_ucapan'))<p role="status">{{ session('success_ucapan') }}</p>@endif
        <div class="guestbook-list">
            @forelse($ucapans as $ucapan)
                <article><h4>{{ $ucapan->tamu?->nama ?? 'Tamu Undangan' }}</h4><p>{{ $ucapan->pesan }}</p></article>
            @empty
                <p class="guestbook-empty">Belum ada ucapan dan doa.</p>
            @endforelse
        </div>
        @if($ucapans->hasPages())
            <nav aria-label="Halaman buku tamu">
                @if($ucapans->previousPageUrl())<a href="{{ $ucapans->previousPageUrl() }}">Sebelumnya</a>@endif
                @if($ucapans->nextPageUrl())<a href="{{ $ucapans->nextPageUrl() }}">Berikutnya</a>@endif
            </nav>
        @endif
    </section>
</section>
<script type="module">
    const form = document.querySelector('.guestbook-form');
    const button = form.querySelector('button');
    const feedback = form.querySelector('[role="status"]');
    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (button.disabled) return;
        button.disabled = true;
        button.textContent = 'Mengirim…';
        feedback.textContent = '';
        form.querySelectorAll('small').forEach(node => node.textContent = '');
        form.querySelectorAll('[aria-invalid]').forEach(node => node.removeAttribute('aria-invalid'));
        try {
            const response = await fetch(form.action, {method: 'POST', body: new FormData(form), headers: {Accept: 'application/json'}});
            if (response.status === 422) {
                const {errors} = await response.json();
                for (const [name, messages] of Object.entries(errors)) {
                    const error = document.getElementById('err-' + name);
                    (error || feedback).textContent = messages[0];
                    form.querySelectorAll('[name="' + CSS.escape(name) + '"]').forEach(node => node.setAttribute('aria-invalid','true'));
                }
                form.querySelector('[aria-invalid="true"]:not([type="hidden"])')?.focus();
                return;
            }
            if (!response.ok) {
                feedback.textContent = response.status === 429 ? 'Terlalu banyak pengiriman. Coba lagi dalam satu menit.' : 'Konfirmasi belum berhasil dikirim. Silakan coba lagi.';
                return;
            }
            const result = await response.json();
            form.reset();
            form.elements.nama.value = form.elements.pesan.value = '';
            form.elements.status.value = 'hadir';
            form.elements.jumlah_hadir.value = '1';
            form.elements.submission_id.value = result.submission_id;
            feedback.textContent = result.message;
        } catch {
            feedback.textContent = 'Koneksi terputus. Silakan kirim kembali.';
        } finally {
            button.disabled = false;
            button.textContent = 'Kirim';
        }
    });
</script>
