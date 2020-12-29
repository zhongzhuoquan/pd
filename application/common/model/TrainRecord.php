<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/5/23
 * Time: 9:40
 */
namespace app\common\model;
use think\Model;
class TrainRecord extends Model
{
    protected $pk='id';//默认主键
    protected $table='train_record';//默认数据表
}