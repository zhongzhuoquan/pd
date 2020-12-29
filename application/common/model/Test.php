<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/5/23
 * Time: 9:40
 */
namespace app\common\model;
use think\Model;
class Test extends Model
{
    protected $pk='id';//默认主键
    protected $table='test';//默认数据表
}