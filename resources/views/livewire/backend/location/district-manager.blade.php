<div x-data="{ open: @entangle('isModalOpen'), isDeleteModalOpen: @entangle('isDeleteModalOpen') }">
    <div class="rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-5 py-4 sm:px-6 sm:py-5 flex flex-col md:flex-row justify-between items-center gap-3">
            <h2 class="text-xl font-bold">Districts</h2>
            <div class="flex items-center gap-3">
                <x-inputs.input type="text" wire:model.live="search" placeholder="Search Districts..." />
                <x-buttons.button wire:click="create" variant="primary">
                    Create District
                </x-buttons.button>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-5 mb-4"
                role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table">
                <thead class="table-thead">
                    <tr class="table-tr">
                        <th class="table-thead-th">Division</th>
                        <th class="table-thead-th">Name</th>
                        <th class="table-thead-th">BN Name</th>
                        <th class="table-thead-th text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($districts as $district)
                        <tr class="table-tr">
                            <td class="table-td">{{ $district->division->name ?? 'N/A' }}</td>
                            <td class="table-td">{{ $district->name }}</td>
                            <td class="table-td">{{ $district->bn_name }}</td>
                            <td class="table-td text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <x-buttons.button wire:click="edit({{ $district->id }})" variant="info"
                                        size="sm">Edit</x-buttons.button>
                                    <x-buttons.button wire:click="confirmDelete({{ $district->id }})" variant="danger"
                                        size="sm">Delete</x-buttons.button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="my-4 px-4 sm:px-6">
                {{ $districts->links() }}
            </div>
        </div>
    </div>

    <!-- Modal -->
    <x-modal.modal wire:model="isModalOpen">
        <x-slot name="header">
            {{ $districtId ? 'Edit District' : 'Create District' }}
        </x-slot>

        <div class="space-y-4">
            <div wire:key="division-select-{{ $districtId ?? 'create' }}">
                <x-inputs.combobox name="division_id" label="Division" :options="$divisions->pluck('name', 'id')" :selected="$division_id"
                    searchable="true" x-on:combobox-change="$wire.set('division_id', $event.detail.value)" required />
                @error('division_id')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <x-inputs.input label="Name" name="name" wire:model="name" required />
            @error('name')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror

            <x-inputs.input label="BN Name" name="bn_name" wire:model="bn_name" required />
            @error('bn_name')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <x-inputs.input label="Latitude" name="lat" wire:model="lat" />
                    @error('lat')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="space-y-1">
                    <x-inputs.input label="Longitude" name="lon" wire:model="lon" />
                    @error('lon')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <x-inputs.input label="URL" name="url" wire:model="url" />
            @error('url')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <x-slot name="footer">
            <x-buttons.button wire:click="closeModal" variant="secondary">Cancel</x-buttons.button>
            <x-buttons.button wire:click="store" variant="primary">Save</x-buttons.button>
        </x-slot>
    </x-modal.modal>

    <!-- Delete Confirmation Modal -->
    <x-modals.confirm-delete wire:model.live="isDeleteModalOpen" modalTrigger="isDeleteModalOpen"
        title="Delete District" content="Are you sure you want to delete this District? This action cannot be undone."
        wireClick="delete" />
</div>
