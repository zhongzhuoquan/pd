<?php
/**
 * Created by PhpStorm.
 * Admin: Administrator
 * Date: 2019/5/15 0015
 * Time: 15:35
 */

namespace app\facade;
use think\Facade;

class Admin extends Facade {
    protected static function getFacadeClass()
    {
         return 'app\common\validate\Admin';
    }
}