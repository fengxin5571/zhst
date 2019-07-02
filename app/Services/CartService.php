<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/2
 * Time: 10:13 AM
 */
namespace App\Services;

use App\Model\Cart;
use App\Model\TakeFoodPool;

class CartService{
    /**
     * 添加购物车
     * @param $food_id
     * @param $num
     * @param $type
     * @param $user
     * @return mixed
     */
    public function add($food_id,$num,$type,$user,$flog){
        $foodsku=$type==1?TakeFoodPool::class:'';
        if ($item =Cart::where(['userid'=>$user['userId'],'type'=>$type,'food_id'=>$food_id])->first()) {
            // 如果存在则直接叠加或减少菜品数量
            if($flog=="+"){
                $item->increment('cart_num',$num);
            }else{
                if($item->cart_num>$num&&$item->cart_num>1){
                    $item->decrement('cart_num',$num);
                }else{
                    $item->delete();
                }
            }
            $item['foodCountPrice']=bcmul($foodsku::find($food_id)->price,$item->cart_num,2);
        } else {
            if($flog=="+"){
                // 否则创建一个新的购物车记录
                $item =Cart::create(['userid'=>$user['userId'],'type'=>$type,'food_id'=>$food_id,'cart_num'=>$num]);
                $item['foodCountPrice']=bcmul($foodsku::find($food_id)->price,$item->cart_num,2);
            }

        }
        return $item;
    }

    public function remove($type,$user)
    {
        return Cart::where(['userid'=>$user['userId'],'type'=>$type])->delete();
    }
}