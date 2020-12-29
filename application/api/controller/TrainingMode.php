<?php
/**
 * Created by PhpStorm.
 * User: 钟焯权
 * Date: 2020/12/1
 * Time: 11:36
 */

namespace app\api\controller;
use think\facade\Request;
use think\Controller;
use app\common\model\Devices as DevicesMode;
use app\common\model\User as UserMode;
use app\common\model\Train as TrainMode;
use app\common\model\TrainRecord as TrainRecordMode;
use think\Db;
class TrainingMode extends Controller
{
    public function trainingMode_record_save_api()
    {
    $data = Request::param();
	$data = str_replace('\\&quot;','"',$data);
	$data = json_decode($data['data'],true);
	$user_name = $data['name'];
	$devices_name = $data['devicesId'];
	$standardArray = $data['standardArray'];
	$testArray = $data['testArray'];
	$score = $data['score'];
	$train_name = $data['trainName'];
	$round = count($testArray);

	$user_id = UserMode::where('name' , $user_name)
        ->where('devices_id', DevicesMode::where('name' , $devices_name)->value('id'))->value('id');
	$devices_id = DevicesMode::where('name' , $devices_name)->value('id');
	$train_id = TrainMode::where('category' , $train_name)->value('id');
    $res = TrainRecordMode::create(['user_id' => $user_id , 'devices_id' => $devices_id , 'train_id' =>$train_id , 'standard_data' => json_encode($standardArray) , 'test_data' => json_encode($testArray) , 'score' => json_encode($score) ,
        'round' => $round , 'create_time' => date('Y-m-d H:i:s')]);

    if($res)
    {
        $message = ['status'=>200,'message'=>'训练完成','id' => $res['id']];
        return json_encode($message);
    }
        $message = ['status'=>500,'message'=>'发生未知错误'];
        return json_encode($message);
    }
    //获取1条训练记录
    public function trainingMode_record_get_one_api()
    {
        $data = Request::param();
        $res = TrainRecordMode::where('id',$data['train_id'])->find();
        $res['score'] = json_decode($res['score']);
        $res['test_data'] = json_decode($res['test_data']);
        $res['standard_data'] = json_decode($res['standard_data']);
        if($res)
        {
            $train_name = TrainMode::where('id',$res['train_id'])->value('desc');

            unset($res['train_id']);
            $res['train_name'] = $train_name;
            $message = ['status'=>200,'message'=>'','data' => $res];
            return json_encode($message);
        }
        $message = ['status'=>500,'message'=>'发生未知错误'];
        return json_encode($message);
    }
    public function training_record_get_api()
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
        $res = Db::table('train_record')
            ->alias('tr')
            ->leftJoin('train t' ,'t.id = tr.train_id')
            ->where('tr.devices_id' , DevicesMode::where('name',$data['devices_id'])->value('id'))
            ->where('tr.user_id',$user['id'])
            ->whereIn('train_id' ,$data['train_id'])
            ->field('tr.id , tr.create_time , t.desc , tr.round')
            ->select();

        if(isset($res))
        {
            $message = ['status' => 200 , 'message' => $res ];
            return json_encode($message);
        }
        $message = ['status' => 500 , 'message' => '发生未知错误'];
        return json_encode($message);
    }
    public function training_record_delete_api(){
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

        $res = TrainRecordMode::where('devices_id',DevicesMode::where('name',$data['devices_id'])->value('id'))
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

    public function basicTestOfSlowMuscle_long_record_get_api()
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
        if($data['train_id'])
        {
            $res = TrainRecordMode::where('devices_id', DevicesMode::where('name', $data['devices_id'])->value('id'))
                ->where('user_id', $user['id'])
                ->where('train_id', $data['train_id'])
                ->field('create_time , score , round')
                ->order('create_time asc')
                ->select();
        }
        if($data['train_id'] != null && $data['create_time'] != null)
        {
            $res = TrainRecordMode::where('devices_id', DevicesMode::where('name', $data['devices_id'])->value('id'))
                ->where('user_id', $user['id'])
                ->where('train_id', $data['train_id'])
                ->where('create_time', 'between', $data['create_time'])
                ->field('create_time , score , round')
                ->order('create_time asc')
                ->select();
        }
        if($res)
        {
            foreach ($res as $key => $item)
            {
            $item['score'] = json_decode($item['score']);
            }
            $message = ['status'=>200,'message'=>'','data' => $res];
            return json_encode($message);
        }
        $message = ['status'=>500,'message'=>'发生未知错误'];
        return json_encode($message);
    }
}