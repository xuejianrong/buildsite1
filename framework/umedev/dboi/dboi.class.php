<?php
class DBOI{
    protected $options          =   array();
    protected $redis            =   null;
    protected $masterDbServer = '';
    protected $slaveDbServer = '';
    protected $redisServer = '';
    protected $fullTableName = '';
    protected $operateArr = array();
    protected $transOn = 0;

    public function __construct() {
        $this->redis = new RedisCache();
    }

    public function debug($query_info){

    }


	//$dao->table()->field('name distinct, age distinct')->where(array('id'=>15))->select();
    public function getStep($dbServer){
        return mysql::getStep($dbServer);
    }





    public function insert($isExecute = true){
        $this->_parseTable();
        $options = $this->options;

        //如果插入的数据为空
        if(!isset($options['data'])){
            return false;
        }
        $data = $options['data'];
		foreach ($data as $value) {
			if(is_array($value)){
				$singleInsert = false;
			}else{
				$singleInsert = true;
			}
			break;
		}
        $this->options = array(); // 查询过后清空sql表达式组装 避免影响下次查询
        if($singleInsert){ //插入的是单条
            $values  =  $fields    = array();
            foreach ($data as $key=>$value){
                if(is_scalar($value)) { // 过滤非标量数据
					if(is_string($value)){
						//检测是否有"\"号,替换成两个
						$value = str_replace('\\', '\\\\', $value);
						//检测是否有"'"号.替换成“\'”
						$value = str_replace("'", "\\'", $value);
						$value = self::_filter($value);
						$values[]   =  "'" . $value . "'";
					}else{
						$values[]   =  $value;
					}
                    $fields[]   =  $this->parseKey($key);
                }
            }
			$values = '(' . implode(',', $values) . ')';
        }else{ //批量插入
            $fields = array_keys($data[0]);
            array_walk($fields, array($this, 'parseKey'));
            $values  =  array();
            foreach ($data as $record){
                $value = array();
                foreach($record as $key => $val){
                    if(is_scalar($val)) { // 过滤非标量数据
						if(is_string($val)){
							//检测是否有"\"号,替换成两个
							$val = str_replace('\\', '\\\\', $val);
							//检测是否有"'"号.替换成“\'”
							$val = str_replace("'", "\\'", $val);
							$val = self::_filter($val);
							$value[]   =  "'" . $val . "'";
						}else{
							$value[]   =  $val;
						}
                    }
                }
                $values[]    = '(' . implode(',', $value) . ')';
            }
			$values = implode(',', $values);
        }
        $sql = 'INSERT INTO ' . $this->fullTableName . ' (' . implode(',', $fields) . ') VALUES ' . $values;
		if(!$isExecute){
			return array(
				'masterDbServer'	=> $this->masterDbServer,
				'slaveDbServer'		=> $this->slaveDbServer,
				'redisServer'		=> $this->redisServer,
				'sqlStr'			=> $sql
			);
		}
        $insertId = mysql::query($sql, $this->masterDbServer);
        if($insertId){
            /*if($GLOBALS['DB_CONFIG']['IS_REDIS_ACTIVE'] && $GLOBALS['DB_CONFIG']['REDIS_SERVER'][$this->redisServer]['is_active']){
                $this->redis->connect($this->redisServer);
                if($GLOBALS['DB_CONFIG']['REDIS_SERVER'][$this->redisServer]['is_active']){
                    $this->_afterInsert($data, $insertId);
                }
            }*/
            if($this->transOn){
                $i = count($this->operateArr);
                $this->operateArr[$i]['type'] = 'insert';
                $this->operateArr[$i]['sql'] = $sql;
				$this->operateArr[$i]['table'] = $options['table'];
                //插入的是单条
                if($singleInsert){
                    $this->operateArr[$i]['data'] = $insertId;
                }else{  //批量插入
                    $stepLen = $this->getStep($this->slaveDbServer);  //获取步长
                    $aPkArr = array();  //新增记录的主键集合
                    for($j=0;$j<count($data);$j++){
                        $aPkArr[] = $insertId + $stepLen*$j;
                    }
                    $this->operateArr[$i]['data'] = $aPkArr;
                }
            }
        }
		if($insertId === false){
			$oException = Yii::$app->buildError('执行语句出错', false, [mysql_error(), $sql]);
			Yii::error((string)$oException);
		}
		return $insertId;
    }

