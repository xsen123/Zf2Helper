<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                	'Zf2Helper' => __DIR__ . '/../../vendor/Zf2Helper/library/Zf2Helper',
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig(){
    	return array(
   			'factories'=>array(

   				// 定义具体的业务Service类
   				'Application\Service\NewsService'=>function($sm){
   					$service = new \Application\Service\NewsService();
   					$dbSettings = $this->_getDefaultDbSettings($sm);
   					// \Zend\Debug\Debug::dump($dbSettings); // 调试输出
   					$service->prepareTableGateway($dbSettings);
   					return $service;
   				},

   			),
    	);
    }

    // 获取默认的数据库配置信息，方便Service统一使用
    protected function _getDefaultDbSettings(\Zend\ServiceManager\ServiceManager $sm)
    {
    	$configArray = $sm->get('Config');
    	return $configArray['db'];
    }

}
