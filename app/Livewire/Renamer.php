<?php

namespace App\Livewire;

use Illuminate\Support\Facades\File;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Mary\Traits\Toast;
use Symfony\Component\Finder\SplFileInfo;

class Renamer extends Component
{
    use Toast;

    #[Url(as: 'path')]
    public ?string $encryptedPath = null;

    public ?string $targetDirectory = null;

    public ?string $openedPath = null;

    public ?string $directoryName = null;

    public int $startFrom = 1;

    public string $directorySeparator;

    public array $customOrder = []; // Store custom order

    public function mount()
    {
        $this->targetDirectory = str_replace('_', DIRECTORY_SEPARATOR, $this->encryptedPath);

        if (!$this->targetDirectory) {
            $this->error(
                title: 'No Directory Selected',
                position: 'toast-bottom toast-end',
            );

            return $this->redirect(route('home'), navigate: true);
        }

        $this->directorySeparator = DIRECTORY_SEPARATOR;
    }

    #[Computed]
    public function directories()
    {
        $directories = File::directories($this->targetDirectory);

        return collect($directories)->map(fn ($directory) => [
            'name'     => basename($directory),
            'path'     => $directory,
            'pathSafe' => str_replace($this->directorySeparator, '_', $directory),
        ])->toArray();
    }

    #[Computed]
    public function files()
    {
        if (!$this->openedPath) return [];

        $directory = str_replace('_', $this->directorySeparator, $this->openedPath);

        if (!$this->directoryName) {
            $this->directoryName = str(basename($directory))->title();
        }

        if (!$this->directoryName) return [];

        /** @var SplFileInfo[] */
        $files = File::files($directory);

        // Define a regex pattern to extract episode numbers
        $episodePattern = '/(?:\s+|_)?(?:EP)?(?:\s+|_)?(\d+)/i';

        // Sort files by episode number or custom order
        $sortedFiles = collect($files)
            ->sort(function ($a, $b) use ($episodePattern) {
                preg_match($episodePattern, $a->getFilename(), $matchesA);
                preg_match($episodePattern, $b->getFilename(), $matchesB);

                $episodeA = isset($matchesA[1]) ? (int) $matchesA[1] : PHP_INT_MAX;
                $episodeB = isset($matchesB[1]) ? (int) $matchesB[1] : PHP_INT_MAX;

                return $episodeA - $episodeB;
            })->values();

        // Apply custom order if available
        if ((bool) $this->customOrder) {
            $fileMap = $sortedFiles->keyBy(fn ($file) => $file->getFilenameWithoutExtension());

            $sortedFiles = collect($this->customOrder)
                ->map(fn (array $filename) => $fileMap->get($filename['value']))
                ->filter()
                ->values();
        }

        // Rename files
        return $sortedFiles
            ->map(function (SplFileInfo $file, $index) use ($sortedFiles) {
                $fileNumber = str_pad($index + $this->startFrom, 2, '0', STR_PAD_LEFT);
                $isLastFile = $index === count($sortedFiles) - 1;

                $newFileName = $this->directoryName . ' - ' . $fileNumber;
                if ($isLastFile) {
                    $newFileName .= ' END';
                }

                return [
                    'order'          => $index + 1,
                    'original_name'  => $file->getFilenameWithoutExtension(),
                    'extension'      => $file->getExtension(),
                    'path'           => $file->getPathname(),
                    'directory_path' => dirname($file->getPathname()),
                    'new_name'       => $newFileName,
                ];
            })
            ->toArray();
    }

    public function reorderFiles($order)
    {
        $this->customOrder = $order; // Update custom order
    }

    public function openDirectory($path)
    {
        $this->openedPath = $path;
        $this->directoryName = null;
        $this->customOrder = []; // Reset custom order
    }

    public function rename()
    {
        if (!$this->openedPath) {
            $this->error(
                title: 'No Directory Opened',
                position: 'toast-bottom toast-end',
            );

            return;
        }

        if (!$this->files) {
            $this->error(
                title: 'No Files',
                position: 'toast-bottom toast-end',
            );

            return;
        }

        try {
            foreach ($this->files as $file) {
                File::move($file['path'], $file['directory_path'] . DIRECTORY_SEPARATOR . $file['new_name'] . '.' . $file['extension']);
            }

            $this->success(
                title: 'Renamed',
                position: 'toast-bottom toast-end',
            );

            unset($this->files);

            $this->reset(['startFrom', 'directoryName']);
        } catch (\Exception $e) {
            $this->error(
                title: 'Something went wrong',
                position: 'toast-bottom toast-end',
            );
        }
    }

    public function render()
    {
        return view('livewire.renamer');
    }
}