    public function delete($isExecute = true){
        $this->_parseTable();
        $options = $this->options;

        //如果没有删除条件
        if(!isset($options['where'])){
            return false;
        }
        $where = $options['where'];
		if($GLOBALS['DB_CONFIG']['IS_REDIS_ACTIVE'] && $GLOBALS['DB_CONFIG']['REDIS_SERVER'][$this->redisServer]['is_active']){
			$pkArr = array();
			if(is_array($where) && isset($where['id']) && count($where) == 1){
				//如果不是已知主键
				if(is_array($where['id']) && $where['id'][0] != 'in'){
					$this->options['field'] = 'id';
					$pkArr = array();
					$pkArr = $this->select();   //查出要删除的主键
				}
				$this->options = array();   //清空sql表达式组装 避免影响下次操作
			}else{
				$this->options['field'] = 'id';
				$pkArr = array();
				$pkArr = $this->select();   //查出要删除的主键
			}
		}
        $whereStr = $this->_parseWhere($options['where']);

        //如果开启了事物，查出删除前的数据
        if($this->transOn){
            $selSql = 'SELECT * FROM ' . $this->fullTableName . $whereStr;
            $originalData = mysql::query($selSql, $this->slaveDbServer);
        }
        $sql = 'DELETE FROM ' . $this->fullTableName . $whereStr;
		if(!$isExecute){
			return array(
				'masterDbServer'	=> $this->masterDbServer,
				'slaveDbServer'		=> $this->slaveDbServer,
				'redisServer'		=> $this->redisServer,
				'sqlStr'			=> $sql
			);
		}
        $result = mysql::query($sql, $this->masterDbServer);
        //删除成功并且能连接redis
        if($result){
            if($GLOBALS['DB_CONFIG']['IS_REDIS_ACTIVE'] && $GLOBALS['DB_CONFIG']['REDIS_SERVER'][$this->redisServer]['is_active']){
                $this->redis->connect($this->redisServer);
                if($GLOBALS['DB_CONFIG']['REDIS_SERVER'][$this->redisServer]['is_active']){
                    $this->_afterDelete($options, $pkArr);
                }
            }
            if($this->transOn){
                $i = count($this->operateArr);
                $this->operateArr[$i]['type'] = 'delete';
                $this->operateArr[$i]['sql'] = $sql;
                $this->operateArr[$i]['data'] = $originalData; //删除前的数据
				$this->operateArr[$i]['table'] = $options['table'];
            }
        }
		if($result === false){
			$oException = Yii::$app->buildError('执行语句出错', false, [mysql_error(), $sql]);
			Yii::error((string)$oException);
		}
        return $result;
    }

