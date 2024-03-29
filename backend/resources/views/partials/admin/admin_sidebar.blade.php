<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item {{ request()->is('admin/index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">{{__('global.dashboard')}}</span>
            </a>
        </li>

        @if(auth()->user()->is_super_admin)
            

            @can('user_access')
            <li class="nav-item {{ request()->is('admin/seller') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.seller') }}">
                    <i class="icon-grid menu-icon fa-solid fa-users"></i>
                    <span class="menu-title"> {{ __('cruds.user.title') }} </span>
                </a>
            </li>
            @endcan
            @can('buyer_access')            
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#buyer-menu" aria-expanded="false" aria-controls="buyer-menu">
                    <i class="icon-grid menu-icon fa-solid fa-users"></i>
                    <span class="menu-title"> {{ __('cruds.buyer.title') }} </span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="buyer-menu">
                    <ul class="nav flex-column sub-menu">
                        @can('buyer_access')
                        <li class="nav-item {{ request()->is('admin/buyer') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.buyer') }}">
                                <span class="menu-title"> {{ __('cruds.buyer.title') }} </span>
                            </a>
                        </li>
                        @endcan
                        @can('buyer_flag_access')
                        <li class="nav-item {{ request()->is('admin/buyer') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.buyer') }}">
                                <span class="menu-title"> {{ __('cruds.buyer.title') }} </span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li> 
            @endcan
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#master-menu"  aria-expanded="false" aria-controls="master-menu">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Master</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="master-menu">
                    <ul class="nav flex-column sub-menu">
                        @can('plan_access')
                        <li class="nav-item {{ request()->is('admin/plan') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.plan') }}">
                                <span class="menu-title">{{__('cruds.plan.title')}}</span>
                            </a>
                        </li>
                        @endcan
                        @can('addon_access')
                        <li class="nav-item {{ request()->is('admin/addon') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.addon') }}">
                                <span class="menu-title">{{__('cruds.addon.title')}}</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li> 
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#setting-menu" aria-expanded="false" aria-controls="setting-menu">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title"> {{ __('cruds.setting.title') }} </span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="setting-menu">
                    <ul class="nav flex-column sub-menu">
                        @can('video_access')
                        <li class="nav-item {{ request()->is('admin/video') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.video') }}">
                                <span class="menu-title">{{__('cruds.video.title')}}</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li> 
        @endif
    </ul>
</nav>