<?php


namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Manajemen User';
    protected static ?string $navigationGroup = 'Admin Permission';
    

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->hasRole('super_admin');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('super_admin');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema(function (?User $record) {
                $isOwnerOrSuperAdmin = $record?->hasRole('owner') || $record?->hasRole('super_admin');
                $defaultRole = $record?->roles->first()?->name;

                return [
                    TextInput::make('name')
                        ->label('Nama')
                        ->disabled()
                        ->required(),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->disabled()
                        ->required(),

                    TextInput::make('no_whatsapp')
                        ->label('Kontak')
                        ->disabled()
                        ->required(),

                    TextInput::make('alamat')
                        ->label('Alamat')
                        ->disabled()
                        ->required(),

                    Toggle::make('is_active')
                        ->label('Aktif')
                        ->onColor('success')
                        ->offColor('danger'),

                    Select::make('role')
                        ->label('Role')
                        ->options([
                            'clinic_employee' => 'Karyawan Klinik',
                            'petshop_employee' => 'Karyawan Petshop',
                            'doctor' => 'Dokter Hewan',
                            'manager' => 'Manajer',
                            'customer' => 'Pelanggan',
                        ])
                        ->searchable()
                        ->preload()
                        ->required()
                        ->dehydrated(false)
                        ->disabled($isOwnerOrSuperAdmin)
                        ->afterStateHydrated(function (callable $set, $state, ?User $record) {
                            if ($record) {
                                $currentRole = $record->roles->first()?->name;
                                if ($currentRole) {
                                    $set('role', $currentRole);
                                }
                            }
                        }),
                ];
            });
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                BooleanColumn::make('is_active')
                    ->label('Aktif')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                    Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role Aktif')
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        $roleLabels = [
                            'super_admin'     => 'Super Admin',
                            'owner'           => 'Owner',
                            'manager'         => 'Manajer',
                            'docter'          => 'Dokter Hewan',
                            'petshop_employee'=> 'Karyawan Petshop',
                            'clinic_employee' => 'Karyawan Klinik',
                            'customer' => 'Pelanggan',
                        ];

                        return collect($state)->map(function ($role) use ($roleLabels) {
                            return $roleLabels[$role] ?? Str::of($role)->replace('_', ' ')->title();
                        })->implode(', ');
                    }),

              Tables\Columns\TextColumn::make('created_at')
                ->label('Dibuat')
                ->sortable()
                ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('j/n/y')),


            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (User $record) => !$record->hasRole('owner') && !$record->hasRole('super_admin')),
            ])
            ->bulkActions([])
            ->searchPlaceholder('Cari');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('roles');
    }

    public static function getModelLabel(): string
    {
        return 'Manajemen User';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Manajemen User';
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'edit'  => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
