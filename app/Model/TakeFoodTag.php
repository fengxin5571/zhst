<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 30 Jun 2019 09:18:56 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class TakeFoodTag
 * 
 * @property int $id
 * @property string $t_tag_name
 *
 * @package App\Models
 */
class TakeFoodTag extends Eloquent
{
	protected $table = 'take_food_tag';
	public $timestamps = false;

	protected $fillable = [
		't_tag_name'
	];
}