    public function update($isExecute = true){
        $this->_parseTable();
        $options = $this->options;
		//如果没有设置更新数据和更新条件
        if(!isset($options['data']) || !isset($options['where'])){
            return false;
        }
        $data = $options['data'];
		//如果只是主键查找的话
		if(is_array($options['where']) && isset($options['where']['id'])){
			$where = $options['where'];
			//如果不是已知主键
			if(is_array($where['id']) && $where['id'][0] != 'in'){
				$this->options['field'] = 'id';
				$pkArr = array();
				$pkArr = $this->select();
			}
			$this->options = array();   //清空sql表达式组装 避免影响下次操作
		}else{  //非主键查找
			$this->options['field'] = 'id';
			$pkArr = array();
			$pkArr = $this->select();
		}
        //如果没有更新条件，则不允许更新
        if(!isset($options['where']) || empty($options['where'])){
            return false;
        }

        foreach($data as $key => $val){
            //  过滤非标量数据
            if(is_scalar($val)){
				if(is_string($val)){
					//检测是否有"\"号,替换成两个
					$val = str_replace('\\', '\\\\', $val);
					//检测是否有"'"号.替换成“\'”
					$val = str_replace("'", "\\'", $val);
					$val = self::_filter($val);
					$set[] = $this->parseKey($key) . '=' . "'" . $val . "'";
				}else{
					$set[] = $this->parseKey($key) . '=' . $val;
				}
            }elseif(is_array($val)){
				if($val[0] == 'add'){
					if(is_numeric($val[1])){
						$set[] = $this->parseKey($key) . '=' . $this->parseKey($key) . '+' . $val[1];
					}
				}elseif($val[0] == 'sub'){
					if(is_numeric($val[1])){
						$set[] = $this->parseKey($key) . '=' . $this->parseKey($key) . '-' . $val[1];
					}
				}
			}
        }
        $where = $this->_parseWhere($options['where']);

        if($this->transOn){
            $selSql = 'SELECT * FROM ' . $this->fullTableName . $where;
            $originalData = mysql::query($selSql, $this->slaveDbServer);
        }

        $sql = 'UPDATE ' . $this->fullTableName . ' SET ' . implode(',', $set) . $where;
		if(!$isExecute){
			return array(
				'masterDbServer'	=> $this->masterDbServer,
				'slaveDbServer'		=> $this->slaveDbServer,
				'redisServer'		=> $this->redisServer,
				'sqlStr'			=> $sql
			);
		}
        $result = mysql::query($sql, $this->masterDbServer);

        //更新成功并且能连接redis
        if($result){
            if($GLOBALS['DB_CONFIG']['IS_REDIS_ACTIVE'] && $GLOBALS['DB_CONFIG']['REDIS_SERVER'][$this->redisServer]['is_active']){
                $this->redis->connect($this->redisServer);
                if($GLOBALS['DB_CONFIG']['REDIS_SERVER'][$this->redisServer]['is_active']){
                    if(isset($pkArr)){
                        $this->_afterUpdate($data, $options, $pkArr);
                    }else{
                        $this->_afterUpdate($data, $options);
                    }
                }
            }
            if($this->transOn){
                $i = count($this->operateArr);
                $this->operateArr[$i]['type'] = 'update';
                $this->operateArr[$i]['sql'] = $sql;
                $this->operateArr[$i]['data'] = $originalData; //删除前的数据
				$this->operateArr[$i]['table'] = $options['table'];
            }
        }
		if($result === false){
			$oException = Yii::$app->buildError('执行语句出错', false, [mysql_error(), $sql]);
			Yii::error((string)$oException);
		}
        return $result;
    }

    public function select($isExecute = true){
        $this->_parseTable();
        $options = $this->options;
        $this->options = array(); // 查询过后清空sql表达式组装 避免影响下次查询
		//不执行的话
		if(!$isExecute){
			return $this->_getSqlInfo($options);
		}
		$specialFlag = false;
		//如果设置了field
		if(isset($options['field'])){
			$specialFlag = preg_match('/avg\(|AVG\(|count\(|COUNT\(|max\(|MAX\(|min\(|MIN\(|sum\(|SUM\(|distinct|DISTINCT|as|AS/', $options['field']);
		}
		if(isset($options['join'])){
			$specialFlag = true;
		}
		if(isset($options['order']) || isset($options['limit']) || isset($options['group'])){
			$specialFlag = true;
		}

		$sqlStr = '';
		//如果redis可用,查询field中没有别的表达式
        if($GLOBALS['DB_CONFIG']['IS_REDIS_ACTIVE'] && $GLOBALS['DB_CONFIG']['REDIS_SERVER'][$this->redisServer]['is_active'] && !$specialFlag){
            $this->redis->connect($this->redisServer);
            //如果连接redis成功
            if($GLOBALS['DB_CONFIG']['REDIS_SERVER'][$this->redisServer]['is_active']){
                $result = $this->_before_select($options);
                if($result['isSelect']){ //如果在缓存中查到了
                    $resultSet = $result['data'];
                }else{
                    if(isset($options['field']) && $options['field'] == 'id'){ //如果只是为了查主键
                        $sqlStr = $this->_buildSelectSql($options);
                        $resultSet = mysql::query($sqlStr, $this->slaveDbServer);
                    }else{ //否则查出主键列表再到缓存中根据主键查找
						$sqlStr = $this->_buildSelectSql($options);
                        $resultSet = $this->_index_select($options);
                    }
                }
            }else{ //否则直接查询数据库
                $sqlStr = $this->_buildSelectSql($options);
                $resultSet = mysql::query($sqlStr, $this->slaveDbServer);
            }
        }else{ //否则直接查询数据库
            $sqlStr = $this->_buildSelectSql($options);
            $resultSet = mysql::query($sqlStr, $this->slaveDbServer);
        }

        if(false === $resultSet) {
			$oException = Yii::$app->buildError('执行语句出错', false, [mysql_error(), $sqlStr]);
			Yii::error((string)$oException);
            return false;
        }elseif(empty($resultSet)) { // 查询结果为空
            return array();
        }

        return $resultSet;
    }

