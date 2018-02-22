<?php

namespace Application\Controller;

use Application\Controller\BaseController;
use Zend\Mvc\MvcEvent;

/**
 * 需要执行权限检查的控制器基类：可以根据实际情况细分为不同的权限检查控制器基类，例如手机端用户鉴权、后台管理员鉴权等
 * @author Jason
 *
 */
class AuthBaseController extends BaseController {
	
	protected function beforeDispatch(MvcEvent $e){
		parent::beforeDispatch($e);
		
		$ignoreAtuhAcitons = $this->_provideIgnoreAuthMethods();
		$method = $this->getCurRequestActionMethod(); // 当前请求对应的Action方法
		// 当前请求的方法在忽略鉴权的列表中，则不需要鉴权，直接返回true
		if(is_array($ignoreAtuhAcitons) && in_array($method, $ignoreAtuhAcitons))
		{
			return true;
		}
		else
		{
			// 获取请求参数值，然后传递给checkPermission方法
			$token = $this->getParamValue('token');
			$checkResult = $this->checkPermission($token);
			if($checkResult==false)
			{
				$this->redirect()->toUrl('/'); // 如果需要执行URL跳转，最后需要返回false，否则会在跳转前仍然执行一次当前请求的Action
			}
			return $checkResult;
		}
	}
	
	/**
	 * 返回当前控制器类中不需要鉴权的Action方法名称列表
	 * @return array
	 */
	protected function _provideIgnoreAuthMethods()
	{
		return array();
	}
	
	protected function checkPermission($token)
	{
		// TODO: 根据参数执行权限检查
		return $token == 'test';
	}
	
}
