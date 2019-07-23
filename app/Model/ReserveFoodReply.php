<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 23 Jul 2019 08:37:11 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ReserveFoodReply
 * 
 * @property int $id
 * @property string $userid
 * @property string $avatar
 * @property string $reply_name
 * @property int $food_id
 * @property string $comment
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class ReserveFoodReply extends Eloquent
{
	protected $table = 'reserve_food_reply';

	protected $casts = [
		'food_id' => 'int'
	];

	protected $fillable = [
		'userid',
		'avatar',
		'reply_name',
		'food_id',
		'comment'
	];
}