    //开启自定义事物
    public function startTrans(){
        $this->operateArr = array();    //清空操作记录数组
        $this->transOn = 1; //开启事物标志
    }

    //关闭事物
    public function commit(){
        $this->transOn = 0; //关闭事物标志
        $this->operateArr = array();    //清空操作记录数组
    }

    public function table($table){
        $this->options['table'] = $table;
        return $this;
    }


    public function fields($fields){
        if(is_string($fields) && strlen(trim($fields))>0){ //如果查询的字段存在，并且不为空
            $this->options['field'] = $fields;
        }
        return $this;
    }

    public function where($where){
        if(is_string($where) && '' != $where){ //如果条件是字符串并且不为空的话
            $this->options['where'] = $where;
        }elseif(is_array($where) && !empty($where)){ //如果条件是数组
            $this->options['where'] = $where;
        }
        return $this;
    }

    public function groupby($group){
        if(is_string($group) && strlen(trim($group))>0){ //如果分组字段存在，并且不为空
            $this->options['group'] = $group;
        }
        return $this;
    }

    /**
     * 指定查询数量
     * @access public
     * @param mixed $offset 起始位置
     * @param mixed $length 查询数量
     * @return Model
     */
    public function limit($offset,$length=null){
        $this->options['limit'] =   is_null($length)?$offset:$offset.','.$length;
        return $this;
    }

    public function orderby($order){
        if(is_string($order) && strlen(trim($order))>0){ //如果分组字段存在，并且不为空
            $this->options['order'] = $order;
        }
        return $this;
    }

    public function leftjoin($joinTable, $joinCondition){
		$aTableInfo = explode(',', $joinTable);
		$fullTableName = $aTableInfo[1];
		if(is_string($fullTableName) && strlen(trim($fullTableName))>0){ //如果分组字段存在，并且不为空
			if(isset($this->options['join'])){
				$this->options['join'] .= ' left join ' . $fullTableName . ' ' . $joinCondition;
			}else{
				$this->options['join'] = ' left join ' . $fullTableName . ' ' . $joinCondition;
			}
        }
        return $this;
    }

    public function union($union){

    }

    public function having($having){
		if(is_string($having) && strlen(trim($having))>0){ //如果分组字段存在，并且不为空
            $this->options['having'] = $having;
        }
        return $this;
    }


    /**
     * 设置数据对象值
     * @access public
     * @param mixed $data 数据
     * @return Model
     */
    public function data($data=''){
        if(is_array($data) && !empty($data)){
            $this->options['data'] = $data;
        }
        return $this;
    }

