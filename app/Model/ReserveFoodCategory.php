<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 09 Jul 2019 09:42:27 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ReserveFoodCategory
 * 
 * @property int $id
 * @property string $cat_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class ReserveFoodCategory extends Eloquent
{
	protected $table = 'reserve_food_category';

	protected $fillable = [
		'cat_name'
	];
}
