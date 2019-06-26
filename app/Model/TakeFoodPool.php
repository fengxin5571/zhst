<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 25 Jun 2019 19:26:17 +0800.
 */

namespace App\Model;

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
		'mer_id' => 'int'
	];

	protected $fillable = [
		'cid',
		'name',
		'description',
		'info',
		'food_ image',
		'slider_image',
		'sellCount',
		'likeCount',
		'is_show',
		'mer_id'
	];
}
