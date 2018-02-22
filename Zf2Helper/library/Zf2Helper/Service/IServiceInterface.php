<?php

namespace Zf2Helper\Service;

use Zf2Helper\Model\BaseModel;

/**
 * Service工具类接口
 * 
 * @author Jason
 * @date: 2015/4/6
 */
interface IServiceInterface {
	
	// 初始化数据库操作的相关信息
	function prepareTableGateway($dbSettings);
	
	/**
	 * 根据Id获取对象
	 *
	 * @param  string|int $id
	 * @return Model
	 */
	function fetchById($id);
	
	/**
	 * 根据条件查询记录列表
	 * $where 查询条件 【示例： array('title'=>'Fifth news') 或  'id>1 and id<=6'】
	 * $order 排序条件 【示例： id desc】
	 * $fetchNum 要查询的记录数量
	 * $skipNum 要跳过的记录数量
	 * 
	 * @param Where|\Closure|string|array|Predicate\PredicateInterface $where
	 * @param string|array $order
	 * @param int $fetchNum
	 * @param int $skipNum
	 * 
	 * @return ResultSet
	 */
	function fetch($where, $order=null, $fetchNum=0, $skipNum=0);

	/**
	 * 获取所有记录
	 *
	 * @param string|array $order
	 * @return array(BaseModel)
	 */
	function fetchAll($order);
	
	/**
	 * 新增对象
	 * 
	 * @param  BaseModel $model
     * @return int
	 */
	function insert(BaseModel $model);
	
	/**
	 * 更新对象
	 * @param BaseModel $model
	 * @return int
	 */
	function update(BaseModel $model);
	
	/**
	 * 根据条件更新记录
	 * 
	 * @param  array $set
     * @param  string|array|\Closure $where
     * @return int
	 */
	function batchUpdate(array $set, $where = null);
	
	/**
	 * 根据Id删除记录
	 *
	 * @param  string|int $id
	 * @return int
	 */
	function deleteById($id);
	
	/**
	 * 根据条件删除记录
	 *
	 * @param  Where|\Closure|string|array $where
	 * @return int
	 */
	function delete($where);

}
