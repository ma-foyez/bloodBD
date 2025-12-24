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
            <x-inputs.input label="Name" name="name" wire:model="name" required />
            @error('name')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <x-inputs.input label="Email" type="email" name="email" wire:model="email" required />
                    @error('email')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="space-y-1">
                    <x-inputs.input label="Username" name="username" wire:model="username" required />
                    @error('username')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <x-inputs.input label="Mobile" name="mobile" wire:model="mobile" />
                    @error('mobile')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="space-y-1">
                    <x-inputs.input label="Date of Birth" type="date" name="dob" wire:model="dob" />
                    @error('dob')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Blood Group</label>
                    <select wire:model="blood_group"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Select Blood Group</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                    @error('blood_group')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="space-y-1">
                    <x-inputs.input label="Occupation" name="occupation" wire:model="occupation" />
                    @error('occupation')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="is_weight_50kg"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Weight is 50kg or more</span>
                    </label>
                </div>
                <div class="space-y-1">
                    <x-inputs.input label="Last Donation Date" type="date" name="last_donation"
                        wire:model="last_donation" />
                    @error('last_donation')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Address Section -->
            <div class="border-t pt-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Address Information</h3>

                <div wire:key="division-select-{{ $userId ?? 'create' }}">
                    <x-inputs.combobox name="division_id" label="Division" :options="$divisions->pluck('name', 'id')" :selected="$division_id"
                        searchable="true" x-on:combobox-change="$wire.set('division_id', $event.detail.value)"
                        placeholder="Select Division" />
                </div>

                <div wire:key="district-select-{{ $division_id }}" class="mt-3">
                    <x-inputs.combobox name="district_id" label="District" :options="$districts ? collect($districts)->pluck('name', 'id') : []" :selected="$district_id"
                        searchable="true" x-on:combobox-change="$wire.set('district_id', $event.detail.value)"
                        :placeholder="$division_id ? 'Select District' : 'Select Division first'" />
                </div>

                <div wire:key="area-select-{{ $district_id }}" class="mt-3">
                    <x-inputs.combobox name="area_id" label="Area" :options="$areas ? collect($areas)->pluck('name', 'id') : []" :selected="$area_id"
                        searchable="true" x-on:combobox-change="$wire.set('area_id', $event.detail.value)"
                        :placeholder="$district_id ? 'Select Area' : 'Select District first'" />
                </div>

                <div class="mt-3">
                    <x-inputs.input label="Post Office" name="post_office" wire:model="post_office" />
                    @error('post_office')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Roles Section -->
            <div class="border-t pt-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Role Assignment</h3>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Roles</label>
                <select wire:model="selectedRoles" multiple
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    size="5">
                    @foreach ($roles as $roleValue => $roleLabel)
                        <option value="{{ $roleValue }}">{{ $roleLabel }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-sm text-gray-500">Hold Ctrl (Windows) or Command (Mac) to select multiple roles.
                </p>
                @error('selectedRoles')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password Section (Only for Create/Edit with password change) -->
            @if (!$userId)
                <div class="border-t pt-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Password</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <x-inputs.password label="Password" name="password" wire:model="password" />
                            @error('password')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="space-y-1">
                            <x-inputs.password label="Confirm Password" name="password_confirmation"
                                wire:model="password_confirmation" />
                            @error('password_confirmation')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            @else
                <div class="border-t pt-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Change Password (Optional)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <x-inputs.password label="New Password" name="password" wire:model="password" />
                            @error('password')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="space-y-1">
                            <x-inputs.password label="Confirm New Password" name="password_confirmation"
                                wire:model="password_confirmation" />
                            @error('password_confirmation')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <x-slot name="footer">
            <x-buttons.button wire:click="closeModal" variant="secondary">Cancel</x-buttons.button>
            <x-buttons.button wire:click="store" variant="primary">Save</x-buttons.button>
        </x-slot>
    </x-modal.modal>
</div>
