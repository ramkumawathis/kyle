
<h4 class="card-title">
    {{__('global.show')}}
    {{ strtolower(__('cruds.plan.title_singular'))}}</h4>

    <table class="table table-design mb-4">
        <tr>
            <th width="25%">{{ __('cruds.plan.fields.image')}}</th>
            <td><img class="rounded img-thumbnail" src="{{ $details->image_url }}" style="width:100px; height: auto;"/></td>
        </tr>
        <tr>
            <th width="25%">{{ __('cruds.plan.fields.title')}}</th>
            <td>{{ $details->title ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th width="25%">{{ __('cruds.plan.fields.price')}}</th>
            <td> {{ $details->price ? '$'.number_format($details->price,2) : 'N/A' }}</td>
        </tr>
        <tr>
            <th width="25%">{{ __('cruds.plan.fields.type')}}</th>
            <td> {{ ucfirst($details->type) ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th width="25%">{{ __('cruds.plan.fields.credits')}}</th>
            <td> {{ $details->credits ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th width="25%">{{ __('cruds.plan.fields.description')}}</th>
            <td>{!! $details->description !!}</td>
        </tr>
        <tr>
            <th width="25%">{{ __('cruds.plan.fields.status')}}</th>
            <td> {{ ($details->status ? 'Active' : 'Inactive') }}</td>
        </tr>
        <tr>
            <th width="25%">{{ __('global.created_at')}}</th>
            <td> {{ $details->created_at->format(config('constants.datetime_format')) }}</td>
        </tr>
    </table>
    <div class="text-right">
        <button wire:click.prevent="cancel" class="btn btn-fill btn-dark">
            {{ __('global.back')}}
            <span wire:loading wire:target="cancel">
                <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
            </span>
        </button>
    </div>

               