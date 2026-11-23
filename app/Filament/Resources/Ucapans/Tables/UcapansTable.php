<?php

namespace App\Filament\Resources\Ucapans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UcapansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tamu.nama')
                    ->label('Nama tamu')->wrap()->searchable()->sortable(),
                TextColumn::make('pesan')
                    ->label('Ucapan')->wrap()->extraAttributes(['class' => 'wedding-greeting']),
                TextColumn::make('is_approved')
                    ->label('Moderasi')->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Disetujui' : 'Menunggu')
                    ->color(fn ($state) => $state ? 'success' : 'warning'),
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
