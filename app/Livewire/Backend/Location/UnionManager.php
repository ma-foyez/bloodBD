<?php

namespace App\Livewire\Backend\Location;

use App\Models\Area;
use App\Models\District;
use App\Models\Division;
use App\Models\Union;
use Livewire\Component;
use Livewire\WithPagination;

class UnionManager extends Component
{
    use WithPagination;

    public $name, $bn_name, $url, $lat, $lon;
    public $division_id = '';
    public $district_id = '';
    public $area_id = '';
    public $unionId;
    public $isModalOpen = 0;
    public $search = '';

    protected $rules = [
        'area_id' => 'required',
        'name' => 'required|string|max:255',
        'bn_name' => 'required|string|max:255',
        'url' => 'nullable|string|max:255',
        'lat' => 'nullable|string|max:255',
        'lon' => 'nullable|string|max:255',
    ];

    public function render()
    {
        $unions = Union::with('area')
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('bn_name', 'like', '%' . $this->search . '%')
            ->orWhere('bn_name', 'like', '%' . $this->search . '%')
            ->orderBy('area_id')
            ->paginate(10);



        $districts = $this->division_id
            ? District::where('division_id', $this->division_id)->get()
            : [];

        $areas = $this->district_id
            ? Area::where('district_id', $this->district_id)->get()
            : [];

        return view('livewire.backend.location.union-manager', [
            'unions' => $unions,
            'areas' => $areas, // Filtered areas
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
        $this->area_id = '';
        $this->unionId = null;
    }

    public function store()
    {
        $this->validate();

        Union::updateOrCreate(['id' => $this->unionId], [
            'area_id' => $this->area_id,
            'name' => $this->name,
            'bn_name' => $this->bn_name,
            'url' => $this->url,
            'lat' => $this->lat,
            'lon' => $this->lon,
        ]);

        session()->flash('message', $this->unionId ? 'Union Updated Successfully.' : 'Union Created Successfully.');

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
        $union = Union::with('area.district.division')->findOrFail($id);
        $this->unionId = $id;
        $this->division_id = $union->area->district->division_id;
        $this->district_id = $union->area->district_id;
        $this->area_id = $union->area_id;
        $this->name = $union->name;
        $this->bn_name = $union->bn_name;
        $this->url = $union->url;
        $this->lat = $union->lat;
        $this->lon = $union->lon;

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
            Union::find($this->deleteId)->delete();
            session()->flash('message', 'Union Deleted Successfully.');
            $this->deleteId = null;
            $this->isDeleteModalOpen = false;
        }
    }
}
