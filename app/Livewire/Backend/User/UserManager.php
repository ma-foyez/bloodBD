<?php

namespace App\Livewire\Backend\User;

use App\Models\User;
use App\Models\Role;
use App\Models\Division;
use App\Models\District;
use App\Models\Area;
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

    public $first_name, $last_name, $email, $username, $mobile, $password, $password_confirmation;
    public $dob, $blood_group, $occupation, $is_weight_50kg = false, $last_donation;
    public $division_id = '', $district_id = '', $area_id = '', $post_office;
    public $status = 1;
    public $userId;
    public $selectedRoles = [];
    public $isModalOpen = 0;
    public $confirmingDelete = false;
    public $deleteId = null;
    public $search = '';

    // Additional fields from original controller
    public $locale;
    public $timezone;

    public function render()
    {
        $users = User::with('roles')
            ->where(function ($query) {
                $query->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ['%' . $this->search . '%']);
            })
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orWhere('username', 'like', '%' . $this->search . '%')
            ->paginate(10);

        $districts = $this->division_id
            ? District::where('division_id', $this->division_id)->get()
            : [];

        $areas = $this->district_id
            ? Area::where('district_id', $this->district_id)->get()
            : [];

        return view('livewire.backend.user.user-manager', [
            'users' => $users,
            'roles' => app(RolesService::class)->getRolesDropdown(),
            'locales' => app(LanguageService::class)->getLanguages(),
            'timezones' => app(TimezoneService::class)->getTimezones(),
            'divisions' => Division::all(),
            'districts' => $districts,
            'areas' => $areas,
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
        $this->mobile = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->dob = '';
        $this->blood_group = '';
        $this->occupation = '';
        $this->is_weight_50kg = false;
        $this->last_donation = '';
        $this->division_id = '';
        $this->district_id = '';
        $this->area_id = '';
        $this->post_office = '';
        $this->selectedRoles = [];
        $this->status = 1;
        $this->userId = null;
        $this->locale = config('app.locale');
        $this->timezone = config('app.timezone');
    }

    protected function rules()
    {
        $rules = [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->userId)],
            'username' => ['required', 'max:255', Rule::unique('users')->ignore($this->userId)],
            'mobile' => ['nullable', 'max:20', Rule::unique('users')->ignore($this->userId)],
            'dob' => 'nullable|date',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'occupation' => 'nullable|max:255',
            'is_weight_50kg' => 'nullable|boolean',
            'last_donation' => 'nullable|date',
            'division_id' => 'nullable|exists:divisions,id',
            'district_id' => 'nullable|exists:districts,id',
            'area_id' => 'nullable|exists:areas,id',
            'post_office' => 'nullable|max:255',
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
            'mobile' => $this->mobile,
            'dob' => $this->dob,
            'blood_group' => $this->blood_group,
            'occupation' => $this->occupation,
            'is_weight_50kg' => $this->is_weight_50kg,
            'last_donation' => $this->last_donation,
            'division_id' => $this->division_id,
            'district_id' => $this->district_id,
            'area_id' => $this->area_id,
            'post_office' => $this->post_office,
            'roles' => $this->selectedRoles,
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

    public function updatedDivisionId()
    {
        $this->district_id = '';
        $this->area_id = '';
    }

    public function updatedDistrictId()
    {
        $this->area_id = '';
    }

    public function edit($id)
    {
        $user = User::with('division', 'district', 'area')->findOrFail($id);
        $this->userId = $id;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->username = $user->username;
        $this->mobile = $user->mobile;
        $this->dob = $user->dob;
        $this->blood_group = $user->blood_group;
        $this->occupation = $user->occupation;
        $this->is_weight_50kg = $user->is_weight_50kg;
        $this->last_donation = $user->last_donation;
        $this->division_id = $user->division_id;
        $this->district_id = $user->district_id;
        $this->area_id = $user->area_id;
        $this->post_office = $user->post_office;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();

        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->confirmingDelete = true;
    }

    public function delete()
    {
        $user = User::find($this->deleteId);
        if ($user) {
            // Basic protection
            if ($user->hasRole(\App\Models\Role::SUPERADMIN) || $user->id === auth()->id()) {
                session()->flash('error', 'Cannot delete this user.');
                $this->confirmingDelete = false;
                return;
            }
            $user->delete();
            session()->flash('message', 'User Deleted Successfully.');
        }
        $this->confirmingDelete = false;
        $this->deleteId = null;
    }

    public function cancelDelete()
    {
        $this->confirmingDelete = false;
        $this->deleteId = null;
    }
}
