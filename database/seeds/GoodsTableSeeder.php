<?php

use Illuminate\Database\Seeder;

class GoodsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('goods')->delete();
        
        \DB::table('goods')->insert(array (
            0 => 
            array (
                'id' => 5,
                'goods_code' => 'YC2020071116135000001',
            'name' => '胡桃里(文殊院店)',
            'title' => '胡桃里(文殊院店)',
                'address' => '成都市青羊区白云寺街29号',
                'goods_category_id' => 10,
                'sales_price' => '11.00',
                'market_price' => '22.00',
            'detail' => '<p><a title="胡桃里(文殊院店)">胡桃里(文殊院店)</a></p>
<p>地址：成都市青羊区白云寺街29号<br />电话：(028)86911511<br />坐标：104.080257,30.678898</p>',
                'img_id' => 1,
                'sort' => 3,
                'sales_number' => 3,
                'status' => 1,
                'tags' => '太香了',
                'lng' => '104.080257',
                'lat' => '30.678898',
                'deleted_at' => NULL,
                'created_at' => '2020-07-11 16:13:50',
                'updated_at' => '2020-07-15 21:20:43',
            ),
            1 => 
            array (
                'id' => 6,
                'goods_code' => 'YC2020071117420500001',
            'name' => '音乐房子(兰桂坊店)',
            'title' => '音乐房子(兰桂坊店)',
            'address' => '成都锦江区兰桂坊水津街13号名人堂1楼(近兰桂坊)',
                'goods_category_id' => 7,
                'sales_price' => '1.00',
                'market_price' => '1.00',
            'detail' => '<p><a title="音乐房子(兰桂坊店)">音乐房子(兰桂坊店)</a></p>
<p>地址：成都锦江区兰桂坊水津街13号名人堂1楼(近兰桂坊)<br />电话：(028)83387337<br />坐标：104.091182,30.650242</p>',
                'img_id' => 1,
                'sort' => 1,
                'sales_number' => 1,
                'status' => 1,
                'tags' => '真便宜'
                'lng' => '104.091182',,
                'lat' => '30.650242',
                'deleted_at' => NULL,
                'created_at' => '2020-07-11 17:42:05',
                'updated_at' => '2020-07-15 21:18:23',
            ),
        ));
        
        
    }
}