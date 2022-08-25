<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 12 Sep 2019 16:28:31 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Notice
 * 
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $userid
 * @property \Carbon\Carbon $add_time
 *
 * @package App\Models
 */
class Notice extends Eloquent
{
	protected $table = 'notice';
	public $timestamps = true;

	protected $dates = [
		'created_at',
        'updated_at'
	];

	protected $fillable = [
		'title',
		'content',
		'add_time'
	];
}
