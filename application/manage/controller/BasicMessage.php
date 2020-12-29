<?php
/**
 * Created by PhpStorm.
 * User: 钟焯权
 * Date: 2020/12/11
 * Time: 9:52
 */

namespace app\manage\controller;
use app\common\model\Devices as DeviceMode;
use app\common\model\TestRecord;
use app\common\model\User as UserMode;
use think\Controller;
use think\Db;
use app\common\model\TestRecord as TestRecordMode;
use app\common\model\TrainRecord as TrainRecordMode;
class BasicMessage extends Controller
{
    //获取系统磁盘容量
    public function get_disk_message()
    {
        $sh = shell_exec('df -lh | grep -E "^(/)"');
        $sh = preg_replace("/\s{2,}/", ' ', $sh);
        $hd = explode(" ", $sh);
        $hd_usage = number_format(trim($hd[4],'%')/100,2);  //已使用 ex:0.51
        $free_space = 1 - $hd_usage; //剩余空间
        if($hd_usage && $free_space)
        {
            $message = ['status'=>200 , 'message' =>[ 'free_space' => $free_space ,'hd_usage' =>$hd_usage,'total_space' => trim($hd[1],'G')]];
            return json_encode($message);
        }
        else
        {
            $message = ['status'=>300 , 'message' => '发生未知错误' ];
            return json_encode($message);
        }
    }
    public function get_basic_message_api()
    {
        $devices_count = DeviceMode::count('id');
        $user_count = UserMode::count('id');
        $record_count = TestRecordMode::count('id')+ TrainRecordMode::count('id');
        if( isset($devices_count) && isset($user_count)  && isset($record_count) )
        {
            $message = ['status'=>200 , 'message' =>[ 'devices_count' => $devices_count ,'user_count' =>$user_count ,'record_count' =>$record_count]];
            return json_encode($message);
        }
        $message = ['status'=>300 , 'message' => '发生未知错误' ];
        return json_encode($message);
    }
    //获取设备用户数量
    public function get_user_devices_count_api()
    {
        $devices_name = [];
        $user_count =[];
        $res = Db::table('devices')
            ->alias('d')
            ->leftJoin('user u','d.id = u.devices_id')
            ->field('d.name ,COUNT(u.devices_id) as devices_user_count')
            ->group('d.id')
            ->order('d.name asc')
            ->select();
        if(!isset($res))
        {
            $message = ['status'=>400 , 'message' => '发生未知错误' ];
            return json_encode($message);
        }
        foreach ($res as $key=>$r){
            $devices_name[$key] = $r['name'];
            $user_count[$key] = $r['devices_user_count'];
        }
            foreach ($res as $key=>$r){
                $devices_name[$key] = $r['name'];
                $user_count[$key] = $r['devices_user_count'];
            }
        if(isset($devices_name)&&isset($user_count))
        {
            $message = ['status'=>200 , 'message' =>[ 'devices_name' => $devices_name ,'user_count' =>$user_count ]];
            return json_encode($message);
        }
        $message = ['status'=>401 , 'message' => '数据错误' ];
        return json_encode($message);
    }
    public function get_devices_message()
    {
        $res = DeviceMode::where('available_count<10')->select();
        if($res)
        {
            $message = ['status' => 200, 'message' => $res];
            return json_encode($message);
        }
        else
        {
            $message = ['status'=>300 , 'message' => '发生未知错误' ];
            return json_encode($message);
        }
    }

}