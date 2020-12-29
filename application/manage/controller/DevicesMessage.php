<?php
/**
 * Created by PhpStorm.
 * User: 钟焯权
 * Date: 2020/12/14
 * Time: 16:25
 */

namespace app\manage\controller;

use app\common\model\Devices as DevicesMode;
use think\Controller;
use think\facade\Log;
use think\facade\Request;

class DevicesMessage extends Controller
{
//获取设备信息
    public function get_devices_message()
    {
        $data = Request::param();
        if($data)
        {
            $data = str_replace('\\&quot;', '"', $data);
            $data = json_decode($data['data'], true);
            $res = DevicesMode::where('name', 'like', '%' . $data['devices_name'] . '%')
                ->order('name asc')
                ->select();
            if ($res) {
                $message = ['status' => 200, 'message' => $res];
                return json_encode($message);
            }
            $message = ['status' => 400, 'message' => '发生未知错误'];
            return json_encode($message);
        }
        else
        {
            $message = ['status' => 300, 'message' => '参数发生未知错误'];
            return json_encode($message);
        }
    }
    //删除一个设备
    public function delete_devices_api()
    {
        $data = Request::param();
        if($data != null) {
            if($data)
                $res = DevicesMode::where('id', $data['id'])->delete();
            if($res != null)
            {
                $message = ['status'=>200 , 'message' =>'删除成功'];
                return json_encode($message);
            }
            else
            {
                $message = ['status'=>400 , 'message' =>'删除失败'];
                return json_encode($message);
            }
        }
        else
        {
            $message = ['status'=>300 , 'message' =>'参数错误'];
            return json_encode($message);
        }
    }
    //修改设备信息
    public function change_devices_message()
    {
        $data = Request::param();
        if ($data)
        {

            $data = str_replace('\\&quot;', '"', $data);
            $data = json_decode($data['data'], true);
            if(strlen($data['phone'])!=11)
            {
                $message = ['status'=>401 , 'message' =>'手机号长度错误'];
                return json_encode($message);
            }
            if($data['uname'] == null)
            {
                $message = ['status'=>402 , 'message' =>'持有人不能为空'];
                return json_encode($message);
            }
            if($data['address'] == null)
            {
                $message = ['status'=>403 , 'message' =>'地址不能为空'];
                return json_encode($message);
            }
            $res = DevicesMode::where('id',$data['id'])->update(['uname'=>$data['uname'],'address'=>$data['address'],'phone'=>$data['phone']]);
            if($res)
            {
                $message = ['status'=>200 , 'message' =>'修改成功'];
                return json_encode($message);
            }
            else
            {
                $message = ['status'=>400 , 'message' =>'修改失败'];
                return json_encode($message);
            }
        }
        else
        {
            $message = ['status'=>300 , 'message' =>'参数错误'];
            return json_encode($message);
        }
    }
    //修改可使用次数
    public function change_available_count_api()
    {
        $data = Request::param();
        if($data) {
            $data = str_replace('\\&quot;', '"', $data);
            $data = json_decode($data['data'], true);
            $res = DevicesMode::where('id',$data['id'])->update(['available_count'=>$data['available_count']]);
            if($res)
            {
                $message = ['status'=>200 , 'message' =>'修改成功'];
                return json_encode($message);
            }
            else
            {
                $message = ['status' => 300, 'message' => '发生未知错误'];
                return json_encode($message);
            }
        }
        else
        {
            $message = ['status'=>400 , 'message' =>'参数错误'];
            return json_encode($message);
        }
    }
}