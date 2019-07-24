<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 02 Jul 2019 18:14:59 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Order
 * 
 * @property int $id
 * @property string $order_sn
 * @property int $order_type
 * @property string $userid
 * @property string $real_name
 * @property string $user_phone
 * @property string $user_address
 * @property \Carbon\Carbon $eat_time
 * @property \Carbon\Carbon $get_time
 * @property int $total_num
 * @property float $total_price
 * @property int $paid
 * @property \Carbon\Carbon $pay_time
 * @property string $pay_type
 * @property int $status
 * @property float $refund_price
 * @property string $mark
 * @property string $unique
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class Order extends Eloquent
{
	protected $table = 'order';

	protected $casts = [
		'order_type' => 'int',
		'total_num' => 'int',
		'total_price' => 'float(10,2)',
        'box_charges' =>'float(10,2)',
		'paid' => 'int',
		'status' => 'int',
		'refund_price' => 'float(10,2)'
	];

	protected $dates = [
		'eat_time',
		'get_time',
		'pay_time'
	];

	protected $fillable = [
		'order_sn',
		'order_type',
        'reserve_type',
		'userid',
		'real_name',
		'user_phone',
		'user_address',
		'eat_time',
		'get_time',
		'total_num',
		'total_price',
		'paid',
		'pay_time',
		'pay_type',
		'status',
		'refund_price',
        'refund_status',
		'mark',
		'unique',
        'eat_people',
        'box_charges'
	];
    //订单单号
    static function findAvailableNo()
    {
        // 订单流水号前缀
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // 随机生成 6 位的数字
            $no = $prefix.str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!static::query()->where('order_sn', $no)->exists()) {
                return $no;
            }
            usleep(100);
        }
        return $no;
    }
    //订单菜品
    public function orderFoods(){
        return $this->hasMany(OrderFood::class,'order_unique','unique');
    }
}
