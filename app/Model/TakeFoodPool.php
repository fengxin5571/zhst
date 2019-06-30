<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 25 Jun 2019 19:26:17 +0800.
 */

namespace App\Model;

use Illuminate\Support\Facades\Storage;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class TakeFoodPool
 * 
 * @property int $id
 * @property int $cid
 * @property string $name
 * @property string $description
 * @property string $info
 * @property string $food_ image
 * @property string $slider_image
 * @property int $sellCount
 * @property int $likeCount
 * @property int $is_show
 * @property int $mer_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class TakeFoodPool extends Eloquent
{
	protected $table = 'take_food_pool';
	protected $casts = [
		'cid' => 'int',
		'sellCount' => 'int',
		'likeCount' => 'int',
		'is_show' => 'int',
		'mer_id' => 'int',
        'weight' =>'float(10,2)',
        'ot_price' =>'float(10,2)',
        'price'    =>'float(10,2)'
	];

	protected $fillable = [
		'cid',
		'name',
		'description',
		'info',
		'food_image',
		'slider_image',
		'sellCount',
		'likeCount',
		'is_show',
		'mer_id',
        'weight',
        'calorie',
        'ot_price',
        'is_recommend',
        'is_today'
	];
	public static function boot(){
	    parent::boot();
        static ::deleted(function($takeFood){
            @Storage::disk('admin')->delete($takeFood->food_image);
        });
    }
	public function foodCategory(){
	    return $this->hasOne(TakeFoodCategory::class,'id','cid');
    }
	public function tags(){
	    return $this->belongsToMany(TakeFoodTag::class,'takefood_tag_relation','t_food_id','t_tag_id');
    }
}
