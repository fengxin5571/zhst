<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 10 Jul 2019 08:38:30 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ReserveFoodTag
 * 
 * @property int $id
 * @property string $r_tag_name
 *
 * @package App\Models
 */
class ReserveFoodTag extends Eloquent
{
	protected $table = 'reserve_food_tag';
	public $timestamps = false;

	protected $fillable = [
		'r_tag_name'
	];
}
