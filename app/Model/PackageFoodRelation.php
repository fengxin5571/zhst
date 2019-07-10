<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 11 Jul 2019 02:12:16 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class PackageFoodRelation
 * 
 * @property int $id
 * @property int $pid
 * @property int $food_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class PackageFoodRelation extends Eloquent
{
	protected $table = 'package_food_relation';

	protected $casts = [
		'pid' => 'int',
		'food_id' => 'int'
	];

	protected $fillable = [
		'pid',
		'food_id'
	];
}
