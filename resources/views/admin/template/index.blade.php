@extends('admin.layouts.app')

@section('content')
    <router-view></router-view>
@endsection

@push('scripts')
    <script>
        // 路由配置
        var routeConfig = {
            "index": "{{ route('admin.template.index') }}",
            "store": "{{ route('admin.template.store') }}",
            "update": "{{ route('admin.template.update', ['id'=> ':id']) }}",
            "show": "{{ route('admin.template.show', ['id'=> ':id']) }}",
            "edit": "{{ route('admin.template.edit',['id'=> ':id']) }}",
            "destroy" :"{{route('admin.template.destroy',['id'=> ':id'])}}",
        }
        var CSRF_TOKEN = "{{ csrf_token() }}";
    </script>

    <script src="{{ mix('/js/admin/template/index.js', 'dist') }}"></script>
@endpush
