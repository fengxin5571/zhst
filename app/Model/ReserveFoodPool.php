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
		'point' => 'int',
		'likeCount' => 'int',
		'is_show' => 'int',
		'mer_id' => 'int',
		'food_type' => 'int',
        'price'    =>'float(10,2)'
	];

	protected $fillable = [
		'cid',
		'name',
		'description',
		'food_image',
		'point',
		'sellCount',
		'likeCount',
		'is_show',
        'is_new',
		'mer_id',
		'food_type',
        'is_today',
        'cook',
        'cook_image',
        'cook_speciality',
        'cook_group',
        'price',
        'is_today_leader',
        'is_today_employ',
        'health_images',
        'health_content',
        'is_health',
        'is_exchange',
        'calorie',
        'ex_content',
        'is_recommend'
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
        if($value){
            return config('filesystems.disks.admin.url').'/'.$value;
        }else{
            return '';
        }
    }
    public function getCookImageAttribute($value)
    {
        if($value){
            return config('filesystems.disks.admin.url').'/'.$value;
        }else{
            return '';
        }

    }
    public function getIsTodayLeaderAttribute($value){
        return explode(',',$value);
    }
    public function getIsTodayEmployAttribute($value){
        return explode(',',$value);
    }
    public function getIsRecommendAttribute($value){
        return explode(',',$value);
    }
    public function setIsTodayLeaderAttribute($value){
        $this->attributes['is_today_leader'] = implode(',',$value);
    }
    public function setIsTodayEmployAttribute($value){
        $this->attributes['is_today_employ'] = implode(',',$value);
    }
    public function setIsRecommendAttribute($value){
        $this->attributes['is_recommend'] = implode(',',$value);
    }
    public function setHealthImagesAttribute($image)
    {
        if (is_array($image)) {
            foreach ($image as $k=>$value){
                $image[$k]=config('filesystems.disks.admin.url').'/'.$value;
            }
            $this->attributes['health_images'] = json_encode($image);
        }
    }

    public function getHealthImagesAttribute($image)
    {
        return json_decode($image, true);
    }
    //上架网订菜品
    public function scopeIsShow($query){
        return $query->where('is_show',1);
    }
	//菜品分类
    public function foodCategory(){
        return $this->hasOne(ReserveFoodCategory::class,'id','cid');
    }
	//菜品标签
	public function tags(){
        return $this->belongsToMany(ReserveFoodTag::class,'reservefood_tag_relation','r_food_id','r_tag_id');
    }
    //菜品评论
    public function comments(){
        return $this->hasMany(ReserveFoodReply::class,'food_id','id');
    }
}
