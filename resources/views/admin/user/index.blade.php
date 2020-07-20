@extends('admin.layouts.app')

@section('content')
    <router-view></router-view>
@endsection

@push('scripts')
    <script>
        // 路由配置
        var routeConfig = {
            "index": "{{ route('admin.user.index') }}",
            "store": "{{ route('admin.user.store') }}",
            "update": "{{ route('admin.user.update', ['id'=> ':id']) }}",
            "show": "{{ route('admin.user.show', ['id'=> ':id']) }}",
            "roles": "{{ route('admin.user.roles', ['id'=> ':id']) }}",
        }
    </script>
    <script src="{{ mix('/js/admin/user/index.js', 'dist') }}"></script>
@endpush
