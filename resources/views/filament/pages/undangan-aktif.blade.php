<x-filament-panels::page>
    <x-filament::section>
        <form wire:submit="simpan" class="grid gap-6">
            <label for="active-couple">Pasangan</label>
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="pasangan" id="active-couple" required>
                    <option value="">Pilih pasangan</option>
                    @foreach($pasangans as $item)
                        <option value="{{ $item->id }}">{{ $item->nama_pria }} &amp; {{ $item->nama_wanita }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
            @error('pasangan') <p role="alert">{{ $message }}</p> @enderror
            <label for="active-event">Acara utama</label>
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model="acara" id="active-event" wire:key="events-{{ $pasangan }}" required>
                    <option value="">Pilih acara utama</option>
                    @foreach($acaras as $item)
                        <option value="{{ $item->id }}">{{ $item->nama_acara }} · {{ $item->tanggal }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
            @error('acara') <p role="alert">{{ $message }}</p> @enderror
            <div><x-filament::button type="submit">Simpan undangan aktif</x-filament::button></div>
        </form>
    </x-filament::section>
</x-filament-panels::page>
