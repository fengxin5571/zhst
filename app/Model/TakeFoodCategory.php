<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 30 Jun 2019 08:39:32 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class TakeFoodCategory
 * 
 * @property int $id
 * @property string $cat_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class TakeFoodCategory extends Eloquent
{
	protected $table = 'take_food_category';

	protected $fillable = [
		'cat_name'
	];
}
