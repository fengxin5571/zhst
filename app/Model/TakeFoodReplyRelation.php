<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/1
 * Time: 2:25 PM
 */
namespace App\Model;
use Reliese\Database\Eloquent\Model as Eloquent;


class TakeFoodReplyRelation extends Eloquent{
    protected $table = 'take_food_reply_relation';
    public $timestamps = true;
    protected $fillable=[
        'userid',
        't_food_id',
        'type',
    ];

}