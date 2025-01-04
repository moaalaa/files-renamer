<div class="align-items-start flex h-screen">
    <!-- Left Sidebar -->
    <div id="left-sidebar"
        class="h-screen w-1/4 overflow-y-auto bg-base-300/50 px-4 scrollbar-thin scrollbar-track-base-300/50 scrollbar-thumb-base-100/50 dark:bg-base-300 dark:scrollbar-track-base-300 dark:scrollbar-thumb-base-100">
        <!-- Scrollable list -->
        <div class="pb-24 pt-3">
            <div class="divider">Begins of Directories</div>

            @foreach ($this->directories as $directory)
                <x-mary-list-item :item="$directory" value="name" sub-value="path">
                    <x-slot:actions>
                        <x-mary-button
                            icon="{{ $this->openedPath === $directory['pathSafe'] ? 'o-folder-open' : 'o-folder-plus' }}"
                            class="{{ $this->openedPath === $directory['pathSafe'] ? 'text-primary' : '' }} hover:scale-125"
                            wire:click="openDirectory('{!! $directory['pathSafe'] !!}')" spinner />
                    </x-slot:actions>
                </x-mary-list-item>
                @if (!$loop->last)
                    <div class="divider"></div>
                @endif
            @endforeach

            <div class="divider">End of Directories</div>
        </div>

    </div>

    <!-- Right Content -->
    <div id="right-content"
        class="relative h-[calc(100vh-12rem)] w-3/4 overflow-y-auto overflow-x-hidden p-4 scrollbar-thin scrollbar-track-base-300/50 scrollbar-thumb-base-100/50 dark:scrollbar-track-base-300 dark:scrollbar-thumb-base-100">
        <table class="table">
            <!-- Table head -->
            <thead>
                <tr>
                    <th>
                        <x-mary-icon name="o-arrows-up-down" />
                    </th>
                    <th class="w-1/2">Original</th>
                    <th class="w-1/2">Renamed</th>
                </tr>
            </thead>
            <!-- Table body -->
            <tbody wire:sortable="reorderFiles">
                @foreach ($this->files as $index => $file)
                    <tr wire:sortable.item="{{ $file['original_name'] }}" wire:key="file-{{ $file['original_name'] }}"
                        class="hover:bg-base-300/50 dark:hover:bg-base-300">
                        <td>
                            <x-mary-icon name="o-bars-3" />
                        </td>
                        <td>
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

        @if (!!$this->files)
            <!-- Fixed button at the bottom -->
            <div class="fixed bottom-0 right-5 flex w-full items-center justify-end space-x-2 pb-5">

                <x-mary-input label="Directory Name" wire:model.live="directoryName" class="w-full" inline />
                <x-mary-input label="Start From" wire:model.live="startFrom" class="w-full" inline />

                <x-mary-popover>
                    <x-slot:trigger>
                        <x-mary-button class="btn-circle btn-primary btn-lg" icon="o-pencil" wire:click="rename"
                            spinner />
                    </x-slot:trigger>
                    <x-slot:content>
                        Rename
                    </x-slot:content>
                </x-mary-popover>
            </div>
        @endif
    </div>

</div>
