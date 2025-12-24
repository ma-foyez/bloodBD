<?php

namespace App\Livewire\Backend\Location;

use App\Http\Controllers\Controller;
use App\Models\Division;
use Livewire\Component;
use Livewire\WithPagination;

class DivisionManager extends Component
{
    use WithPagination;

    public $name, $bn_name, $url, $lat, $lon;
    public $divisionId;
    public $isModalOpen = 0;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'bn_name' => 'required|string|max:255',
        'url' => 'nullable|string|max:255',
        'lat' => 'nullable|string|max:255',
        'lon' => 'nullable|string|max:255',
    ];

    public function render()
    {
        $divisions = Division::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('bn_name', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.backend.location.division-manager', [
            'divisions' => $divisions,
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
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->bn_name = '';
        $this->url = '';
        $this->lat = '';
        $this->lon = '';
        $this->divisionId = null;
    }

    public function store()
    {
        $this->validate();

        Division::updateOrCreate(['id' => $this->divisionId], [
            'name' => $this->name,
            'bn_name' => $this->bn_name,
            'url' => $this->url,
            'lat' => $this->lat,
            'lon' => $this->lon,
        ]);

        session()->flash('message', $this->divisionId ? 'Division Updated Successfully.' : 'Division Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $division = Division::findOrFail($id);
        $this->divisionId = $id;
        $this->name = $division->name;
        $this->bn_name = $division->bn_name;
        $this->url = $division->url;
        $this->lat = $division->lat;
        $this->lon = $division->lon;

        $this->openModal();
    }

    public $deleteId = null;
    public $isDeleteModalOpen = false;

    // ... (existing helper methods)

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    public function delete()
    {
        if ($this->deleteId) {
            Division::find($this->deleteId)->delete();
            session()->flash('message', 'Division Deleted Successfully.');
            $this->deleteId = null;
            $this->isDeleteModalOpen = false;
        }
    }
}
