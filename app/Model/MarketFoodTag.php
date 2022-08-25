<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 08 Sep 2019 10:30:01 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class MarketFoodTag
 * 
 * @property int $id
 * @property string $m_tag_name
 *
 * @package App\Models
 */
class MarketFoodTag extends Eloquent
{
	protected $table = 'market_food_tag';
	public $timestamps = false;

	protected $fillable = [
		'm_tag_name'
	];
}
