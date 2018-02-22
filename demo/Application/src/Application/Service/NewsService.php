<?php

namespace Application\Service;

use Zf2Helper\Service\AbstractBaseService;

class NewsService extends AbstractBaseService {

	protected function _provideDao()
	{
		// 注意：这里需要返回一个Dao对象，不是字符串
		return new \Application\Dao\NewsDao();
	}
	
}