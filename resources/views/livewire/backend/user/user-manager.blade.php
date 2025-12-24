<div>
    <div class="rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-5 py-4 sm:px-6 sm:py-5 flex flex-col md:flex-row justify-between items-center gap-3">
            <h2 class="text-xl font-bold">Users</h2>
            <div class="flex items-center gap-3">
                <x-inputs.input type="text" wire:model.live="search" placeholder="Search Users..." />
                <x-buttons.button wire:click="create" variant="primary">
                    Create User
                </x-buttons.button>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-5 mb-4"
                role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mx-5 mb-4"
                role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table">
                <thead class="table-thead">
                    <tr class="table-tr">
                        <th class="table-thead-th">Name</th>
                        <th class="table-thead-th">Email</th>
                        <th class="table-thead-th">Roles</th>
                        <th class="table-thead-th text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="table-tr">
                            <td class="table-td">
                                <div class="flex items-center">
                                    <div class="ml-3">
                                        <p class="text-gray-900 whitespace-no-wrap font-semibold">
                                            {{ $user->full_name }}
                                        </p>
                                        <p class="text-gray-500 whitespace-no-wrap text-xs">{{ $user->username }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="table-td">
                                {{ $user->email }}
                            </td>
                            <td class="table-td">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($user->roles as $role)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="table-td text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <x-buttons.button wire:click="edit({{ $user->id }})" variant="info"
                                        size="sm">Edit</x-buttons.button>
                                    <x-buttons.button wire:click="delete({{ $user->id }})" variant="danger"
                                        size="sm"
                                        onclick="return confirm('Are you sure?') || event.stopImmediatePropagation()">Delete</x-buttons.button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="my-4 px-4 sm:px-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Modal -->
    <x-modal.modal wire:model="isModalOpen">
        <x-slot name="header">
            {{ $userId ? 'Edit User' : 'Create User' }}
        </x-slot>

        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <x-inputs.input label="First Name" name="first_name" wire:model="first_name" required />
                    @error('first_name')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="space-y-1">
                    <x-inputs.input label="Last Name" name="last_name" wire:model="last_name" required />
                    @error('last_name')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <x-inputs.input label="Email" type="email" name="email" wire:model="email" required />
            @error('email')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror

            <p class="mt-1 text-sm text-gray-500">Hold Ctrl (Windows) or Command (Mac) to select multiple roles.</p>
            @error('selectedRoles')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
</div>

<x-slot name="footer">
    <x-buttons.button wire:click="closeModal" variant="secondary">Cancel</x-buttons.button>
    <x-buttons.button wire:click="store" variant="primary">Save</x-buttons.button>
</x-slot>
</x-modal.modal>
</div>
