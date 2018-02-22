<?php

namespace Zf2Helper\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zf2Helper\Service\IServiceInterface;

/**
 * 基础控制器类，所有业务控制器均继承自该类
 *
 * @author Jason
 * @date: 2015/4/6
*/
abstract class AbstractBaseController extends AbstractActionController {
	
	/**
	 * 
	 * @var IServiceInterface
	 */
	private $_mainService;
	
	/**
	 * 当前请求的Action对应的方法名称
	 * @var string
	 */
	private $_curRequestActionMethod;
	
	// 提供当前控制器对应的主Model的名称，默认不提供
	protected function _provideMainServiceName()
	{
		return null;
	}

	// 重写分发请求的方法，并将该方法设置为final，不允许子类再重写该方法
	final public function onDispatch(MvcEvent $e)
	{
		if(isset($_SESSION)==false)
		{
			session_start();
		}
		
		// TODO: 后续根据需要可在此写一些统一实现的功能
		// ...
		
		// 特殊：特意先判断当前请求的Action是否存在，如果不存在则直接调用parent::onDispatch($e)，该方法中会判断Action是否存在，并进行页面不存在的提示
		// 如果不先判断，将会导致请求不存在的Action也会执行beforeDispatch方法（可能含有业务逻辑和数据库操作）
		$this->initCurRequestActionMethod($e);
		if($this->actionExists($this->_curRequestActionMethod))
		{
			if($this->beforeDispatch($e))
			{
				$actionResponse = parent::onDispatch($e);
				$this->afterDispatch($e);
				return $actionResponse;
			}
			else
			{
				// 如果beforeDispatch返回false，则强制返回空白页面
 				$response = $this->getResponse();
				$response->setContent("");
				return $response;
			}
		}
		else
		{
			return parent::onDispatch($e);
		}
	}
	
	/**
	 * 调用请求的Action前需要执行的动作
	 * 
	 * @param MvcEvent $e
	 * @return boolean 返回true则继续执行当前请求的Action和afterDispatch，否则不再执行
	 * 			重要：如果需要执行URL跳转【例如$this->redirect()->toUrl()】，则需要在该方法中返回false，否则导致原有Action和afterDispatch仍然会执行一遍，然后再跳转 
	 */
	protected  function beforeDispatch(MvcEvent $e)
	{
		// 默认什么也不做，直接返回true，各子类可重写该方法
		return true;
	}
	

	/**
	 * 调用请求的Action后需要执行的动作，如果在beforeDispatch或Action中调用了exit或die，则不会再执行该方法
	 * 注意在该方法中执行echo内容会显示在网页的最前面，而不是最后面！
	 * 
	 * @param MvcEvent $e
	 */
	protected  function afterDispatch(MvcEvent $e)
	{
		// 默认什么也不做，各子类可重写该方法
	}
	
	/**
	 * 获取HTTP提交过来的参数值，包括GET和POST。如果不存在这样的参数，则返回null
	 * @param string $paramName
	 * @param string $default
	 * @return string||NULL
	 */
	protected function getParamValue($paramName, $default=null)
	{
		if (isset($_REQUEST[$paramName]))
		{
			return $_REQUEST[$paramName];
		}
		else
		{
			return $default;
		}
	}
	
	/**
	 * 在session节点下存值
	 * @param mixed $key
	 * @param mixed $value
	 */
    protected function setSessionValue($key, $value)
    {
    	$_SESSION[$key] = $value;
    }
    
    /**
     * 获取session节点下存储的值
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    protected function getSessionValue($key, $default=null)
    {
    	return (isset($_SESSION[$key]) ? $_SESSION[$key] : $default);    
    }

	/**
     * 删除某一个session的值
	 *
	 * @param $key
	 */
	protected function unsetSession($key){
		if(isset($_SESSION[$key])){
			unset($_SESSION[$key]);
		}
	}

	/**
	 * 获取控制器对应的主Model的service对象，这些service对象需要在Module.php的getServiceConfig()方法中初始化
	 * 
	 * @return IServiceInterface
	 */
	protected function getMainService()
	{
		if(empty($this->_mainService))
		{
			$mainServiceName = $this->_provideMainServiceName();
			if(empty($mainServiceName))
			{
				$this->_mainService = null;
				return null;
			}
			else
			{
				$this->_mainService = $this->getServiceLocator()->get($mainServiceName);
				return $this->_mainService;
			}
		}
		else
		{
			return $this->_mainService;
		}
	}
	
	/**
	 * 根据名称获取service对象，这些service对象需要在Module.php的getServiceConfig()方法中初始化
	 * 
	 * @param string $serviceName
         * @return IServiceInterface
	 */
	protected function getServiceByName($serviceName)
	{
		return $this->getServiceLocator()->get($serviceName);
	}
	
	/**
	 * 获取配置文件中的配置项
	 * 	包括 module.config.php、global.php、local.php和application.config.php 等文件中的配置项
	 * @param mixed $configKey
	 * @param string $default
	 * @return string|null
	 */
	protected function getConfigValue($configKey, $default=null)
	{
		if(empty($configKey)) { return $default; }

		$sm = $this->getServiceLocator();
		$configArray = $sm->get('Config');
		if(isset($configArray[$configKey])==false) // 如果Config中没有该节点，则从ApplicationConfig中查找
		{
			$configArray = $sm->get('ApplicationConfig');
		}
		
		if(isset($configArray[$configKey]))
		{
			return $configArray[$configKey]; 
		}
		
		return $default;
	}
	
	/**
	 * 初始化当前请求的Action对应的方法名称变量
	 * @return string|NULL
	 */
	private function initCurRequestActionMethod(MvcEvent $e)
	{
		if(empty($this->_curRequestActionMethod))
		{
			$routeMatch = $e->getRouteMatch();
			if ($routeMatch)
			{
				$this->_curRequestActionMethod = static::getMethodFromAction($routeMatch->getParam('action', null));
			}
			else
			{
				return null;
			}
		}
	}
	
	/**
	 * 获取当前请求的Action对应的方法名称
	 * @return string
	 */
	protected function getCurRequestActionMethod()
	{
		return $this->_curRequestActionMethod;
	}
	
	/**
	 * 判断当前请求的Action是否存在
	 * @param MvcEvent $e
	 * @return boolean
	 */
	protected function actionExists($method)
	{
		if(empty($method))
		{
			return false;
		}
		else {
			return method_exists($this, $method);
		}
	}
	
	/**
	 * 获取发请求的客户端IP地址
	 * @return string|NULL
	 */
	protected function getClientIP()
	{
		$client_ip = "";
		if(empty($_SERVER["HTTP_CLIENT_IP"])==false && 'unknown'!=strtolower($_SERVER["HTTP_CLIENT_IP"]))
		{
			$client_ip = $_SERVER["HTTP_CLIENT_IP"];
		}
		else if(empty($_SERVER["HTTP_X_FORWARDED_FOR"])==false && 'unknown'!=strtolower($_SERVER["HTTP_X_FORWARDED_FOR"]))
		{
			$client_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		else if(empty($_SERVER["REMOTE_ADDR"])==false && 'unknown'!=strtolower($_SERVER["REMOTE_ADDR"]))
		{
			$client_ip = $_SERVER["REMOTE_ADDR"];
		}
		return $client_ip;
	}

}
