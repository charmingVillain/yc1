@extends('admin.layouts.app')

@section('content')
    <router-view></router-view>
@endsection

@push('scripts')
    <script>
        // 路由配置
        var routeConfig = {
            "index": "{{ route('admin.test.index') }}",
            "store": "{{ route('admin.test.store') }}",
            "update": "{{ route('admin.test.update', ['id'=> ':id']) }}",
            "show": "{{ route('admin.test.show', ['id'=> ':id']) }}",
        }
    </script>
    <script src="{{ mix('/js/admin/test/index.js', 'dist') }}"></script>
@endpush