    /**
	 * 回滚事务
	 * @author 李亚林 后期精简:黄文非
	 * @return null
	 */
    public function rollBack(){
        if(empty($this->operateArr)){
            return;
        }
		//回滚关闭事务
		$this->transOn = 0;
        //反向遍历记录的操作
        for($i = count($this->operateArr) - 1; $i >= 0; $i--){
            $aOperate = $this->operateArr[$i];
			$type = &$aOperate['type'];
			$rollbackResult = false;
            if($type == 'insert'){
                //如果是新增操作，则把记录删掉
				$aWhere = [];
                if(!is_array($aOperate['data'])){
					$aWhere = array('id' => $aOperate['data']);
                }else{
					$aWhere = array('id' => array('in', $aOperate['data']));
                }
                $rollbackResult = $this->table($aOperate['table'])->where($aWhere)->delete();

            }elseif($type == 'delete'){
                //如果是删除操作，则插入原来的数据
                $rollbackResult = $this->table($aOperate['table'])->data($aOperate['data'])->insert();

            }elseif($type == 'update'){
                //如果是更新操作，则把记录更新成原来的数据
                $rollbackResult = true;
                foreach($aOperate['data'] as $data){
					//!!!!!
					if($i == 0){
						$this->operateArr[0]['db_data'] = $this->table($aOperate['table'])->where(array('id' => $data['id']))->select();
					}
					//!!!!!
                    $row = $this->table($aOperate['table'])->data($data)->where(array('id' => $data['id']))->update();
                    if(!$row){
                        $rollbackResult = false;
                    }
                }
            }

			if(!$rollbackResult){
				$oException = Yii::$app->buildError('回滚执行到第' . $i . '步出错', false, $this->operateArr);
				Yii::error((string)$oException);
			}
        }
    }

    private function _buildSelectSql($options){
        $fullTableName = $this->fullTableName;
		$fields = isset($options['field']) ? $options['field'] : '*';
		$where = isset($options['where']) ? $this->_parseWhere($options['where']) : '';
		$group = isset($options['group']) ? ' GROUP BY ' . $options['group'] : '';
		$having = isset($options['having']) ? ' HAVING ' . $options['group'] : '';
		$order = isset($options['order']) ? ' ORDER BY ' . $options['order'] : '';
		$limit = isset($options['limit']) ? ' LIMIT ' . $options['limit'] : '';
		if (!isset($options['join'])) {
			$sql = 'SELECT ' . $fields . ' FROM ' . $fullTableName . $where . $group . $having . $order . $limit;
		} else {
			$join = $options['join'];
			$sql = 'SELECT ' . $fields . ' FROM ' . $fullTableName . ' AS t1' . $join . $where . $group . $having . $order . $limit;
		}

        return $sql;
    }

    private function _parseWhere($xWhere){
        $whereStr = '';
        if(is_string($xWhere)){// 直接使用字符串条件
            $whereStr = $xWhere;
        }else{
			//使用数组表达式
            if(!isset($xWhere['id'])){ //如果不是主键查询
				throw Yii::$app->buildError('错误的查询条件', false, $xWhere);
            }

            if(!is_array($xWhere['id'])){ //如果是单个主键
                $whereStr = 'id = ' . $xWhere['id'];
            }elseif(is_array($xWhere['id']) && $xWhere['id'][0] == 'in') { //如果是主键批量查询
                if(is_string($xWhere['id'][1]) || is_integer($xWhere['id'][1])){ //如果查询的id是一串字符串
                    $whereStr = 'id in ' . '(' . $xWhere['id'][1] .')';
                }else{
					//如果查询的id是一个数组
                    $whereStr = 'id in ' . '(' . implode(',', array_filter($xWhere['id'][1])) . ')';
                }

            }else{
				throw Yii::$app->buildError('错误的查询条件', false, $xWhere);
            }
        }
        return empty($whereStr) ? '' : ' WHERE ' . $whereStr;
    }

    private function _parseTable(){
        $options = $this->options;
        if(isset($options['table']) && !empty($options['table'])){
            $aTableInfo = explode(',', $options['table']);
            $aDbServer = explode(':', $aTableInfo[0]);
            $fullTableName = $aTableInfo[1];
            $redisServer = $aTableInfo[2];
        }else{
			throw Yii::$app->buildError('必须指定要操作的表的信息');
        }
        $this->masterDbServer = $aDbServer[0];
        $this->slaveDbServer = $aDbServer[1];
        $this->redisServer = $redisServer;
        $this->fullTableName = $fullTableName;
    }

