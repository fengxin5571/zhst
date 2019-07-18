<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 10 Jul 2019 08:15:53 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ReserveFoodPool
 * 
 * @property int $id
 * @property int $cid
 * @property string $name
 * @property string $description
 * @property string $food_image
 * @property float $price
 * @property float $ot_price
 * @property int $point
 * @property int $sellCount
 * @property int $likeCount
 * @property int $is_show
 * @property int $mer_id
 * @property float $weight
 * @property int $calorie
 * @property int $food_type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class ReserveFoodPool extends Eloquent
{
	protected $table = 'reserve_food_pool';
    protected static $pid=0;
	protected $casts = [
		'cid' => 'int',
		'price' => 'float(10,2)',
		'ot_price' => 'float(10,2)',
		'point' => 'int',
		'sellCount' => 'int',
		'likeCount' => 'int',
		'is_show' => 'int',
		'mer_id' => 'int',
		'weight' => 'float(10,2)',
		'calorie' => 'int',
		'food_type' => 'int'
	];

	protected $fillable = [
		'cid',
		'name',
		'description',
		'food_image',
		'price',
		'ot_price',
		'point',
		'sellCount',
		'likeCount',
		'is_show',
		'mer_id',
		'weight',
		'calorie',
		'food_type',
        'cook'
	];
    public static function boot(){
        parent::boot();
        static ::deleting(function ($reserveFood){
            self::$pid=$pid=PackageFoodRelation::where('food_id',$reserveFood->id)->value('pid');
        });
        static ::deleted(function($reserveFood){
              if(self::$pid){
                  $package_food_count=PackageFood::where('id',self::$pid)->first()->foods->count();
                  if($package_food_count==0){
                      PackageFood::where('id',self::$pid)->delete();
                  }
              }
        });
    }
    public function getFoodImageAttribute($value)
    {
        return config('filesystems.disks.admin.url').'/'.$value;
    }
	//菜品分类
    public function foodCategory(){
        return $this->hasOne(ReserveFoodCategory::class,'id','cid');
    }
	//菜品标签
	public function tags(){
        return $this->belongsToMany(ReserveFoodTag::class,'reservefood_tag_relation','r_food_id','r_tag_id');
    }
}
