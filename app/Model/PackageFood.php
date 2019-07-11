<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 10 Jul 2019 13:34:28 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class PackageFood
 * 
 * @property int $id
 * @property string $package_name
 * @property string $package_image
 * @property string $package_description
 * @property float $package_price
 * @property int $is_show
 * @property int $package_type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class PackageFood extends Eloquent
{
	protected $table = 'package_food';

	protected $casts = [
		'package_price' => 'float(10,2)',
		'is_show' => 'int',
	];

	protected $fillable = [
		'package_name',
		'package_image',
		'package_description',
		'package_price',
		'is_show',
        'food_type',
	];
	public function getPackageImageAttribute($value){
        return config('filesystems.disks.admin.url').'/'.$value;
    }
	public function foods(){
	    return $this->belongsToMany(ReserveFoodPool::class,'package_food_relation','pid','food_id');
    }
}
