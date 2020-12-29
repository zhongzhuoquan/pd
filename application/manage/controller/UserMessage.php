<?php
/**
 * Created by PhpStorm.
 * User: 钟焯权
 * Date: 2020/12/14
 * Time: 16:26
 */

namespace app\manage\controller;
use think\Controller;
use think\facade\Request;
use app\common\model\User as UserMode;
use think\Db;
class UserMessage extends Controller
{
    //获取用户信息
    public function get_user_message()
    {
        $data = Request::param();
        if($data) {
            $data = str_replace('\\&quot;', '"', $data);
            $data = json_decode($data['data'], true);
            $res = Db::table('devices')
                ->alias('d')
                ->leftJoin('user u', 'd.id = u.devices_id')
                ->where('u.name', 'like', '%' . $data['user_name'] . '%')
                ->where('d.name', 'like', '%' . $data['devices_name'] . '%')
                ->field('u.id,u.name as username ,d.name as devicesname , u.create_time ,u.login_time ,u.phone,u.password')
                ->order('d.name asc')
                ->order('u.create_time asc')
                ->select();
            if (isset($res))
            {
                $message = ['status' => 200, 'message' => $res];
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
            $message = ['status' => 400, 'message' => '参数错误'];
            return json_encode($message);
        }
    }

    //删除一个用户
    public function delete_user_api()
    {
        $data = Request::param();
        if($data['id'] !=null) {
            $res = UserMode::where('id', $data['id'])->delete();
            if($res !=null)
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
            $message = ['status'=>401 , 'message' =>'参数错误'];
            return json_encode($message);
        }
    }
    //删除多个用户
    public function delete_user_all_api()
    {
        $data = Request::param();
        if ($data['id'] !=null)
        {
            $res = UserMode::where('id','in', $data['id'])->delete();
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
            $message = ['status' => 401, 'message' => '参数错误'];
            return json_encode($message);
        }
    }

    public function change_available_count_api()
    {
        $data = Request::param();
        if($data) {
            $data = str_replace('\\&quot;', '"', $data);
            $data = json_decode($data['data'], true);
            $res = UserMode::where('id',$data['id'])->update(['available_count'=>$data['available_count']]);
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