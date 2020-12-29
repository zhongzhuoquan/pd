<?php
/**
 * Created by PhpStorm.
 * User: 钟焯权
 * Date: 2020/11/27
 * Time: 10:48
 */
namespace app\index\controller;
use think\Controller;
use app\common\model\User as UserMode;
use think\facade\Request;
class Api extends Controller
{   
 //用户登录接口
    public function user_login_api()
    {
        $data = Request::param();
        if($data['name'] == null )
        {
            $message=['status' => 400 , 'message' => '用户名不能为空'];
            return json_encode($message);
        }
        if($data['password'] == null )
        {
            $message=['status' => 401 , 'message' => '密码不能为空'];
            return json_encode($message);
        }
        $res = UserMode::get($data);

        if($res != null )
        {
            $message=['status' => 200 , 'message' => '登录成功'];
            return json_encode($message);
        }
        else
        {
            $message=['status' => 402 , 'message' => '用户名或者密码错误'];
            return json_encode($message);
        }
    }
}