<?php

namespace Zf2Helper\Dao;

use Zf2Helper\Model\BaseModel;

/**
 * 基础DAO，所有业务DAO均继承自该类
 * 通过该DAO进行数据库操作的，Model必须提供setter方法，否则无法为Model对象组装数据，即rowObjectPrototype对应的Model类
 * 
 * @author Jason
 * @date: 2015/4/6
 */
abstract class AbstractBaseDao implements IDaoInterface {
	
	protected $dbAdapter;
	protected $dbSettings;
	protected $hydrator;
	protected $resultSet;
	protected $rowObjectPrototype;
	protected $tableGateway;

	private $conn;

	// 默认的id字段名称
	private $_defaultIdName = 'id';
	
	public function __construct()
	{
		$this->hydrator = new \Zend\Stdlib\Hydrator\ClassMethods(true);
	}

	// 各dao子类提供当前dao需要操作的Model对象
	abstract protected function _provideObjectPrototype();
	
	// 各dao子类提供当前dao需要操作的Model对应的表名
	abstract protected function _provideTableName();
	
	// 各dao子类提供当前dao需要操作的Model对应的表主键字段名，默认为id
	protected function _provideIdName()
	{
		return $this->_defaultIdName;
	}
	
	public function prepare($dbSettings)
	{
		$this->dbSettings = $dbSettings;
		$this->dbAdapter = new \Zend\Db\Adapter\Adapter($this->dbSettings);
		$this->rowObjectPrototype = $this->_provideObjectPrototype();
		$this->resultSet = new \Zend\Db\ResultSet\HydratingResultSet($this->hydrator, $this->rowObjectPrototype);
		$this->tableGateway = new \Zend\Db\TableGateway\TableGateway($this->_provideTableName(), $this->dbAdapter, null, $this->resultSet);
	}

	/**
	 * 获取数据库连接
	 * @return mixed
	 */
	protected function getDbConnection(){
		if(!$this->conn){
			$this->conn = $this->dbAdapter->getDriver()->getConnection();//获取当前连接
		}
		return $this->conn;
	}

	/**
	 * 将模型转换成数组
	 * @param BaseModel $model
	 */
	protected function modelToArray($model)
	{
		if(empty($model))
		{
			return array();
		}
		else
		{
			$data = $this->hydrator->extract($model);
			if($this->_provideIdName()!=$this->_defaultIdName) // 如果主键名称不是默认的id
			{
				if(empty($data)==false)
				{
					if(array_key_exists($this->_defaultIdName, $data))
					{
						unset($data[$this->_defaultIdName]);
					}
				}
			}
			return $data;
		}
	}
	
	public function fetchById($id)
	{
		$resultSet = $this->tableGateway->select(array($this->_provideIdName() => $id));
		if(empty($resultSet) || $resultSet->count()==0)
		{
			return null;
		}
		else
		{
			return $resultSet->current();
		}
	}
	
	public function fetch($where, $order=null, $fetchNum=-1, $skipNum=0)
	{
		$select = $this->tableGateway->getSql()->select();
		
		if(empty($where)==false)
		{
			$select->where($where); // 先where
		}
		
		if(empty($order)) // 再order by
		{
			$order = $this->_provideIdName();
		}
		$select->order($order);
		
		if($fetchNum>=0) // 最后limit offset
		{
			$select->limit($fetchNum);
			if($skipNum>0)
			{
				$select->offset($skipNum);
			}
		}
		
		return $this->tableGateway->selectWith($select);
	}

	public function fetchAll($order=null)
	{
		$select = $this->tableGateway->getSql()->select();
		if(empty($order))
		{
			$order = $this->_provideIdName();
		}
		$select->order($order);

		return $this->tableGateway->selectWith($select);
	}

	public function insert(BaseModel $model)
	{
		return $this->tableGateway->insert($this->modelToArray($model));
	}
	
	public function update(BaseModel $model)
	{
		return $this->tableGateway->update($this->modelToArray($model), array($this->_provideIdName()=>$model->getId()));		
	}
	
	public function batchUpdate(array $set, $where = null)
	{
		return $this->tableGateway->update($set, $where);
	}

	public function deleteById($id)
	{
		return $this->tableGateway->delete(array($this->_provideIdName()=>$id));
	}
	
	public function delete($where)
	{
		return $this->tableGateway->delete($where);
	}

	/**
	 * 直接SQL查询的方法
	 *
	 */
	function queryBySql($sql, $parametersOrQueryMode = self::QUERY_MODE_PREPARE,
						\Zend\Db\ResultSet\ResultSetInterface $resultPrototype = null){
		return $this->dbAdapter->query($sql,$parametersOrQueryMode,$resultPrototype);
	}

	/**
	 * 开启事务操作
	 * @return mixed
	 */
	function beginTransaction(){
		$this->getDbConnection()->beginTransaction();//开启事务
	}

	/**
	 * 提交事务操作
	 * @return mixed
	 */
	function commitTransaction(){
		$this->getDbConnection()->commit();    //提交事务
	}

	/**
	 * 回滚事务操作
	 * @return mixed
	 */
	function rollbackTransaction(){
		$this->getDbConnection()->rollback();  //回滚事务
	}
}
