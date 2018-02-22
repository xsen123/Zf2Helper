<?php

namespace Application\Controller;

use Zf2Helper\Controller\AbstractBaseController;
use Zend\Mvc\MvcEvent;

/*
 * 该模块下其他Controller的父类文件
 * 	1.可在此类的onDispatch方法中执行一些需要统一执行的动作
 * 	2.可在此类的基础上，继承出一个统一控制权限的Controller，供其他需要鉴权的业务Controller继承使用
 */
abstract class BaseController extends AbstractBaseController {

	protected function beforeDispatch(MvcEvent $e){
		
		// TODO: 添加在执行Action前需要统一处理的动作
		
		return true;
	}
	
	// 获取HTTP提交过来的参数值，包括GET和POST。如果不存在这样的参数，则返回null
	public function __get($key)
	{
		if (preg_match('/^__([0-9a-z_]+)/i', $key, $regs))
		{
			$key = $regs[1];
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$raw = $_POST;
			}
			else
			{
				$raw = $_GET;
			}
			if (isset($raw[$key]))
			{
				return $raw[$key];
			}
		}
		return null;
	}

}
