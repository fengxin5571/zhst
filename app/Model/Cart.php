<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 02 Jul 2019 11:09:06 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Cart
 * 
 * @property int $id
 * @property string $userid
 * @property int $type
 * @property int $food_id
 * @property int $cart_num
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class Cart extends Eloquent
{
	protected $table = 'cart';

	protected $casts = [
		'type' => 'int',
		'food_id' => 'int',
		'cart_num' => 'int'
	];

	protected $fillable = [
		'userid',
		'type',
		'food_id',
		'cart_num',
        'food_type'
	];
	//外卖菜品
	public function takeFood(){
	    return $this->hasOne(TakeFoodPool::class,'id','food_id');
    }
    //网超菜品
    public function marketFood(){
        return $this->hasOne(MarketFoodPool::class,'id','food_id');
    }
    //网订菜品
    public function reserveFood(){
        return $this->hasOne(ReserveFoodPool::class,'id','food_id');
    }
}
