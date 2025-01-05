<?php

namespace App\Livewire;

use Livewire\Component;
use Mary\Traits\Toast;
use Native\Laravel\Dialog;

class Home extends Component
{
    use Toast;

    public function openDirectory()
    {
        $directoryPath = Dialog::new()->folders()->open();

        if (!$directoryPath) {
            $this->error(
                title: 'No Directory Selected',
                position: 'toast-bottom toast-end',
            );

            return;
        }

        $this->success(
            title: 'Opened Directory: ' . str($directoryPath)->explode(DIRECTORY_SEPARATOR)->last(),
            position: 'toast-bottom toast-end',
        );

        return $this->redirect(route('app', ['path' => str_replace(DIRECTORY_SEPARATOR, '_', $directoryPath)]), navigate: true);
    }

    public function render()
    {
        return view('livewire.home');
    }
}
