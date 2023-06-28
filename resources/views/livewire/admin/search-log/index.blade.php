<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    @if($viewMode)

                        @livewire('admin.search-log.show', ['search_log_id' => $search_log_id])

                    @else
                        <div wire:loading wire:target="{{ $updateMode ? 'edit' : 'create' }}" class="loader"></div>
                        <div class="card-title">
                            <h4 class="float-left">{{__('cruds.search_log.title')}} {{ __('global.list') }}</h4>                            
                        </div>                
                        <div class="table-responsive">
                            <div class="align-items-center border-top mt-4 pt-2 row justify-content-between">
                                <div class="col-md-2">
                                    <select wire:change="changeNumberOfList($event.target.value)" class="form-control">
                                        @foreach($numberOfrowsList as $key => $val)
                                            <option value="{{ $key }}">{{ $val }}</option>
                                        @endforeach 
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="table-additional-plugin ">
                                        <input type="text" class="form-control" wire:model="search" placeholder="{{ __('global.search')}}">
                                    </div>
                                </div>
                            </div>                        
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>
                                            {{ trans('cruds.search_log.fields.user_id') }}
                                            <span wire:click="sortBy('user_id')" class="float-right text-sm" style="cursor: pointer;">
                                                <i class="fa fa-arrow-up {{ $sortColumnName === 'user_id' && $sortDirection === 'asc' ? '' : 'text-muted' }}"></i>
                                                <i class="fa fa-arrow-down {{ $sortColumnName === 'user_id' && $sortDirection === 'desc' ? '' : 'text-muted' }}"></i>
                                            </span>
                                        </th>
                                        <th>
                                            {{ trans('global.created_at') }}
                                            <span wire:click="sortBy('created_at')" class="float-right text-sm" style="cursor: pointer;">
                                                <i class="fa fa-arrow-up {{ $sortColumnName === 'created_at' && $sortDirection === 'asc' ? '' : 'text-muted' }}"></i>
                                                <i class="fa fa-arrow-down {{ $sortColumnName === 'created_at' && $sortDirection === 'desc' ? '' : 'text-muted' }}"></i>
                                            </span>
                                        </th>
                                        <th>{{ trans('global.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($allSearchLogs->count() > 0)
                                        @foreach($allSearchLogs as $serialNo => $searchLog)
                                            <tr>
                                                <td>{{ $serialNo+1 }}</td>
                                                <td>{{ ucfirst($searchLog->seller->name) }}</td>
                                                
                                                <td>{{ convertDateTimeFormat($searchLog->created_at,'datetime') }}</td>
                                                <td>
                                                    <button type="button" wire:click="show({{$searchLog->id}})" class="btn btn-primary btn-rounded btn-icon">
                                                        <i class="ti-eye"></i>
                                                    </button>
                                                    
                                                    {{-- <button type="button" wire:click="edit({{$searchLog->id}})" class="btn btn-info btn-rounded btn-icon">
                                                        <i class="ti-pencil-alt"></i>
                                                    </button>

                                                    <button type="button" data-id ="{{$searchLog->id}}" class="btn btn-danger btn-rounded btn-icon deleteBtn">
                                                        <i class="ti-trash"></i>
                                                    </button> --}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                    <tr>
                                        <td colspan="5">{{ __('messages.no_record_found')}}</td>
                                    </tr>
                                    @endif
                                
                                </tbody>
                            </table>

                            {{ $allSearchLogs->links('vendor.pagination.bootstrap-5') }}
                        
                        </div>

                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

