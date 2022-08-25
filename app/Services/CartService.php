<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/2
 * Time: 10:13 AM
 */
namespace App\Services;

use App\Model\Cart;
use App\Model\MarketFoodPool;
use App\Model\ReserveFoodPool;
use App\Model\TakeFoodPool;
use Illuminate\Support\Facades\DB;

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
        $item['carts']=Cart::where(['userid'=>$user['userId'],'type'=>$type,'food_type'=>$food_type,'food_id'=>$food_id])->select(['id','type','food_type','type','food_id','cart_num','updated_at','created_at'])->first();
        $item['error']=false;
        $item['message']='添加成功';
        $foodLimit=0;
        $foodStock=0;
        if($type==1){//如果是外卖
            if($food_type==1){//如果是普通菜品
                $foodsku=TakeFoodPool::class;
            }else{//如果是套餐

            }
            //限购数量
            $foodLimit=$foodsku::find($food_id)->limit;
            //库存
            $foodStock=$foodsku::find($food_id)->stock;
        }elseif($type==3){//如果是网超
            $foodsku=MarketFoodPool::class;
            //库存
            $foodStock=100000;
        }elseif ($type==2){
            $foodsku=ReserveFoodPool::class;
            //库存
            $foodStock=100000;
        }
        if ($item['carts']) {
            // 如果存在则直接叠加或减少菜品数量
            if($flog=="+"){
                //超过限购不让添加购物车
                if($foodLimit>0&&$item['carts']->cart_num>=$foodLimit){
                    $item['error']=true;
                    $item['message']='已超出限购数量';
                    return $item;
                }
                if($num>$foodStock){
                    $item['error']=true;
                    $item['message']='该菜品库存不足';
                    return $item;
                }
                $item['carts']->increment('cart_num',$num);
                if($type==1){
                    $foodsku::find($food_id)->decrement('stock',$num);
                }
            }else{
                if($item['carts']->cart_num>$num&&$item['carts']->cart_num>1){
                    $item['carts']->decrement('cart_num',$num);
                }else{
                    $item['carts']->delete();
                    $item['carts']=null;
                }
                if($type==1){
                    $foodsku::find($food_id)->increment('stock',$num);
                }

            }
            if($item['carts']){
                $item['carts']['foodCountPrice']=bcmul($foodsku::find($food_id)->price,$item['carts']->cart_num,2);
            }else{
                $item['carts']['cart_num']=0;
                $item['carts']['foodCountPrice']=0.00;
            }

        } else {
            if($flog=="+"){
                // 否则创建一个新的购物车记录
                if($foodLimit>0&&$num>$foodLimit){//超过限购不让添加购物车
                    $item['error']=true;
                    $item['message']='已超出限购数量';
                    return $item;
                }
                if($num>$foodStock){
                    $item['error']=true;
                    $item['message']='该菜品库存不足';
                    return $item;
                }
                $item['carts'] =Cart::create(['userid'=>$user['userId'],'type'=>$type,'food_type'=>$food_type,'food_id'=>$food_id,'cart_num'=>$num]);
                $item['carts']['foodCountPrice']=bcmul($foodsku::find($food_id)->price,$item['carts']->cart_num,2);
                if($type==1){
                    $foodsku::find($food_id)->decrement('stock',$num);
                }

            }

        }
        return $item;
    }

    public function remove($type,$user)
    {
        $data['error']=false;
        $data['message']='清空购物车成功';
        try{
            DB::beginTransaction();
            $cart_list=Cart::where(['userid'=>$user['userId'],'type'=>$type])->get();
            $cart_list->each(function ($item,$key){
                if($item->type==1){//如果是外卖
                    $food=TakeFoodPool::find($item->food_id);
                    $food->increment('stock',$item->cart_num);
                }elseif ($item->type==3){//如果是网超
//                    $food=MarketFoodPool::find($item->food_id);
//                    $food->increment('stock',$item->cart_num);
                }
            });
            Cart::where(['userid'=>$user['userId'],'type'=>$type])->delete();
            DB::commit();
        }catch (\Exception $e){
            $data['error']=true;
            $data['message']='清空购物车失败';
            DB::rollBack();
        }
        return $data;
    }
}