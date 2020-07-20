<div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
            <img src="/AdminLTE-3.0.2/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
            <a href="#" class="d-block">{{ data_get(Auth::user(), 'nickname') }}</a>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-compact nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            {{-- menu-open --}}
            {{--<li class="nav-header">MULTI LEVEL EXAMPLE</li>--}}

            {{-- 循环菜单 --}}
            @foreach(get_admin_menu() as $key => $val)
                @if(!data_get($val, 'is_ajax'))
                    <li  class="nav-item @if(has_menu_children($val, '_child')) has-treeview @endif  @if(data_get($val, 'active') && has_menu_children($val, '_child')) menu-open @endif">
                        <a data-id="{{ data_get($val, 'id') }}" href="{{ data_get($val, 'url') }}" class="nav-link  @if(data_get($val, 'active')) active @endif">
                            @if(data_get($val, 'icon')) <i class="{{ $val['icon'] }}"></i> @endif
                            <p>
                                {{ data_get($val, 'name') }}
                                @if(has_menu_children($val, '_child'))
                                    <i class="right fas fa-angle-left"></i>
                                @endif
                            </p>
                        </a>

                        @if(has_menu_children($val, '_child'))
                            @include('admin.layouts.AdminLTE.treeview', ['children'=> data_get($val, '_child')])
                        @endif
                    </li>
                @endif
            @endforeach

        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