    private function _before_select($options){
        $data = null;
        $isSelect = false;
        if(!isset($options['where'])){
            return array('isSelect'=>$isSelect, 'data'=>$data);
        }
        $where = $options['where']; //查找条件
        if(is_array($where) && isset($where['id']) && count($where) == 1){ //是否主键查找
            $fullTableName = $this->fullTableName;
            $aFullTableName = explode('.', $fullTableName);
            $dbName = $aFullTableName[0];
            $tableName = $aFullTableName[1];
            if(isset($options['field']) && $options['field'] != '*')
                $value = explode(',', $options['field']);
            else
                $value = '';
            if(is_array($where['id'])){ //主键对应值是数组
                if($where['id'][0] == 'in'){ //主键对应值是数组并且数组第一个元素是in
                    $pkArr = array_pop($where['id']);
                    if(!is_array($pkArr)){ //主键对应值内是数组保存主键
                        $pkArr = explode(',', $pkArr);
                    }
					$pkArr = array_unique($pkArr);
					$this->redis->redis->multi(Redis::PIPELINE);
                    foreach($pkArr as $pk){
                        $key = $dbName . ':' . $tableName . ':' . $pk;
                        $this->redis->get($key, $value);
                        //if($result) $data[] = $result;
                    }
					$data = $this->redis->redis->exec();
                    $isSelect = true;
                    $recordCount = count(array_filter($pkArr)); //记录条数
                }
            }else{ //主键对应值不是数组--那就是单个值
                $pk = $where['id'];
                $key = $dbName . ':' . $tableName . ':' . $pk;
				$this->redis->redis->multi(Redis::PIPELINE);
                $this->redis->get($key, $value);
				$data = $this->redis->redis->exec();
                $isSelect = true;
                $recordCount = 1;   //如果是单个值，那么只有一条记录
            }
        }
        if($isSelect){
			$data = array_filter($data);
            if(count($data) != $recordCount){ //如果查到的记录数和要查的不一致
				$mysqlOption = array();
				$mysqlOption['table'] = $options['table'];
				$mysqlOption['where'] = $options['where'];
                $sql = $this->_buildSelectSql($mysqlOption);
                $mysqlData = mysql::query($sql, $this->slaveDbServer);
				if($mysqlData === false){
					throw Yii::$app->buildError('查询出错', false, $sql);
				}
                if(count($mysqlData) > count($data)){ //如果mysql查出来的记录数大于redis记录数
					$this->redis->redis->multi(Redis::PIPELINE);
                    foreach($mysqlData as $record){
                        $key = $dbName . ':' . $tableName . ':' . $record['id'];
                        $this->redis->add($key, $record); //向redis中添加记录]
                    }
					$this->redis->redis->exec();
                    //$resultData = $this->_before_select($options);
                    //return $resultData;
					return array('isSelect'=>$isSelect, 'data'=>$mysqlData);
                }elseif(count($mysqlData) < count($data)){	//如果redis记录数大于mysql
					$this->redis->redis->multi(Redis::PIPELINE);
                    foreach($pkArr as $pk){
                        $key = $dbName . ':' . $tableName . ':' . $pk;
                        $this->redis->delete($key);
                    }
					$data = $this->redis->redis->exec();
					//$resultData = $this->_before_select($options);
                    //return $resultData;
					return array('isSelect'=>$isSelect, 'data'=>$mysqlData);
				}
				sort($data);
            }
			if($data && isset($options['field']) && trim($options['field']) && trim($options['field']) != '*'){
				$dataBak = $data;
				$data = array();
				$options['field'] = str_replace('`', '', $options['field']);
				$aFields = explode(',', $options['field']);
				foreach($aFields as $key => $field){
					$aFields[$key] = trim($field);
				}
				foreach($dataBak as $records){
					$aRecords = array();
					foreach($aFields as $field){
						$aRecords[$field] = $records[$field];
					}
					$data[] = $aRecords;
				}
			}
        }
        return array('isSelect'=>$isSelect, 'data'=>$data);
    }

    private function _index_select($options){
        $mySqlOptions = $options;
        $mySqlOptions['field'] = 'id';
        $sql = $this->_buildSelectSql($mySqlOptions);
        $indexArr = mysql::query($sql, $this->slaveDbServer);
        if($indexArr === false){
			return false;
		}if(empty($indexArr)){
            return null;
        }
        $pkArr = array();
        foreach($indexArr as $index){
            $pkArr[] = $index['id'];
        }
        $selectOptions = $options;
        $selectOptions['where'] = array('id'=>array('in', $pkArr));
        $result = $this->_before_select($selectOptions);
        return $result['data'];
    }

