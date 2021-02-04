<?php


namespace app\model;

use think\Model;


class Order extends Model
{
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'commodity_name'        => 'string',
        'pay_type'      => 'int',
        'order_no' =>  'string',
        'newpay_no' => 'string',
        'status'       => 'string',
        'price' => 'int',
        'create_time' => 'datetime',
        'end_time' => 'datetime',
    ];
}