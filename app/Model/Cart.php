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
		'cart_num'
	];
}
