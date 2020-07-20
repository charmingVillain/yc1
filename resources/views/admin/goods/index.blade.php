@extends('admin.layouts.app')

@section('content')
    <router-view></router-view>
@endsection

@push('scripts')
    <script>
        // 路由配置
        var routeConfig = {
            "index": "{{ route('admin.goods.index') }}",
            "store": "{{ route('admin.goods.store') }}",
            "update": "{{ route('admin.goods.update', ['id'=> ':id']) }}",
            "show": "{{ route('admin.goods.show', ['id'=> ':id']) }}",
            "edit": "{{ route('admin.goods.edit',['id'=> ':id']) }}",
            "destroy" :"{{route('admin.goods.destroy',['id'=> ':id'])}}",
            "goodsCategory": "{{ route('admin.goods-category.index') }}",
        }
        var CSRF_TOKEN = "{{ csrf_token() }}";
    </script>

    <script src="https://cdn.jsdelivr.net/npm/tinymce-all-in-one@4.9.2/tinymce.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tinymce-lang@0.0.1/langs/zh_CN.js"></script>
    <script src="{{ mix('/js/admin/goods/index.js', 'dist') }}"></script>
@endpush
