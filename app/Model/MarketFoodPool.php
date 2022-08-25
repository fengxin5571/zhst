<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 08 Sep 2019 10:48:21 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class MarketFoodPool
 * 
 * @property int $id
 * @property int $cid
 * @property string $name
 * @property string $description
 * @property string $info
 * @property string $food_image
 * @property string $slider_image
 * @property int $stock
 * @property float $price
 * @property float $ot_price
 * @property int $point
 * @property int $sellCount
 * @property int $likeCount
 * @property int $is_show
 * @property int $mer_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $food_type
 *
 * @package App\Models
 */
class MarketFoodPool extends Eloquent
{
	protected $table = 'market_food_pool';

	protected $casts = [
		'cid' => 'int',
		'stock' => 'int',
		'price' => 'float(10,2)',
		'ot_price' => 'float(10,2)',
		'point' => 'int',
		'sellCount' => 'int',
		'likeCount' => 'int',
		'is_show' => 'int',
		'mer_id' => 'int',
		'food_type' => 'int'
	];

	protected $fillable = [
		'cid',
		'name',
		'description',
		'info',
		'food_image',
		'slider_image',
		'stock',
		'price',
		'ot_price',
		'point',
		'sellCount',
		'likeCount',
		'is_show',
        'is_new',
		'mer_id',
		'food_type'
	];
    public function getFoodImageAttribute($value)
    {
        return config('filesystems.disks.admin.url').'/'.$value;
    }
    //上架网超菜品
    public function scopeIsShow($query){
        return $query->where('is_show',1);
    }
    public function foodCategory(){
        return $this->hasOne(MarketFoodCategory::class,'id','cid');
    }
    public function tags(){
        return $this->belongsToMany(MarketFoodTag::class,'marketfood_tag_relation','m_food_id','m_tag_id');
    }
}
