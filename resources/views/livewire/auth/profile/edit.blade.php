 
 <form wire:submit.prevent="updateProfile" class="mt-5">
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label for="first_name">{{ __('cruds.user.fields.first_name') }}</label>
                <div class="input-set">
                    <span class="icon-left"><img src="{{ asset('admin/images/user-login.svg') }}" alt="Img"></span>
                    <input type="text" class="form-control" id="first_name"  wire:model.defer="first_name" placeholder="Enter {{ __('cruds.user.fields.first_name') }}"/>
                </div>
                @error('first_name') <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label for="last_name">{{ __('cruds.user.fields.last_name') }}</label>
                <div class="input-set">
                    <span class="icon-left"><img src="{{ asset('admin/images/user-login.svg') }}" alt="Img"></span>
                    <input type="text" class="form-control" id="last_name"  wire:model.defer="last_name" placeholder="Enter {{ __('cruds.user.fields.last_name') }}"/>
                </div>
                @error('last_name') <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-lg-12">
            <div class="form-group">
                <label for="email">{{ __('cruds.user.fields.email') }}</label>
                <div class="input-set">
                    <span class="icon-left"><img src="{{ asset('admin/images/mail.svg') }}" alt="Img"></span>
                    <input type="email" class="form-control" wire:model.defer="email" placeholder="Enter {{ __('cruds.user.fields.email') }}" disabled="" />
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-12">
            <div class="form-group">
                <label for="Mobile">{{ __('cruds.user.fields.phone') }}</label>
                <div class="input-set">
                    <span class="icon-left"><img src="{{ asset('admin/images/phone.svg') }}" alt="Img"></span>
                    <input type="text" class="form-control only_integer" id="mobile" wire:model.defer="phone" placeholder="Enter {{ __('cruds.user.fields.phone') }}" onkeydown="javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight','Tab'].includes(event.code) ? true : !isNaN(Number(event.key)) && event.code!=='Space' && this.value.length < 10 " step="1"  autocomplete="off"/>
                </div>
                @error('phone') <span class="error text-danger">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-fill btn-blue" wire:loading.attr="disabled">
        {{ __('global.update')}}
        <span wire:loading wire:target="updateProfile">
            <i class="fa fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
        </span>
    </button>
</form>
