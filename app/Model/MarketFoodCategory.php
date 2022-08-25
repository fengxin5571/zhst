<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 08 Sep 2019 10:17:23 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class MarketFoodCategory
 * 
 * @property int $id
 * @property string $cat_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class MarketFoodCategory extends Eloquent
{
	protected $table = 'market_food_category';

	protected $fillable = [
		'cat_name'
	];
}
