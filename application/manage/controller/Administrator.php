<?php
/**
 * Created by PhpStorm.
 * User: 钟焯权
 * Date: 2020/12/14
 * Time: 15:11
 */

namespace app\manage\controller;
use think\facade\Request;
use think\Db;
use think\Controller;
use think\facade\Log;
class Administrator extends Controller
{
    public function administrators_message()
    {
        dump(11);
        $res = $this->connect_database()->name('administrators')->where('show_status',1)->find();
        if($res){
            $message = ['status'=>200 , 'message' => $res];
            return json_encode($message);
        }
        $message = ['status'=>400 , 'message' => "发生未知错误"];
        return json_encode($message);
    }
    public function connect_database()
    {
        $connect = Db::connect([
            // 数据库类型
            'type'        => 'mysql',
            // 数据库连接DSN配置
            'dsn'         => '',
            // 服务器地址
            'hostname'    => '127.0.0.1',
            // 数据库名
            'database'    => 'manage',
            // 数据库用户名
            'username'    => 'root',
            // 数据库密码
            'password'    => 'Aa@123456',
            // 数据库连接端口
            'hostport'    => '',
            // 数据库连接参数
            'params'      => [],
            // 数据库编码默认采用utf8
            'charset'     => 'utf8',
            // 数据库表前缀
            'prefix'      => '',
        ]);
        return $connect;
    }
    //管理员注册
    public function administrator_register()
    {
        $data = Request::param();
        if($data) {
            $data = str_replace('\\&quot;', '"', $data);
            $data = json_decode($data['data'], true);

            $res = $this->connect_database()->name('administrators')->where('name',$data['name'])->find();
            if($data['name'] == null)
            {
                $message = ['status' => 401 , 'message'=>'用户名不能为空'];
                return json_encode($message);
            }

            if($data['password'] == null)
            {
                $message = ['status' => 402 , 'message'=>'密码不能为空'];
                return json_encode($message);
            }
            if($data['phone'] == null)
            {
                $message = ['status' => 403 , 'message'=>'手机号不能为空'];
                return json_encode($message);
            }
            if($data['rpassword'] == null)
            {
                $message = ['status' => 404 , 'message'=>'重输密码不能为空'];
                return json_encode($message);
            }
            if($data['password'] != $data['rpassword'])
            {
                $message = ['status' => 405 , 'message'=>'两次密码不一致'];
                return json_encode($message);
            }
            if($res != null)
            {
                $message = ['status' => 408 , 'message'=>'用户名已存在'];
                return json_encode($message);
            }
            unset($data['rpassword']);
            $data['create_time'] = date('Y-m-d H:i:s');
            Log::write($data);
            $res2 = $this->connect_database()->name('administrators')->insert($data);
            if($res2)
            {
                $message = ['status' => 200 , 'message'=>'注册成功'];
                return json_encode($message);
            }
            $message = ['status' => 500 , 'message'=>'发生未知错误'];
            return($message);
        }
        else
        {
            $message = ['status'=>300 , 'message' =>'参数发生未知错误'];
            return json_encode($message);
        }
    }

    //管理员登录
    public function administrator_login()
    {
        $data = Request::param();
        if($data) {
            $data = str_replace('\\&quot;', '"', $data);
            $data = json_decode($data['data'], true);
            $res = $this->connect_database()->name('administrators')->where('name',$data['username'])->where('password',$data['password'])->find();
            if($res)
            {
                $message = ['status'=>200 , 'message' => $res];
                $this->connect_database()->name('administrators')->where('id',$res['id'])->update(['login_time'=>date('Y-m-d H:i:s')]);
                return json_encode($message);
            }
            else
            {
                $message = ['status'=>400 , 'message' => "账号或者密码错误"];
                return json_encode($message);
            }

        }
        else
        {
            $message = ['status'=>300 , 'message' =>'参数发生未知错误'];
            return json_encode($message);
        }
    }
}