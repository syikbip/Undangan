<?php

namespace App\Filament\Resources\Rsvps\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RsvpsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tamu.nama')
                    ->label('Nama tamu')->wrap()->searchable()
                    ->sortable(),
                TextColumn::make('acara.nama_acara')
                    ->label('Acara')->wrap()->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()->formatStateUsing(fn (string $state) => match ($state) {
                        'hadir' => 'Hadir',
                        'tidak_hadir' => 'Berhalangan',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    })->color(fn (string $state) => $state === 'hadir' ? 'success' : 'gray'),
                TextColumn::make('jumlah_hadir')->label('Jumlah orang hadir')->alignEnd()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
