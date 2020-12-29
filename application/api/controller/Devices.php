<?php
/**
 * Created by PhpStorm.
 * User: 钟焯权
 * Date: 2020/11/27
 * Time: 10:48
 */
namespace app\api\controller;
use think\Controller;
use app\common\model\Devices as DevicesMode;
use think\facade\Request;
use app\common\model\User as UserMode;
class Devices extends Controller
{
    public function devices_register_api()
    {
        $data = Request::param();
        $res = DevicesMode::get(['name'=>$data['name']]);
        if($data['name'] == null)
        {
            $message = ['status' => 401 , 'message'=>'设备ID不能为空'];
            return json_encode($message);
        }
        if(substr($data['name'],0,6) !='KHY-PD')
        {

            $message = ['status' => 402 , 'message'=>'设备编号有错'];
            return json_encode($message);
        }
        if(strlen($data['name']) !=15)
        {
            $message = ['status' => 403 , 'message'=>'设备编号长度不符合'];
            return json_encode($message);
        }
        if($res != null)
        {
            $message = ['status' => 402 , 'message'=>'设备ID已存在'];
            return json_encode($message);
        }
        $data['create_time'] = date('Y-m-d H:i:s');
        $dev = DevicesMode::create($data);
        if($dev)
        {
            $user = UserMode::create(['devices_id'=>$dev['id'],'name'=>'energy','password'=>'energy','create_time'=>$data['create_time'],'status'=>1]);
            if($user)
            {
                $message = ['status' => 200 , 'message'=>'注册成功'];
                return json_encode($message);
            }else
            {
                $message = ['status' => 400 , 'message'=>'管理员账号出错'];
                return json_encode($message);
            }
        }
        $message = ['status' => 500 , 'message'=>'发生未知错误'];
        return($message);
    }
}