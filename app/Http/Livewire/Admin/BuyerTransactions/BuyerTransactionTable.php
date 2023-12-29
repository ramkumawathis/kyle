<?php

namespace App\Http\Livewire\Admin\BuyerTransactions;

use Illuminate\Support\Str;
use App\Models\BuyerTransaction;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class BuyerTransactionTable extends Component
{
    use WithPagination;

    public $search = null;
    
    public $sortColumnName = 'created_at', $sortDirection = 'desc', $perPage = 10;
    
    protected $listeners = [
        
    ];
    
    public function render()
    {
        // $statusSearch = null;
        $searchValue = $this->search;
        // if(Str::contains('active', strtolower($searchValue))){
        //     $statusSearch = 1;
        // }else if(Str::contains('inactive', strtolower($searchValue))){
        //     $statusSearch = 0;
        // }

        $transactions = BuyerTransaction::query()
        ->select('buyer_transactions.*', 'buyer_plans.title as plan_title')
        ->leftJoin('users', 'buyer_transactions.user_id', '=', 'users.id')
        ->leftJoin('buyer_plans', function ($join) {
            $join->on('buyer_transactions.plan_id', '=', 'buyer_plans.id');
        })
        ->where(function ($query) use ($searchValue) {
            $query->where(function ($subquery) use($searchValue) {
                    $subquery->whereRaw("JSON_SEARCH(lower(buyer_transactions.user_json), 'one', '%".strtolower($searchValue)."%', NULL, '$.\"name\"') IS NOT NULL");
                })
                // ->orWhere(function ($query) use ($searchValue) {
                //     $query->where('buyer_plans.title', 'like', '%' . $searchValue . '%');
                // })
                ->orWhere(function ($subquery) use($searchValue) {
                    $subquery->whereRaw("JSON_SEARCH(lower(buyer_transactions.plan_json), 'one', '%".strtolower($searchValue)."%', NULL, '$.\"title\"') IS NOT NULL");
                })
                
                ->orWhere('buyer_transactions.amount',str_replace(',','',$searchValue))
                ->orWhere('buyer_transactions.currency','like',$searchValue.'%')
                ->orWhere('buyer_transactions.status','like',$searchValue.'%')
                ->orWhereRaw("date_format(buyer_transactions.created_at, '".config('constants.search_datetime_format')."') like ?", ['%'.$searchValue.'%']);

        });
        if($this->sortColumnName == 'name'){
            $transactions = $transactions->orderBy(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(buyer_transactions.user_json, '$.".$this->sortColumnName."'))"), $this->sortDirection);
        } else if($this->sortColumnName == 'title'){
            $transactions = $transactions->orderBy(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(buyer_transactions.plan_json, '$.".$this->sortColumnName."'))"), $this->sortDirection);
        } else {
            $transactions->orderBy($this->sortColumnName, $this->sortDirection);
        }

        $transactions = $transactions->paginate($this->perPage);

        return view('livewire.admin.buyer-transactions.buyer-transaction-table',compact('transactions'));
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