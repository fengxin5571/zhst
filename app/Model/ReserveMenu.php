<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 10 Jul 2019 18:22:54 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ReserveMenu
 * 
 * @property int $id
 * @property int $food_id
 * @property int $food_type
 * @property string $weekly
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class ReserveMenu extends Eloquent
{
	protected $table = 'reserve_menu';

	protected $casts = [
		'food_id' => 'int',
		'food_type' => 'int'
	];
	protected $fillable = [
	    'reserve_type',
		'food_id',
		'food_type',
		'weekly'
	];
	public function getWeeklyAttribute($value){
	    return explode(',',$value);
    }
    public function reserveType(){
	    return $this->hasOne(ReserveType::class,'id','reserve_type');
    }
}
