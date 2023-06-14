<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //

        Filament::serving(function () {
            \Filament\Tables\Columns\ToggleColumn::macro('toggle', function () {
                $this->action(function ($record, $column) {
                    dump($record, $column);
                });
                return $this;
            });
        });
    }
}
