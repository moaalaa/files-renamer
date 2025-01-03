<?php

namespace App\Livewire;

use Illuminate\Support\Facades\File;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Symfony\Component\Finder\SplFileInfo;

class Home extends Component
{
    public ?string $openedPath = null;
    public string $directorySeparator;
    public array $customOrder = []; // Store custom order

    public function mount()
    {
        $this->directorySeparator = DIRECTORY_SEPARATOR;
    }

    #[Computed]
    public function directories()
    {
        $directories = File::directories("D:{$this->directorySeparator}Anime{$this->directorySeparator}New{$this->directorySeparator}");

        return collect($directories)->map(function ($directory) {
            return [
                'name' => basename($directory),
                'path' => $directory,
                'pathSafe' => str_replace($this->directorySeparator, '_', $directory),
            ];
        })->toArray();
    }

    #[Computed]
    public function files()
    {
        if (!$this->openedPath) return [];

        $directory = str_replace('_', $this->directorySeparator, $this->openedPath);
        $directoryName = str(basename($directory))->title();

        /** @var SplFileInfo[] */
        $files = File::files($directory);

        // Define a regex pattern to extract episode numbers
        $episodePattern = '/(?:\s+|_)?(?:EP)?(?:\s+|_)?(\d+)/i';

        // Sort files by episode number or custom order
        $sortedFiles = collect($files)
            ->sort(function ($a, $b) use ($episodePattern) {
                preg_match($episodePattern, $a->getFilename(), $matchesA);
                preg_match($episodePattern, $b->getFilename(), $matchesB);

                $episodeA = isset($matchesA[1]) ? (int)$matchesA[1] : PHP_INT_MAX;
                $episodeB = isset($matchesB[1]) ? (int)$matchesB[1] : PHP_INT_MAX;

                return $episodeA - $episodeB;
            })->values();

        // Apply custom order if available
        if (!! $this->customOrder) {
            $fileMap = $sortedFiles->keyBy(fn($file) => $file->getFilenameWithoutExtension());

            $sortedFiles = collect($this->customOrder)
                ->map(fn(array $filename) => $fileMap->get($filename['value']))
                ->filter()
                ->values();
        }

        // Rename files
        return $sortedFiles
            ->map(function (SplFileInfo $file, $index) use ($directoryName, $sortedFiles) {
                $fileNumber = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                $isLastFile = $index === count($sortedFiles) - 1;

                $newFileName = $directoryName . ' - ' . $fileNumber;
                if ($isLastFile) {
                    $newFileName .= ' END';
                }

                // $newFileName .= '.' . $file->getExtension();

                // Rename the file
                // $newFilePath = $file->getPath() . DIRECTORY_SEPARATOR . $newFileName;
                // File::move($file->getPathname(), $newFilePath);

                return [
                    'order' => $index + 1,
                    'original_name' => $file->getFilenameWithoutExtension(),
                    'extension' => $file->getExtension(),
                    'path' => $file->getPathname(),
                    'new_name' => $newFileName,
                ];
            })->toArray();
    }

    public function reorderFiles($order)
    {
        $this->customOrder = $order; // Update custom order
    }

    public function openDirectory($path)
    {
        $this->openedPath = $path;
        $this->customOrder = []; // Reset custom order
    }

    public function render()
    {
        return view('livewire.home');
    }
}
