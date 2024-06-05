<?php

namespace App\Http\Livewire\Admin\BuyerPlans;

use App\Models\BuyerPlan;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;

class BuyerPlanTable extends Component
{
    use WithPagination;

    public $search = null;
    
    public $sortColumnName = 'created_at', $sortDirection = 'desc', $perPage = 10;
    
    protected $listeners = [
        'refreshTable' =>'render'
    ];

    public function render()
    {
        $searchValue = $this->search;
        $statusSearch = null;
        if(Str::contains('active', strtolower($searchValue))){
            $statusSearch = 1;
        }else if(Str::contains('inactive', strtolower($searchValue))){
            $statusSearch = 0;
        }

        $plans = BuyerPlan::query()
        ->where(function ($query) use ($searchValue,$statusSearch) {
            $query->where('title','like','%'.$searchValue.'%')
            ->orWhere('amount','like',$searchValue.'%')
            ->orWhere('type','like',$searchValue.'%')
            ->orWhere('position','like',$searchValue.'%')
            ->orWhere('status',$statusSearch)
            ->orWhereRaw("date_format(created_at, '".config('constants.search_datetime_format')."') like ?", ['%'.$searchValue.'%']);
        })->orderBy($this->sortColumnName, $this->sortDirection)
        ->paginate($this->perPage);

        return view('livewire.admin.buyer-plans.buyer-plan-table',compact('plans'));
    }
    
    public function updatedPerPage(){
          $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function sortBy($columnName)
    {
        $this->resetPage();

        if ($this->sortColumnName === $columnName) {
            $this->sortDirection = $this->swapSortDirection();
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortColumnName = $columnName;
    }
    
    public function swapSortDirection()
    {
        return $this->sortDirection === 'asc' ? 'desc' : 'asc';
    }
}