<?php

namespace Application\Controller;

use Zend\View\Model\ViewModel;

/**
 * NewsController
 *
 * @author
 *
 * @version
 *
 */
class NewsController extends AuthBaseController {
	
	protected function _provideMainServiceName()
	{
		return 'Application\Service\NewsService';
	}
	
	protected function _provideIgnoreAuthMethods()
	{
		// 指定indexAction不需要鉴权。可以指定多个Action方法不需要鉴权，如：array('indexAction', 'listAction')
		return array('indexAction');
	}

	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//$this->setSessionValue('test', 'cde');
		
		$view = new ViewModel();
		// $view->setTemplate('application/news/add.phtml'); // 可以手动指定其他视图文件
		return $view;
	}
	
	public function listAction(){
		
		// echo '测试：获取indexAction中给SESSION节点保存的值：' . $this->getSessionValue('test');
		
		$service = $this->getMainService();
		
		// 测试多数据库连接的使用：如果参数中指定使用第2个数据，则重新配置数据库信息
		if($this->getParamValue('db')=='2')
		{
			// 可重新指定数据库配置信息
			$dbSettings = $this->getConfigValue('db2');
			//var_dump($dbSettings);
			$service->prepareTableGateway($dbSettings);
		}
		
		// service用法示例
		//$paginator = $service->fetchAll('id desc'); // 获取所有记录
		$paginator = $service->fetch('id>1 and id<=6', 'title', 2, 1); // 获取符合条件的所有记录
		//$paginator = array($service->fetchById('6'));
		//$paginator = $service->fetch(array('title'=>'Fifth news')); // 获取符合条件的所有记录
		//$paginator = $service->fetch(array('title="Fifth news"')); // 获取符合条件的所有记录，不推荐传入这种格式的sql

		// 方式1: 创建ViewModel对象时，将数组作为参数传入，然后返回该view对象
		//$view = new ViewModel(array('paginator'=>$paginator));
		
		// 方式2: 创建ViewModel对象，然后调用setVariable为view对象设置数组
		//$view = new ViewModel();
		//$view->setVariable('paginator', $paginator);
		//return $view;
		
		// 方式3: 直接返回数组/ResultSet对象
		return array('paginator'=>$paginator);
	}
	
	public function addAction(){
		//echo "NewsController addAction";
		//exit;
	}
	
	public function editAction(){
		$id = $this->params()->fromRoute('id',0);
		$news = $this->getMainService()->fetchById($id);
		return array('news'=>$news);
	}
	
	public function deleteAction(){
		$id = $this->params()->fromRoute('id',0);
 		if($id==0){
 			$this->redirect()->toUrl('/news/list');
 		}
		$request = $this->getRequest();
		if($request->isPost()){
			$del = $request->getPost('del','No');
			if($del=='Yes'){
				$this->getMainService()->deleteById($id);
			}
			$this->redirect()->toUrl('/news/list');
		}
		else{
			return array('id'=>$id,'news'=>$this->getMainService()->fetchById($id));
		}
	}

}