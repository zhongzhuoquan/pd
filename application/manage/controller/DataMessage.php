<?php
/**
 * Created by PhpStorm.
 * User: 钟焯权
 * Date: 2020/12/14
 * Time: 16:30
 */

namespace app\manage\controller;

use app\common\model\TestRecord;
use think\Controller;
use think\Db;
use think\facade\Request;
use app\common\model\TestRecord as TestRecordMode;
use app\common\model\TrainRecord as TrainRecordMode;
use think\facade\Log;
class DataMessage extends Controller
{
    //获取测试数据量
    public function get_message_count_message()
    {
        $res_test_day = TestRecordMode::where('create_time','between time',[date('Y-m-d 0:0:0'),date('Y-m-d 23:59:59')])->count('id');
        $res_test_week = TestRecordMode::where('create_time','between time',[date('Y-m-d', strtotime('-7 day')),date('Y-m-d')])->count('id');
        $res_test_money = TestRecordMode::where('create_time','between time',[date('Y-m-d', strtotime('-1 money')),date('Y-m-d')])->count('id');
        $res_test = [$res_test_day,$res_test_week,$res_test_money];
        $res_train_day = TrainRecordMode::where('create_time','between time',[date('Y-m-d 0:0:0'),date('Y-m-d 23:59:59')])->count('id');
        $res_train_week = TrainRecordMode::where('create_time','between time',[date('Y-m-d', strtotime('-7 day')),date('Y-m-d')])->count('id');
        $res_train_money = TrainRecordMode::where('create_time','between time',[date('Y-m-d', strtotime('-1 money')),date('Y-m-d')])->count('id');
        $res_train = [$res_train_day,$res_train_week,$res_train_money];
        if($res_test &&  $res_train)
        {
            $message = ['status'=>200 , 'message' =>['res_test' => $res_test ,'res_train' => $res_train]];
            return json_encode($message);
        }
        $message = ['status'=>300 , 'message' =>'发生未知错误'];
        return json_encode($message);
    }
    //搜索训练记录
    public function get_record_message()
    {
        $data = Request::param();
        if($data)
        {
            $where = array();
            $data = str_replace('\\&quot;','"',$data);
            $data = json_decode($data['data'],true);
            log::write($data);
            if($data['option_value'] == 0)
            {

                if($data['user_name'] != null)
                {
                    $where[] = ['u.name','like','%'.$data['user_name'].'%'];
                }
                if($data['devices_name'] != null)
                {

                    $where[] = ['d.name','like','%'.$data['devices_name'].'%'];
                }
                if($data['time_value'] != null)
                {
                    $where[] = ['t.create_time','between',$data['time_value']];
                }
                $res = Db::table('test_record')
                    ->alias('t')
                    ->leftJoin('user u','u.id = t.user_id')
                    ->leftJoin('devices d' ,'d.id = t.devices_id')
                    ->where($where)
                    ->field('t.id ,u.name as username , d.name as devicesname,t.create_time ,t.total_time as record')
                    ->select();
                $message = ['status'=>200 , 'message' => $res];
                return json_encode($message);
            }
            else
            {

                if($data['user_name'] != null)
                {
                    $where[] = ['u.name','like','%'.$data['user_name'].'%'];
                }
                if($data['devices_name'] != null)
                {

                    $where[] = ['d.name','like','%'.$data['devices_name'].'%'];
                }
                if($data['time_value'] != null)
                {
                    $where[] = ['t.create_time','between',$data['time_value']];
                }
                $res = Db::table('train_record')
                    ->alias('t')
                    ->leftJoin('user u','u.id = t.user_id')
                    ->leftJoin('devices d' ,'d.id = t.devices_id')
                    ->leftJoin('train tn','tn.id = t.train_id')
                    ->where($where)
                    ->field('t.id , u.name as username , d.name devicesname , t.create_time , t.score as record ,tn.desc')
                    ->select();
                foreach ($res as  $key => $r)
                {
                    $res[$key]['record'] = json_decode($r['record']);
                }
                $message = ['status'=>200 , 'message' => $res];
                return json_encode($message);
            }
        }
        else
        {
            $message = ['status'=>300 , 'message' =>'参数发生未知错误'];
            return json_encode($message);
        }
    }
    //删除1个记录
    public function delete_record_api()
    {
        $data = Request::param();
        if($data != null) {
            $data = str_replace('\\&quot;','"',$data);
            $data = json_decode($data['data'],true);
            if($data['soft'] == 0)
            {
                $res = TestRecord::where('id', $data['id'])->delete();
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
                $res = TrainRecordMode::where('id', $data['id'])->delete();
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

        }
        else
        {
            $message = ['status'=>300 , 'message' =>'参数错误'];
            return json_encode($message);
        }
    }
    //删除多个记录
    public function delete_record_all_api()
    {
        $data = Request::param();
        if($data != null) {
            $data = str_replace('\\&quot;','"',$data);
            $data = json_decode($data['data'],true);
            if($data['soft'] == 0)
            {
                $res = TestRecord::where('id', 'in' , $data['id'])->delete();
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
                $res = TrainRecordMode::where('id', 'in' , $data['id'])->delete();
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
        }
        else
        {
            $message = ['status'=>300 , 'message' =>'参数错误'];
            return json_encode($message);
        }
    }
}