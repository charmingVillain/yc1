@extends('admin.layouts.app')

@section('content')
    <router-view></router-view>
@endsection

@push('scripts')
    <script>
        // 路由配置
        var routeConfig = {
            "index": "{{ route('admin.menu.index') }}",
            "store": "{{ route('admin.menu.store') }}",
            "create": "{{ route('admin.menu.create') }}",
            "update": "{{ route('admin.menu.update', ['id'=> ':id']) }}",
            "destroy": "{{ route('admin.menu.destroy', ['id'=> ':id']) }}",
            "edit": "{{ route('admin.menu.edit', ['id'=> ':id']) }}",
            "sort": "{{ route('admin.menu.sort', ['id'=> ':id']) }}",
        }
        var routeList = @json($route_list)
    </script>
    <script src="{{ mix('/js/admin/menu/index.js', 'dist') }}"></script>
@endpush