    /**
     * 字段和表名处理添加`
     * @access protected
     * @param string $key
     * @return string
     */
    protected function parseKey(&$key) {
        $key   =  trim($key);
        if(!preg_match('/[,\'\"\*\(\)`.\s]/',$key)) {
           $key = '`'.$key.'`';
        }
        return $key;
    }

    private function _afterInsert($data, $insertId){
        $fullTableName = $this->fullTableName;
        $aFullTableName = explode('.', $fullTableName);
        $dbName = $aFullTableName[0];
        $tableName = $aFullTableName[1];
		foreach ($data as $value) {
			if(is_array($value)){
				$singleInsert = false;
			}else{
				$singleInsert = true;
			}
			break;
		}
        if($singleInsert){    //如果是插入单条的话
			if(!isset($data['id'])){
				$data['id'] = $insertId;
			}
            $key = $dbName . ':' . $tableName . ':' . $data['id'];
            $this->redis->add($key, $data);
        }else{  //批量插入数据
            $stepLen = $this->getStep($this->slaveDbServer);
            $i = 0;
			$this->redis->redis->multi(Redis::PIPELINE);
            foreach($data as $record){
				if(!isset($record['id'])){
					$record['id'] = $insertId + $stepLen * $i;
				}
                $key = $dbName . ':' . $tableName . ':' . $record['id'];
                $this->redis->add($key, $record);
				$i++;
            }
			$this->redis->redis->exec();
        }
    }

    private function _afterUpdate($data, $options, $aPkArr = null){
        $fullTableName = $this->fullTableName;
        $aFullTableName = explode('.', $fullTableName);
        $dbName = $aFullTableName[0];
        $tableName = $aFullTableName[1];

        //如果有了已知主键
        if(!empty($aPkArr)){
			$this->redis->redis->multi(Redis::PIPELINE);
            foreach($aPkArr as $pk){
                $key = $dbName . ':' . $tableName . ':' . $pk['id'];
                //$this->redis->update($key, $data);
				$this->redis->delete($key);
            }
			$this->redis->redis->exec();
        }else{
            $where = $options['where'];

            if(count($where) == 1 && isset($where['id'])){ //是否主键查找
                if(is_array($where['id'])){ //主键对应值是数组
                    if($where['id'][0] == 'in'){ //主键对应值是数组并且数组第一个元素是in
                        $aPkArr = array_pop($where['id']);
                        if(!is_array($aPkArr)){ //主键对应值内是数组保存主键
                            $aPkArr = explode(',', $aPkArr);
                        }
						$this->redis->redis->multi(Redis::PIPELINE);
                        foreach($aPkArr as $pk){
                            $key = $dbName . ':' . $tableName . ':' . $pk;
                            //$this->redis->update($key, $data);
							$this->redis->delete($key);
                        }
						$this->redis->redis->exec();
                    }
                }else{ //主键对应值不是数组--那就是单个值
                    $pk = $where['id'];
                    $key = $dbName . ':' . $tableName . ':' . $pk;
                    //$this->redis->update($key, $data);
					$this->redis->delete($key);
                }
            }
        }
    }

    private function _afterDelete($aOptions, $aPkArr = array()){
        $fullTableName = $this->fullTableName;
        $aFullTableName = explode('.', $fullTableName);
        $dbName = $aFullTableName[0];
        $tableName = $aFullTableName[1];

        //如果有了已知主键
        if(!empty($aPkArr)){
			$this->redis->redis->multi(Redis::PIPELINE);
            foreach($aPkArr as $pk){
                $key = $dbName . ':' . $tableName . ':' . $pk['id'];
                $this->redis->delete($key);
            }
			$this->redis->redis->exec();
        }else{
            $where = $aOptions['where'];
            if(count($where) == 1 && isset($where['id'])){ //是否主键查找
                if(is_array($where['id'])){ //主键对应值是数组
                    if($where['id'][0] == 'in'){ //主键对应值是数组并且数组第一个元素是in
                        $aPkArr = array_pop($where['id']);
                        if(!is_array($aPkArr)){ //主键对应值内是数组保存主键
                            $aPkArr = explode(',', $aPkArr);
                        }
						$this->redis->redis->multi(Redis::PIPELINE);
                        foreach($aPkArr as $pk){
                            $key = $dbName . ':' . $tableName . ':' . $pk;
                            $this->redis->delete($key);
                        }
						$this->redis->redis->exec();
                    }
                }else{ //主键对应值不是数组--那就是单个值
                    $pk = $where['id'];
                    $key = $dbName . ':' . $tableName . ':' . $pk;
                    $this->redis->delete($key);
                }
            }
        }
    }

