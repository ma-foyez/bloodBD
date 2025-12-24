<?php

namespace App\Livewire\Backend\Location;

use App\Models\Area;
use App\Models\District;
use App\Models\Division;
use Livewire\Component;
use Livewire\WithPagination;

class AreaManager extends Component
{
    use WithPagination;

    public $name, $bn_name, $url, $lat, $lon;
    public $division_id = '';
    public $district_id = '';
    public $areaId;
    public $isModalOpen = 0;
    public $search = '';

    protected $rules = [
        'district_id' => 'required',
        'name' => 'required|string|max:255',
        'bn_name' => 'required|string|max:255',
        'url' => 'nullable|string|max:255',
        'lat' => 'nullable|string|max:255',
        'lon' => 'nullable|string|max:255',
    ];

    public function render()
    {
        $areas = Area::with('district')
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('bn_name', 'like', '%' . $this->search . '%')
            ->orWhere('bn_name', 'like', '%' . $this->search . '%')
            ->orderBy('district_id')
            ->paginate(10);

        $districts = $this->division_id
            ? District::where('division_id', $this->division_id)->get()
            : [];

        return view('livewire.backend.location.area-manager', [
            'areas' => $areas,
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
        $this->district_id = '';
        $this->areaId = null;
    }

    public function store()
    {
        $this->validate();

        Area::updateOrCreate(['id' => $this->areaId], [
            'district_id' => $this->district_id,
            'name' => $this->name,
            'bn_name' => $this->bn_name,
            'url' => $this->url,
            'lat' => $this->lat,
            'lon' => $this->lon,
        ]);

        session()->flash('message', $this->areaId ? 'Area Updated Successfully.' : 'Area Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function updatedDivisionId()
    {
        $this->district_id = '';
    }

    public function edit($id)
    {
        $area = Area::with('district.division')->findOrFail($id);
        $this->areaId = $id;
        $this->division_id = $area->district->division_id;
        $this->district_id = $area->district_id;
        $this->name = $area->name;
        $this->bn_name = $area->bn_name;
        $this->url = $area->url;
        $this->lat = $area->lat;
        $this->lon = $area->lon;

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
            Area::find($this->deleteId)->delete();
            session()->flash('message', 'Area Deleted Successfully.');
            $this->deleteId = null;
            $this->isDeleteModalOpen = false;
        }
    }
}
