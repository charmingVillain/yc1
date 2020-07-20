<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Goods;
use App\GoodsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Api\ApiController;

class GoodsController extends ApiController
{
    // 获取酒吧分类、酒吧位置
    public function category(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pid' => 'required'
        ], [
            'required' => '缺少必要的参数pid'
        ]);
        if ($validator->errors()->count()) {
            return $this->returnJson(500, $validator->errors()->first());
        } else {
            $categorys = GoodsCategory::where(['pid'=>$request->input('pid'), 'status'=>1])->with('file')->select(['id', 'name'])->orderBy('sort', 'ASC')->limit(3)->get();
            return $this->returnJson(200, '操作成功', $categorys);
        }
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required'
        ], [
            'required' => '缺少必要的参数lat',
            'required' => '缺少必要的参数lng'
        ]);
        if ($validator->errors()->count()) {
            return $this->returnJson(500, $validator->errors()->first());
        } else {
            $lat = $request->input('lat');
            $lng = $request->input('lng');
            $data = [];
            // $data['neighbouring'] = $this->neighbouring($lat, $lng);
            // $data['recommend'] = $this->recommend($lat, $lng);
            $lists = $this->neighbouring($lat, $lng);
            
            return $this->returnJson(200, '操作成功', $lists);
        }
    }

    // 获取附近酒吧信息
    private function neighbouring($lat, $lng)
    {
        $lists = Goods::where(['status'=>1])->with('img')->selectRaw("ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($lat*PI()/180-lat*PI()/180)/2),2)+COS($lat*PI()/180)*COS(lat*PI()/180)*POW(SIN(($lng *PI()/180-lng*PI()/180)/2),2)))*1000) AS distance, goods.id, goods.img_id, goods.address, goods.market_price, goods.title, goods.lat, goods.lng, goods.tags")->orderBy('distance', 'ASC')->paginate(5)->toArray();
        // $goods->map(function($item) use ($lat, $lng) {
        //     $distance = self::getDistance($lat, $lng, $item->lat, $item->lng);
        //     data_set($item, 'distance', $distance);
        //     return $item;
        // });
        $data = [];
        $data['page'] = $lists['last_page'];

        $data['lists'] = collect($lists['data'])->map(function($item) use ($lat, $lng) {
            $distance = self::getDistance($lat, $lng, $item['lat'], $item['lng']);
            data_set($item, 'distance', $distance);
            return $item;
        });
        return $data;
    }

    public function rotation()
    {
        $goods = Goods::where(['status'=>1])->orderBy('sort', 'DESC')->with('img')->select(['id', 'img_id', 'title'])->get();
        
        return $this->returnJson(200, '操作成功', $goods);
    }

    // 获取推荐酒吧信息
    private function recommend($lat, $lng)
    {
        $goods = Goods::where(['status'=>1])->with('img')->select(['id', 'address', 'market_price', 'title', 'lat', 'lng', 'tags'])->orderBy('sort', 'DESC')->limit(5)->get();
        $goods->map(function($item) use ($lat, $lng) {
            $distance = self::getDistance($lat, $lng, $item['lat'], $item['lng']);
            data_set($item, 'distance', $distance);
            return $item;
        });
        return $goods;
    }

    // 获取筛选的酒吧信息
    public function goods(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required'
        ], [
            'required' => '缺少必要的参数lat',
            'required' => '缺少必要的参数lng'
        ]);
        if ($validator->errors()->count()) {
            return $this->returnJson(500, $validator->errors()->first());
        } else {
            $lat = $request->input('lat');
            $lng = $request->input('lng');

            $builder = Goods::query();
            $builder->where(['status'=>1]);
            if ($request->input('cid')) {
                $pids = [];
                // $pid = GoodsCategory::where(['id'=>$request->input('cid')])->select(['pid'])->first();
                // if (!$pid) {
                //     return $this->returnJson(500, '分类数据错误');
                // }
                // if (!$pid->pid) {
                //     $cids = GoodsCategory::where(['pid'=>$request->input('cid')])->select(['id'])->get();
                //     $pids = $cids->pluck('id')->merge([$request->input('cid')])->toArray();
                // } else {
                //     $pids[] = $request->input('cid');
                // }
                $pid = GoodsCategory::where(['id'=>$request->input('cid')])->with(['goodscategory'=>function($query){
                    $query->select('id', 'pid');
                }])->select(['id'])->first();
                if (!$pid) {
                    return $this->returnJson(500, '分类数据错误');
                }
                $pids = collect($pid)->flatten()->unique();
                $builder->whereIn('goods_category_id', $pids);
            }
            $lists = $builder->with('img')->selectRaw("ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($lat*PI()/180-lat*PI()/180)/2),2)+COS($lat*PI()/180)*COS(lat*PI()/180)*POW(SIN(($lng *PI()/180-lng*PI()/180)/2),2)))*1000) AS distance, goods.id, goods.img_id, goods.address, goods.market_price, goods.title, goods.lat, goods.lng, goods.tags")->orderBy('sort', 'DESC')->orderBy('distance', 'ASC')->paginate(5)->toArray();

            $data = [];
            $data['page'] = $lists['last_page'];

            $data['lists'] = collect($lists['data'])->map(function($item) use ($lat, $lng) {
                $distance = self::getDistance($lat, $lng, $item['lat'], $item['lng']);
                data_set($item, 'distance', $distance);
                return $item;
            });

            return $this->returnJson(200, '操作成功', $data);
        }
    }

    // 获取夜店详情
    public function detail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required',
            'id'  => 'required'
        ], [
            'required' => '缺少必要的参数lat',
            'required' => '缺少必要的参数lng',
            'required' => '缺少必要的参数id',
        ]);
        if ($validator->errors()->count()) {
            return $this->returnJson(500, $validator->errors()->first());
        } else {
            $lat = $request->input('lat');
            $lng = $request->input('lng');

            $good = Goods::where(['id'=>$request->input('id'), 'status'=>1])->with(['img', 'files'])->select(['id', 'img_id', 'file_id', 'detail', 'address', 'market_price', 'title', 'lat', 'lng', 'tags'])->first();
            $good->distance = self::getDistance($lat, $lng, $good->lat, $good->lng);

            return $this->returnJson(200, '操作成功', $good);
        }
    }

    // 获取经纬度返回定位坐标到夜场距离
    private static function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $radLat1 = deg2rad($lat1);
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137;
        return round($s, 2);//返回公里数
    }
}