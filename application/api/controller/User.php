<?php
/**
 * Created by PhpStorm.
 * User: 钟焯权
 * Date: 2020/11/27
 * Time: 10:48
 */
namespace app\api\controller;
use think\Controller;
use app\common\model\User as UserMode;
use app\common\model\Devices as DevicesMode;
use app\common\model\TestRecord as TestRecordMode;
use app\common\model\TrainRecord as TrainRecordMode;
use think\facade\Log;
use think\facade\Request;
use think\Db;

class User extends Controller
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
        $res = Db::table('user')
            ->alias('u')
            ->leftJoin('devices d','u.devices_id = d.id')
            ->where('u.name',$data['name'])
            ->where('u.password',$data['password'])
            ->where('d.name',$data['devices_id'])
            ->field('u.id as uid,u.status')
            ->find();
        $devices = DevicesMode::where('name',$data['devices_id'])->find();
        if($res != null )
        {
            if($res['status'] == 1)
            {
                UserMode::where('id', $res['uid'])->update(['login_time' => date('Y-m-d H:i:s')]);
                $message = ['status' => 200, 'message' => '登录成功', 'role_status' => $res['status']];
                return json_encode($message);
            }else {
                if ($devices['available_count'] > 0) {
                    DevicesMode::where('name', $data['devices_id'])->update(['available_count' => $devices['available_count'] - 1]);
                    UserMode::where('id', $res['uid'])->update(['login_time' => date('Y-m-d H:i:s')]);
                    $message = ['status' => 200, 'message' => '登录成功', 'available_count' => $devices['available_count'] - 1,'role_status' => $res['status']];
                    return json_encode($message);
                } else {
                    $message = ['status' => 401, 'message' => '可使用次数为0'];
                    return json_encode($message);
                }
            }
        }
        else
        {

            $message=['status' => 402 , 'message' => '用户名或者密码错误'];
            return json_encode($message);
        }
    }
	//用户注册
    public function user_register_api()
    {
        $data = Request::param();
        $res = Db::table('user')
            ->alias('u')
            ->leftJoin('devices d','u.devices_id = d.id')
            ->where('u.name',$data['name'])
            ->where('d.name',$data['devices_id'])
            ->select();
        if($data['name'] == null)
        {
            $message = ['status' => 401 , 'message'=>'用户名不能为空'];
            return json_encode($message);
        }
        if($data['sex'] == null)
        {
            $message = ['status' => 402 , 'message'=>'性别不能为空'];
            return json_encode($message);
        }
        if($data['year'] == null)
        {
            $message = ['status' => 403 , 'message'=>'出生年不能为空'];
            return json_encode($message);
        }
        if($data['password'] == null)
        {
            $message = ['status' => 405 , 'message'=>'密码不能为空'];
            return json_encode($message);
        }
        if($data['phone'] == null)
        {
            $message = ['status' => 409 , 'message'=>'手机号不能为空'];
            return json_encode($message);
        }
        if($data['rpassword'] == null)
        {
            $message = ['status' => 406 , 'message'=>'重输密码不能为空'];
            return json_encode($message);
        }
        if($data['password'] != $data['rpassword'])
        {
            $message = ['status' => 407 , 'message'=>'两次密码不一致'];
            return json_encode($message);
        }
        if($res != null)
        {
            $message = ['status' => 408 , 'message'=>'用户名已存在'];
            return json_encode($message);
        }

	$data['devices_id'] = DevicesMode::where('name',$data['devices_id'])->value('id');
        unset($data['rpassword']);
	$data['create_time'] = date('Y-m-d H:i:s');
        if(UserMode::create($data))
        {
            $message = ['status' => 200 , 'message'=>'注册成功'];
            return json_encode($message);
        }
        $message = ['status' => 500 , 'message'=>'发生未知错误'];
        return($message);
    }
  public function user_information_api()
    {
        $data = Request::param();
        $res = Db::table('user')
            ->alias('u')
            ->leftJoin('devices d','d.id = u.devices_id')
            ->where('d.name',$data['devices_id'])
            ->where('u.name',$data['name'])
	    ->field('u.id id,u.name name,u.year year,u.sex sex,u.password password,u.phone phone')
            ->select();
	if($res)
	{
	$message = ['status'=>200,'message'=>$res];
        return json_encode($message);
	}
	else
	{
	$message = ['status'=>400,'message'=>'没数据'];
     	return json_encode($message);
	}
     }
    public function user_information_change_api()
    {
        $data = Request::param();
        if($data['name'] == null)
        {
            $message = ['status' => 401 , 'message'=>'用户名不能为空'];
            return json_encode($message);
        }
        if($data['sex'] == null)
        {
            $message = ['status' => 402 , 'message'=>'性别不能为空'];
            return json_encode($message);
        }
        if($data['year'] == null)
        {
            $message = ['status' => 403 , 'message'=>'出生年不能为空'];
            return json_encode($message);
        }
        if($data['password'] == null)
        {
            $message = ['status' => 405 , 'message'=>'密码不能为空'];
            return json_encode($message);
        }
        if($data['phone'] == null)
        {
            $message = ['status' => 406 , 'message'=>'电话号码不能为空'];
            return json_encode($message);
        }
        $res2 = Db::table('user')
            ->alias('u')
            ->leftJoin('devices d','u.devices_id = d.id')
            ->where('u.name',$data['name'])
            ->where('d.name',$data['devices_id'])
            ->update(['u.name'=>$data['name'] , 'u.sex' =>$data['sex'] , 'u.year' => $data['year'] , 'u.password' =>$data['password'],'u.phone' =>$data['phone']]);
        if($res2)
        {
            $message = ['status' =>200, 'message' => '修改成功'];
            return json_encode($message);
        }
        $message = ['status' =>500, 'message' => '发生未知错误'];
        return json_encode($message);
    }

    //修改管理员信息
    public function admin_information_change_api()
    {
        $data = Request::param();
        if($data['name'] == null)
        {
            $message = ['status' => 401 , 'message'=>'用户名不能为空'];
            return json_encode($message);
        }
        if($data['password'] == null)
        {
            $message = ['status' => 405 , 'message'=>'密码不能为空'];
            return json_encode($message);
        }
        $res2 = Db::table('user')
            ->alias('u')
            ->leftJoin('devices d','u.devices_id = d.id')
            ->where('u.name',$data['name'])
            ->where('d.name',$data['devices_id'])
            ->update(['u.name'=>$data['name'] , 'u.password' =>$data['password']]);
        if($res2)
        {
            $message = ['status' =>200, 'message' => '修改成功'];
            return json_encode($message);
        }
        $message = ['status' =>500, 'message' => '发生未知错误'];
        return json_encode($message);
    }
    //管理员获取用户信息
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
                ->where('u.status != 1')
                ->where('d.name', '=', $data['devices_name'] )
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
    //管理员删除一个用户
    public function delete_user_api()
    {
        $data = Request::param();
        if($data['id'] !=null) {
            $res = UserMode::where('id', $data['id'])->delete();
            TestRecordMode::where('user_id',$data['id'])->delete();
            TrainRecordMode::where('user_id',$data['id'])->delete();
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
    //管理员查看用户训练记录
    public function get_record_message()
    {
        $data = Request::param();
        if($data)
        {
            $where = array();
            $data = str_replace('\\&quot;','"',$data);
            $data = json_decode($data['data'],true);
            if($data['user_name'] != null)
            {
                $where[] = ['u.name','like','%'.$data['user_name'].'%'];
            }
            if($data['devices_name'] != null)
            {

                $where[] = ['d.name','=',$data['devices_name']];
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
                ->field('t.id , u.name as username  , t.create_time , t.score as record ,tn.desc')
                ->select();
            foreach ($res as  $key => $r)
            {
                $res[$key]['record'] = json_decode($r['record']);
            }
            $message = ['status'=>200 , 'message' => $res];
            return json_encode($message);
        }

        else
        {
            $message = ['status'=>300 , 'message' =>'参数发生未知错误'];
            return json_encode($message);
        }
    }
}