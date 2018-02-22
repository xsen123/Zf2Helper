<?php

namespace Application\Controller;

use Application\Controller\BaseController;
use Zend\Mvc\MvcEvent;

/**
 * 不需要执行权限检查的控制器基类
 * @author Jason
 *
 */
class NonAuthBaseController extends BaseController {
	
	protected function beforeDispatch(MvcEvent $e){
		parent::beforeDispatch($e);
	
		// TODO: 添加在执行Action前需要统一处理的动作
	
		return true;
	}
	
}
