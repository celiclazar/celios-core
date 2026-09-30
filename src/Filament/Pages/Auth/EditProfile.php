<?php

namespace Celios\Core\Filament\Pages\Auth;

use Celios\Core\Filament\Resources\Users\Schemas\UserForm;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    protected static ?string $slug = 'my-profile';
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    public function getMaxContentWidth(): string
    {
        return '5xl';
    }
    public function schema(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->aside()
                    ->components([
                        ...$this->getProfileFormComponents(),
                    ]),

                Section::make('Update Password')
                    ->description('Ensure your account is using a long, random password.')
                    ->aside()
                    ->components([
                        $this->getPasswordComponent(),
                        $this->getPasswordConfirmationComponent(),
                    ]),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->aside()
                    ->components([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('username')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),
                        CuratorPicker::make('avatar')
                            ->label('User Avatar')
                            ->buttonLabel('Select from Library')
                            ->color('primary')
                            ->imageCropAspectRatio('1:1')
                            ->directory('avatars')
                            ->preserveFilenames()
                            ->columnSpanFull(),
                    ]),

                Section::make('Update Password')
                    ->description('Ensure your account is using a long, random password.')
                    ->aside()
                    ->components([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ]),
            ]);
    }

    protected function getProfileFormComponents(): array
    {
        return [
           TextInput::make('name')
                ->required()
                ->maxLength(255),
           TextInput::make('username')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
           TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
           TextInput::make('phone')
                ->tel()
                ->maxLength(20),
        ];
    }
}
