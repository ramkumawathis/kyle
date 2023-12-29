<?php

namespace App\Http\Livewire\Admin\Support;

use App\Models\Support as CutomerSupport;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class SupportDatatable extends LivewireDatatable
{
    public function mount($model = false, $include = [], $exclude = [], $hide = [], $dates = [], $times = [], $searchable = [], $sort = null, $hideHeader = null, $hidePagination = null, $perPage = null, $exportable = false, $hideable = false, $beforeTableSlot = false, $buttonsSlot = false, $afterTableSlot = false, $params = [])
    {
        parent::mount($model, $include, $exclude, $hide, $dates, $times, $searchable, $sort, $hideHeader, $hidePagination, $perPage, $exportable, $hideable, $beforeTableSlot, $buttonsSlot, $afterTableSlot, $params);

        // $this->resetTable();
        $this->perPage = config('livewire-datatables.default_per_page', 10);
        $this->sort(5, 'desc');
        $this->search = null;
        $this->setPage(1);
    }

    public function builder()
    {
        return CutomerSupport::query();
    }

    public function columns()
    {
        return [
            Column::index($this)->unsortable(),
            Column::callback(['name'],function($name){
                return ucwords($name);
            })->label(trans('cruds.support.fields.name'))->sortable()->searchable(),
            Column::name('email')->label(trans('cruds.support.fields.email'))->sortable()->searchable(),
            Column::name('phone_number')->label(trans('cruds.support.fields.phone_number'))->sortable()->searchable(),
            DateColumn::name('created_at')->label(trans('global.created'))->format(config('constants.date_format'))->sortable()->searchable(),
            Column::callback(['id'], function ($id) {
                $array = ['show','support_reply_btn'];
                return view('livewire.datatables.actions', ['id' => $id, 'events' => $array]);
            })->label(trans('global.action'))->unsortable(),
        ];
    }
}