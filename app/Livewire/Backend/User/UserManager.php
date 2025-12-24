<?php

namespace App\Livewire\Backend\User;

use App\Models\User;
use App\Models\Role;
use App\Services\RolesService;
use App\Services\UserService;
use App\Services\LanguageService;
use App\Services\TimezoneService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManager extends Component
{
    use WithPagination;

    public $first_name, $last_name, $email, $username, $password, $password_confirmation;
    public $status = 1;
    public $userId;
    public $selectedRoles = [];
    public $isModalOpen = 0;
    public $search = '';

    // Additional fields from original controller
    public $locale;
    public $timezone;

    public function render()
    {
        $users = User::with('roles')
            ->where('first_name', 'like', '%' . $this->search . '%')
            ->orWhere('last_name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orWhere('username', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.backend.user.user-manager', [
            'users' => $users,
            'roles' => app(RolesService::class)->getRolesDropdown(),
            'locales' => app(LanguageService::class)->getLanguages(),
            'timezones' => app(TimezoneService::class)->getTimezones(),
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetValidation();
    }

    private function resetInputFields()
    {
        $this->first_name = '';
        $this->last_name = '';
        $this->email = '';
        $this->username = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->selectedRoles = [];
        $this->status = 1;
        $this->userId = null;
        $this->locale = config('app.locale');
        $this->timezone = config('app.timezone');
    }

    protected function rules()
    {
        $rules = [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => ['required', 'email', 'max:100', Rule::unique('users')->ignore($this->userId)],
            'username' => ['required', 'max:100', Rule::unique('users')->ignore($this->userId)],
            'selectedRoles' => 'nullable|array',
        ];

        if (!$this->userId) {
            $rules['password'] = 'required|min:6|confirmed';
        } else {
            $rules['password'] = 'nullable|min:6|confirmed';
        }

        return $rules;
    }

    public function store()
    {
        $this->validate();

        $data = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'username' => $this->username,
            'roles' => $this->selectedRoles,
            // 'is_active' => $this->status, // Assuming simple status mapping if needed
        ];

        if ($this->password) {
            $data['password'] = $this->password;
        }

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            app(UserService::class)->updateUserWithMetadata($user, $data);
            session()->flash('message', 'User Updated Successfully.');
        } else {
            app(UserService::class)->createUserWithMetadata($data);
            session()->flash('message', 'User Created Successfully.');
        }

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->username = $user->username;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        // $this->status = $user->is_active;

        $this->openModal();
    }

    public function delete($id)
    {
        $user = User::find($id);
        if ($user) {
            // Basic protection
            if ($user->hasRole(\App\Models\Role::SUPERADMIN) || $user->id === auth()->id()) {
                session()->flash('error', 'Cannot delete this user.');
                return;
            }
            $user->delete();
            session()->flash('message', 'User Deleted Successfully.');
        }
    }
}
