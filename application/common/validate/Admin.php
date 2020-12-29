<?php
/**
 * Created by PhpStorm.
 * Admin: Administrator
 * Date: 2019/5/17 0017
 * Time: 15:45
 */
namespace app\common\validate;
use think\Validate;
class Admin extends Validate{
    protected $rule=[
        'name|用户名'=> 'require|length:1,20|chsAlphaNum|unique:admin',
        'phone|手机号'=> 'require|mobile',
        'password|密码'=> 'require|length:1,20|chsAlphaNum|confirm',
        'role_id|角色'=>'require'
        //chsAlphaNum'只允许汉字，字母和数字
    ];
}