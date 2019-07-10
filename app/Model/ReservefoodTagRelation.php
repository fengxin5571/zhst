<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 10 Jul 2019 09:10:58 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ReservefoodTagRelation
 * 
 * @property int $id
 * @property int $r_food_id
 * @property int $r_tag_id
 *
 * @package App\Models
 */
class ReservefoodTagRelation extends Eloquent
{
	protected $table = 'reservefood_tag_relation';
	public $timestamps = false;

	protected $casts = [
		'r_food_id' => 'int',
		'r_tag_id' => 'int'
	];

	protected $fillable = [
		'r_food_id',
		'r_tag_id'
	];
}
