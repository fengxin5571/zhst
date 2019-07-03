<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 03 Jul 2019 17:08:37 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class OrderFood
 * 
 * @property int $id
 * @property string $order_unique
 * @property string $food_name
 * @property float $food_price
 * @property string $food_image
 * @property int $food_type
 * @property int $food_num
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class OrderFood extends Eloquent
{
	protected $table = 'order_food';

	protected $casts = [
		'food_price' => 'float',
		'food_type' => 'int',
		'food_num' => 'int'
	];

	protected $fillable = [
		'order_unique',
		'food_name',
		'food_price',
		'food_image',
		'food_type',
		'food_num'
	];
}
