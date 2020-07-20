@extends('admin.layouts.app')

@section('content')
    <router-view></router-view>
@endsection

@push('scripts')
    <script>
        // 路由配置
        var routeConfig = {
            "index": "{{ route('admin.goods-category.index') }}",
            "store": "{{ route('admin.goods-category.store') }}",
            "destroy": "{{ route('admin.goods-category.destroy', ['id'=> ':id']) }}",
            "update": "{{ route('admin.goods-category.update', ['id'=> ':id']) }}",
            "parentZero": "{{ route('admin.goods-category.parent-zero') }}",
        }
    </script>
    <script src="{{ mix('/js/admin/goodsCategory/index.js', 'dist') }}"></script>
@endpush
