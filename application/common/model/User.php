<?php
/**
 * Created by PhpStorm.
 * Admin: Administrator
 * Date: 2020/5/23
 * Time: 9:40
 */
namespace app\common\model;
use think\Model;
class User extends Model
{
    protected $pk='id';//默认主键
    protected $table='user';//默认数据表
}