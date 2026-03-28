<?php

namespace Coderflex\FilamentTurnstile\Tests\Fixtures;

use Coderflex\FilamentTurnstile\Forms\Components\Turnstile;
use Coderflex\FilamentTurnstile\Tests\Models\Contact;
use Filament\Forms;
use Filament\Forms\FormsComponent;
use Filament\Schemas\Schema;
use Illuminate\Validation\ValidationException;

class ContactUs extends FormsComponent
{
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Name')
                ->required(),
            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->required(),
            Forms\Components\TextInput::make('content')
                ->label('Content')
                ->required(),
            Turnstile::make('cf-captcha')
                ->theme('auto'),
        ])
            ->statePath('data')
            ->model(Contact::class);
    }

    // Filament 3 compatibility: getForms() is used by InteractsWithForms
    protected function getForms(): array
    {
        return [
            'form' => $this->form($this->makeForm()),
        ];
    }

    public function send()
    {
        Contact::create($this->form->getState());
    }

    public function render()
    {
        return 'fixtures.contact-us';
    }

    protected function onValidationError(ValidationException $exception): void
    {
        $this->dispatch('reset-captcha');
    }
}
