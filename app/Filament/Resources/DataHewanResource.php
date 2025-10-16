<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataHewanResource\Pages;
use App\Filament\Resources\DataHewanResource\RelationManagers\RiwayatKesehatanRelationManager;
use App\Models\DataHewan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class DataHewanResource extends Resource
{
    protected static ?string $model = DataHewan::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Data Hewan';
    protected static ?string $navigationGroup = 'Klinik';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['docter', 'clinic_employee']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole(['docter', 'clinic_employee']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_hewan')
                ->label('Nama Hewan')
                ->required(),
            
            Forms\Components\TextInput::make('name')
                ->label('Nama Pemilik')
                ->formatStateUsing(fn ($record) => $record?->user?->name)
                ->disabled()
                ->dehydrated(false),


            Forms\Components\TextInput::make('jenis_hewan')
                ->label('Jenis Hewan')
                ->required(),

            Forms\Components\TextInput::make('ras')
                ->label('Ras')
                ->nullable(),

            Forms\Components\Select::make('jenis_kelamin')
                ->label('Jenis Kelamin')
                ->options([
                    'jantan' => 'Jantan',
                    'betina' => 'Betina',
                    'Jantan' => 'Jantan',
                    'Betina' => 'Betina',
                ])
                ->required(),

            Forms\Components\TextInput::make('umur')
                ->label('Umur')
                ->nullable(),

            Forms\Components\TextInput::make('no_whatsapp')
                ->label('Nomor WhatsApp')
                ->formatStateUsing(fn ($record) => $record?->user?->no_whatsapp)
                ->disabled()
                ->dehydrated(false),


            Forms\Components\TextInput::make('alamat')
                ->label('Alamat')
                 ->formatStateUsing(fn ($record) => $record?->user?->alamat)
                ->disabled()
                ->dehydrated(false),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_hewan')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('jenis_hewan'),
                Tables\Columns\TextColumn::make('user.name') 
                ->label('Nama Pemilik')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('ras'),
                Tables\Columns\TextColumn::make('jenis_kelamin'),
                Tables\Columns\TextColumn::make('umur'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat'),
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->searchPlaceholder('Cari');
            // ->bulkActions([
            //     Tables\Actions\DeleteBulkAction::make(),
    }

    public static function getRelations(): array
    {
        return [
            RiwayatKesehatanRelationManager::class,
        ];
    }

    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('user') // penting agar default() dapat mengambil relasi user
            ->whereHas('user.roles', function ($query) {
                $query->where('name', 'customer');
            });
    }


    public static function getModelLabel(): string
    {
        return 'Data Hewan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Hewan';
    }

     // Ini untuk redirect setelah tombol "Save"
    protected function getRedirectUrl(): string
    {
        return DataHewanResource::getUrl();
    }

    // Ini untuk redirect setelah notifikasi sukses tampil
    protected function getSavedNotificationRedirectUrl(): ?string
    {
        return DataHewanResource::getUrl(); // arahkan ke List
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDataHewans::route('/'),
            'edit' => Pages\EditDataHewan::route('/{record}/edit'),
            'view' => Pages\ViewDataHewan::route('/{record}'),
        ];
    }
}
