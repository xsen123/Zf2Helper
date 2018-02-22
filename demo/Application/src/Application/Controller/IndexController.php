<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;

class IndexController extends NonAuthBaseController
{

	protected function beforeDispatch(MvcEvent $e)
	{
		echo 'IP=' . $this->getClientIP();
		//$this->redirect()->toUrl("http://www.baidu.com");
		return false;
	}
	
	protected function afterDispatch(MvcEvent $e)
	{
		// 统一执行某个动作，例如记录日志等。成功访问该Controller的任何一个Action后，都将执行该方法
	}
	
    public function indexAction()
    {
    	echo 'indexAction';
    	$view = new ViewModel();
        return $view;
    }
    
}
