<?php
/**
 * Created by PhpStorm.
 * User: 钟焯权
 * Date: 2020/11/30
 * Time: 13:43
 */

namespace app\api\controller;
use think\facade\Request;
use think\Db;
use think\Controller;
use app\common\model\TestRecord as TestRecordMode;
use app\common\model\User as UserMode;
use app\common\model\Devices as DevicesMode;
class TestMode extends Controller
{
    public function enduranceMuscleTest_record_save_api()
    {
        $data = Request::param();
        if(!$data)
        {
                $message = ['status' => 400 , 'message' => '参数错误'];
                return json_encode($message);
        }
        $data['create_time'] = date('Y-m-d H:i:s');
	$user = Db::table('user')
                            ->alias('u')
                            ->leftJoin('devices d','d.id = u.devices_id')
                            ->where('d.name',$data['devices_id'])
                            ->where('u.name',$data['name'])    
			    ->field('u.id')                 
                            ->find();
        $data['user_id'] = $user['id'];
        unset($data['name']);
        $data['devices_id'] = DevicesMode::where('name',$data['devices_id'])->value('id');
        $res = TestRecordMode::create($data);
        if($res)
        {
            $message = ['status' => 200 , 'message' => '保存成功'];
            return json_encode($message);
        }
        else
        {
            $message = ['status' => 500 , 'message' => '发生未知错误'];
            return json_encode($message);
        }

    }
    public function enduranceMuscleTest_record_get_api(){
        $data = Request::param();
        if(!$data)
        {
            $message = ['status' => 400 , 'message' => '参数错误'];
            return json_encode($message);
        }
	$user = Db::table('user')
                            ->alias('u')
                            ->leftJoin('devices d','d.id = u.devices_id')
                            ->where('d.name',$data['devices_id'])
                            ->where('u.name',$data['name'])    
			    ->field('u.id')                 
                            ->find();
        $res = TestRecordMode::where('devices_id',DevicesMode::where('name',$data['devices_id'])->value('id'))
            ->where('user_id',$user['id'])
            ->select();
        if($res)
        {
            $message = ['status' => 200 , 'message' => $res ];
            return json_encode($message);
        }
        else
        {
            $message = ['status' => 300 , 'message' => $res];
            return json_encode($message);
        }
        $message = ['status' => 500 , 'message' => '发生未知错误'];
        return json_encode($message);
    }
    public function enduranceMuscleTest_record_delete_api()
    {
        $data = Request::param();
        if(!$data)
        {
            $message = ['status' => 400 , 'message' => '参数错误'];
            return json_encode($message);
        }
        $user = Db::table('user')
            ->alias('u')
            ->leftJoin('devices d','d.id = u.devices_id')
            ->where('d.name',$data['devices_id'])
            ->where('u.name',$data['name'])
            ->field('u.id')
            ->find();

        $res = TestRecordMode::where('devices_id',DevicesMode::where('name',$data['devices_id'])->value('id'))
            ->where('user_id',$user['id'])
            ->where('id',$data['id'])
            ->delete();
        if($res)
        {
            $message = ['status' => 200 , 'message' => '删除成功'];
            return json_encode($message);
        }
        $message = ['status' => 500 , 'message' => '发生未知错误'];
        return json_encode($message);
    }
}