	private function _getSqlInfo($options){

		$sqlStr = $this->_buildSelectSql($options);
		return array(
			'masterDbServer'	=> $this->masterDbServer,
			'slaveDbServer'		=> $this->slaveDbServer,
			'redisServer'		=> $this->redisServer,
			'sqlStr'			=> $sqlStr
		);
	}

	private static function _filter($value = ''){
		if($value == ''){
			return '';
		}
		$value = self::_xreplace('and',		'\and',		$value);
		$value = self::_xreplace('execute',	'\execute',	$value);
		$value = self::_xreplace('update',	'\update',	$value);
		$value = self::_xreplace('count',	'\count',	$value);
		$value = self::_xreplace('chr',		'\chr',		$value);
		$value = self::_xreplace('mid',		'\mid',		$value);
		$value = self::_xreplace('master',	'\master',	$value);
		$value = self::_xreplace('truncate','\truncate',$value);
		$value = self::_xreplace('char',	'\char',	$value);
		$value = self::_xreplace('declare',	'\declare',	$value);
		$value = self::_xreplace('select',	'\select',	$value);
		$value = self::_xreplace('between',	'b\etween',	$value);
		$value = self::_xreplace('delete',	'\delete',	$value);
		$value = self::_xreplace('insert',	'\insert',	$value);
		$value = self::_xreplace('like',	'\like',	$value);
		$value = self::_xreplace('drop',	'\drop',	$value);
		$value = self::_xreplace('create',	'\create',	$value);
		$value = self::_xreplace('modify',	'\modify',	$value);
		$value = self::_xreplace('rename',	'r\ename',	$value);
		$value = self::_xreplace('alter',	'\alter',	$value);
		$value = self::_xreplace('cast',	'\cast',	$value);
		$value = self::_xreplace('or',		'\or',		$value);
		$value = self::_xreplace('join',	'\join',	$value);
		$value = self::_xreplace('set',		'\set',		$value);
		$value = self::_xreplace('union',	'\union',	$value);
		$value = self::_xreplace('table',	't\able',	$value);
		$value = self::_xreplace('database','\database',$value);
		return $value;
	}

	private static function _xreplace($find, $replace, $str){
		$wordLength = strlen($find);
		$position = $lastPosition = 0;
		$aTmpPreviousWord = array();
		$aTmpWord = array();
		$founds = false;
		do{
			if(!$founds){
				$startPos = 0;
			}else{
				$startPos = $lastPosition + $wordLength;
			}
			$position = stripos($str, $find, $startPos);
			if($position !== false){
				$aTmpWord[] = substr($str, $position, $wordLength);
				if(!$founds){
					$startSub = $position==0?$position - $lastPosition:0;
					$subLength = $position;
				}else{
					$startSub = $lastPosition + $wordLength;
					$subLength = $position - $startSub;
				}
				$aTmpPreviousWord[] = substr($str, $startSub, $subLength);
				$founds = true;
				$lastPosition = $position;
			}else{
				$aTmpWord[] = '';
				$aTmpPreviousWord[] = substr($str, $lastPosition + $wordLength);
			}
		}while($position !== false);
		if(!$founds){
			return $str;
		}
		$insertPosition = strpos($replace, '\\');
		foreach($aTmpWord as &$word){
			if($word == ''){
				continue;
			}
			$preStr = substr($word, 0, $insertPosition);
			$sufStr = substr($word, $insertPosition, $wordLength - $insertPosition);
			$word = $preStr . '\\' . $sufStr;
		}
		$result = '';
		foreach($aTmpWord as $key => $value){
			$result .= $aTmpPreviousWord[$key] . $value;
		}
		return $result;
	}

}


