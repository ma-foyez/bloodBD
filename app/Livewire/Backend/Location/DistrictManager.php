<?php

namespace App\Livewire\Backend\Location;

use App\Models\District;
use App\Models\Division;
use Livewire\Component;
use Livewire\WithPagination;

class DistrictManager extends Component
{
    use WithPagination;

    public $name, $bn_name, $url, $lat, $lon;
    public $division_id;
    public $districtId;
    public $isModalOpen = 0;
    public $search = '';

    protected $rules = [
        'division_id' => 'required',
        'name' => 'required|string|max:255',
        'bn_name' => 'required|string|max:255',
        'url' => 'nullable|string|max:255',
        'lat' => 'nullable|string|max:255',
        'lon' => 'nullable|string|max:255',
    ];

    public function render()
    {
        $districts = District::with('division')
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('bn_name', 'like', '%' . $this->search . '%')
            ->orWhere('bn_name', 'like', '%' . $this->search . '%')
            ->orderBy('division_id')
            ->paginate(10);

        return view('livewire.backend.location.district-manager', [
            'districts' => $districts,
            'divisions' => Division::all(),
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
        $this->division_id = '';
        $this->districtId = null;
    }

    public function store()
    {
        $this->validate();

        District::updateOrCreate(['id' => $this->districtId], [
            'division_id' => $this->division_id,
            'name' => $this->name,
            'bn_name' => $this->bn_name,
            'url' => $this->url,
            'lat' => $this->lat,
            'lon' => $this->lon,
        ]);

        session()->flash('message', $this->districtId ? 'District Updated Successfully.' : 'District Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $district = District::findOrFail($id);
        $this->districtId = $id;
        $this->division_id = $district->division_id;
        $this->name = $district->name;
        $this->bn_name = $district->bn_name;
        $this->url = $district->url;
        $this->lat = $district->lat;
        $this->lon = $district->lon;

        $this->openModal();
    }

    public $deleteId = null;
    public $isDeleteModalOpen = false;

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    public function delete()
    {
        if ($this->deleteId) {
            District::find($this->deleteId)->delete();
            session()->flash('message', 'District Deleted Successfully.');
            $this->deleteId = null;
            $this->isDeleteModalOpen = false;
        }
    }
}
