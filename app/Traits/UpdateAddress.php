<?php


namespace App\Traits;


use App\AreaNew;
use App\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait UpdateAddress
{
    /**
     * @param Model $create
     * @param string $prefix
     * @param string $province
     * @param string $province_no
     * @param string $city
     * @param string $city_no
     * @param string $area
     * @param string $area_no
     * @return Model
     */
    private function updateAddress(Model $create,$prefix='',$province='province',$province_no='province_no',$city='city',$city_no='city_no',$area='area',$area_no='area_no')
    {
        $province=$prefix.$province;
        $province_no=$prefix.$province_no;
        $city=$prefix.$city;
        $city_no=$prefix.$city_no;
        $area=$prefix.$area;
        $area_no=$prefix.$area_no;

        //修改省编号
        if ($create->$province) {
            $sites = AreaNew::where(function($query)use($create,$province){
                $query->where('name', $create->$province)
                    ->orWhere('name', $create->$province.'省')
                    ->orWhere('name', Str::before($create->$province,'市'));
            })->where('level', 1)->get();
            if ($sites->count() == 1) {
                $create->$province_no = data_get($sites->first(), 'no');
                $create->$province = data_get($sites->first(), 'name');
            }
        }
        //修改市编号
        if ($create->$city) {
            $sites = AreaNew::where(function($query)use($create,$city){
                $query->where('name', $create->$city)
                    ->orWhere('name', $create->$city.'市');
            })->where('level', 2);
            if ($create->$province_no) {
                $sites->where('parent_no', $create->$province_no);
            }
            $sites = $sites->get();
            if ($sites->count() == 1) {
                $create->$city_no = data_get($sites->first(), 'no');
                $create->$city = data_get($sites->first(), 'name');
            }
        }
        //修改区县编号
        if ($create->$area) {
            $sites = AreaNew::where(function($query)use($create,$area){
                $query->where('name', $create->$area)->orWhere('name', $create->$area.'区')->orWhere('name', $create->$area.'县');
            })->where('level', 3);
            if ($create->$city_no) {
                $sites->where('parent_no', $create->$city_no);
            }
            $sites = $sites->get();
            if ($sites->count() == 1) {
                $create->$area_no = data_get($sites->first(), 'no');
                $create->$area = data_get($sites->first(), 'name');
            }
        }

        return $create;
    }
}