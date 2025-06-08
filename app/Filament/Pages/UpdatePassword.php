<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;

class UpdatePassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.update-password';
    protected static ?string $slug = 'update-password';
    protected static ?string $title = 'Update Password';
    protected static bool $shouldRegisterNavigation = false;

    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public function mount(): void
    {
        $this->form->fill(); // penting untuk render form
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('current_password')
                ->label('Password Saat Ini')
                ->password()
                ->revealable()
                ->required(),

            Forms\Components\TextInput::make('new_password')
                ->label('Password Baru')
                ->password()
                ->required()
                ->revealable()
                ->minLength(8),

            Forms\Components\TextInput::make('new_password_confirmation')
                ->label('Konfirmasi Password Baru')
                ->password()
                ->same('new_password')
                ->revealable()
                ->required(),
        ];
    }

    public function submit()
    {
        $user = auth()->user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->resetErrorBag();
            $this->addError('current_password', 'Current password is incorrect');
            return;
        }

        if ($this->new_password == $this->current_password) {
            $this->resetErrorBag();
            $this->addError('new_password', 'New password is current password now');
            return;
        }

        if ($this->new_password !== $this->new_password_confirmation) {
            $this->resetErrorBag();
            $this->addError('new_password_confirmation', 'Confimation password is incorrect');
            return;
        }

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        auth()->logout();

        Notification::make()
        ->title('Password berhasil diubah. Silakan login ulang.')
        ->success()
        ->send();

        // $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        redirect()->route('filament.admin.auth.login');
    }
}
