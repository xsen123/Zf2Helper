<?php

namespace Application\Dao;

use Zf2Helper\Dao\AbstractBaseDao;

class NewsDao extends AbstractBaseDao{
	
	protected function _provideObjectPrototype() {
		// 注意：这里需要返回一个Model对象，不是字符串
		return new \Application\Model\News();
	}
	
	protected function _provideTableName() {
		return 'news';
	}
	
}
