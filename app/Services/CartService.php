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
    public function add($food_id,$num,$type,$food_type,$user,$flog){

        if($type==1){//如果是外卖菜
            if($food_type==1){//如果是普通菜品
                $foodsku=TakeFoodPool::class;
            }else{//如果是套餐

            }
        }else{

        }
        if ($item =Cart::where(['userid'=>$user['userId'],'type'=>$type,'food_type'=>$food_type,'food_id'=>$food_id])->select(['id','type','food_type','type','food_id','cart_num'])->first()) {
            // 如果存在则直接叠加或减少菜品数量
            if($flog=="+"){
                $item->increment('cart_num',$num);
            }else{
                if($item->cart_num>$num&&$item->cart_num>1){
                    $item->decrement('cart_num',$num);
                }else{
                    $item->delete();
                    $item=null;
                }
            }
            if($item){
                $item['foodCountPrice']=bcmul($foodsku::find($food_id)->price,$item->cart_num,2);
            }else{
                $item['cart_num']=0;
                $item['foodCountPrice']=0.00;
            }

        } else {
            if($flog=="+"){
                // 否则创建一个新的购物车记录
                $item =Cart::create(['userid'=>$user['userId'],'type'=>$type,'food_type'=>$food_type,'food_id'=>$food_id,'cart_num'=>$num]);
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