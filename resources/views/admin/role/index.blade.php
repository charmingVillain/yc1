@extends('admin.layouts.app')

@section('content')
    <router-view></router-view>
@endsection

@push('scripts')
    <script>
        // 路由配置
        var routeConfig = {
            "index": "{{ route('admin.role.index') }}",
            "store": "{{ route('admin.role.store') }}",
            "destroy": "{{ route('admin.role.destroy', ['id'=> ':id']) }}",
            "update": "{{ route('admin.role.update', ['id'=> ':id']) }}",
            "show": "{{ route('admin.role.show', ['id'=> ':id']) }}",
            "menu": "{{ route('admin.role.menu', ['id'=> ':id']) }}",
            "updateMenu": "{{ route('admin.role.updateMenu', ['id'=> ':id']) }}",
        }
    </script>
    <script src="{{ mix('/js/admin/role/index.js', 'dist') }}"></script>
@endpush
