<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ Ӧ������ļ� ]
namespace think;
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
header("Access-Control-Allow-Headers:DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");

//��������css��js�ļ����õĳ���
define('SITE_URL','http://106.52.43.100/pd');
define('BIND_MODULE','api'); 
// ���ػ����ļ�
require __DIR__ . '/../thinkphp/base.php';

// ֧������ʹ�þ�̬��������Request�����Config����
// ִ��Ӧ�ò���Ӧ
Container::get('app')->run()->send();
