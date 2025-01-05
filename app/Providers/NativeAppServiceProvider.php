<?php

namespace App\Providers;

use Native\Laravel\Facades\Window;
use Native\Laravel\Contracts\ProvidesPhpIni;
use Native\Laravel\Facades\Menu;
use Native\Laravel\Facades\MenuBar;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        Window::open()
            ->minWidth(800)
            ->minHeight(800)
            ->maximized()
            ->title('Renamer')
            ->route('home')
            ->rememberState();

        Menu::create(
            Menu::make(
                Menu::route('home', 'Open New Directory')->hotkey('Ctrl+N'),
                Menu::separator(),
                Menu::quit(),
            )->label('File')
        );
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [];
    }
}
