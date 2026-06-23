<?php

namespace Coderflex\FilamentTurnstile\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Coderflex\FilamentTurnstile\FilamentTurnstileServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\Facades\Filament;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    // protected $enablesPackageDiscoveries = true;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Coderflex\\FilamentTurnstile\\Tests\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        config()->set('app.key', '6rE9Nz59bGRbeMATftriyQjrpF7DcOQm');

        $this->setCurrentFilamentPanel();
    }

    protected function getPackageProviders($app)
    {
        $providers = [
            ActionsServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            FilamentTurnstileServiceProvider::class,
            TurnstilePanelProvider::class,
        ];

        // Filament 3 uses ryangjchandler/blade-capture-directive
        if (class_exists(\RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider::class)) {
            $providers[] = \RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider::class;
        }

        // Filament 4+ includes the schemas package
        if (class_exists(\Filament\Schemas\SchemasServiceProvider::class)) {
            $providers[] = \Filament\Schemas\SchemasServiceProvider::class;
        }

        return $providers;
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $app['config']->set('view.paths', [
            ...$app['config']->get('view.paths'),
            __DIR__.'/resources/views',
        ]);

        $migrations = [
            include __DIR__.'/Database/Migrations/create_users_table.php',
            include __DIR__.'/Database/Migrations/create_contacts_table.php',
        ];

        collect($migrations)->each(
            fn ($migration) => $migration->up()
        );

        // In Filament 3, Filament\Schemas\Schema does not exist.
        // Alias it to Filament\Forms\Form so test fixtures can use Schema type hints
        // and remain compatible with both Filament 3 and Filament 4+.
        if (! class_exists(\Filament\Schemas\Schema::class)) {
            class_alias(\Filament\Forms\Form::class, \Filament\Schemas\Schema::class);
        }
    }

    protected function setCurrentFilamentPanel(): void
    {
        Filament::setCurrentPanel(
            Filament::getPanel('turnstile')
        );
    }
}
