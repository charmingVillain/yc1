<ul class="nav nav-treeview">
    @foreach($children as $key => $val)
        @if(!data_get($val, 'is_ajax'))
            <li class="nav-item @if(has_menu_children($val, '_child')) has-treeview @endif  @if(data_get($val, 'active') && has_menu_children($val, '_child')) menu-open @endif">
                <a data-id="{{ data_get($val, 'id') }}" href="{{ data_get($val, 'url') }}"
                   class="nav-link  @if(data_get($val, 'active')) active @endif">
                    {{--<i class="far fa-dot-circle nav-icon"></i>--}}
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
