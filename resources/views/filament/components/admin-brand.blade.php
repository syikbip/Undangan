@php($name = filament()->getBrandName())
<div class="wedding-brand" aria-label="{{ $name }}">
    @if ($name === 'Admin Undangan')
        <x-filament::icon icon="heroicon-o-envelope" />
    @else
        <span aria-hidden="true">{{ collect(explode(' & ', $name))->map(fn ($part) => mb_strtoupper(mb_substr(trim($part), 0, 1)))->implode('') }}</span>
    @endif
    <strong>Admin Undangan</strong>
</div>
