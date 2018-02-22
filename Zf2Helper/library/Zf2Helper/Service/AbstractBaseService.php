<?php

namespace Zf2Helper\Service;

use Zf2Helper\Model\BaseModel;

/**
 * 基础Service，所有业务Servcie均继承自该类
 * 
 * @author Jason
 * @date: 2015/4/6
 */
abstract class AbstractBaseService implements IServiceInterface {
	
	protected $dao; // DaoInterface类型
	
	public function __construct()
	{
		$this->dao = $this->_provideDao();
	}

	// 各service子类提供当前service需要使用的Dao对象
	abstract protected function _provideDao();
	
	public function prepareTableGateway($dbSettings)
	{
		$this->dao->prepare($dbSettings);
	}

	public function fetchById($id)
	{
		return $this->dao->fetchById($id);
	}
	
	public function fetch($where, $order=null, $fetchNum=-1, $skipNum=0)
	{
		return $this->dao->fetch($where, $order, $fetchNum, $skipNum);
	}
	
	public function fetchAll($order=null)
	{
		return $this->dao->fetchAll($order);
	}

	public function insert(BaseModel $model)
	{
		return $this->dao->insert($model);
	}

	public function update(BaseModel $model)
	{
		return $this->dao->update($model);
	}
	
	public function batchUpdate(array $set, $where = null)
	{
		return $this->dao->batchUpdate($set, $where);
	}
	
	public function deleteById($id)
	{
		return $this->dao->deleteById($id);
	}
	
	public function delete($where)
	{
		return $this->dao->delete($where);
	}
	
}
