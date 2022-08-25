<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/10
 * Time: 1:06 PM
 */
namespace App\Model;
use Reliese\Database\Eloquent\Model as Eloquent;


class ReserveFoodReplyRelation extends Eloquent{
    protected $table = 'reserve_food_reply_relation';
    public $timestamps = true;
    protected $fillable=[
        'userid',
        'r_food_id',
        'type'
    ];

}