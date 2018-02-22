<?php

namespace Zf2Helper\Model;

/**
 * 模型基础类
 * 
 * @author Jason
 * @date 2015/4/8
 */
abstract class BaseModel {

	abstract public function getId();
	
	abstract public function setId($id);

}
