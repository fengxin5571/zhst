<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 09 Jul 2019 08:31:20 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ReserveType
 * 
 * @property int $id
 * @property string $reserve_type_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class ReserveType extends Eloquent
{
	protected $table = 'reserve_type';
    protected $casts = [
        'reserve_price'    =>'float(10,2)'
    ];
	protected $fillable = [
	    'reserve_type_seting',
		'reserve_type_name',
        'reserve_type_image',
        'reserve_price'
	];
    public function getReserveTypeImageAttribute($value)
    {
        return config('filesystems.disks.admin.url').'/'.$value;
    }
}
