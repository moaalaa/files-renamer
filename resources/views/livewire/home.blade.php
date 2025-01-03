<div>
    <div class="flex h-screen items-center justify-center">

        <div class="h-full w-1/4 bg-base-300/50 p-4 dark:bg-base-300">
            @foreach ($this->directories as $directory)
                <x-mary-list-item :item="$directory" value="name" sub-value="path">

                    <x-slot:actions>
                        <x-mary-button
                            icon="{{ $this->openedPath === $directory['pathSafe'] ? 'o-folder-open' : 'o-folder-plus' }}"
                            class="{{ $this->openedPath === $directory['pathSafe'] ? 'text-primary' : '' }}"
                            wire:click="openDirectory('{!! $directory['pathSafe'] !!}')" spinner />
                    </x-slot:actions>
                </x-mary-list-item>
            @endforeach
        </div>

        <div class="h-full w-3/4 p-4">

            <div class="overflow-x-auto">
                <table class="table">
                    <!-- head -->
                    <thead>
                        <tr>
                            <th class="w-1/2">Original</th>
                            <th class="w-1/2">Renamed</th>
                        </tr>
                    </thead>

                    <tbody wire:sortable="reorderFiles">

                        @foreach ($this->files as $index => $file)
                            <tr wire:sortable.item="{{ $file['original_name'] }}"
                                wire:key="file-{{ $file['original_name'] }}"
                                class="hover:bg-base-300/50 dark:hover:bg-base-300">
                                <td>
                                    <x-mary-icon name="o-bars-3" />

                                    {{ $file['original_name'] }}
                                    <x-mary-badge :value="$file['extension']" class="badge-primary" />
                                </td>
                                <td>
                                    {{ $file['new_name'] }}
                                    <x-mary-badge :value="$file['extension']" class="badge-primary" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
