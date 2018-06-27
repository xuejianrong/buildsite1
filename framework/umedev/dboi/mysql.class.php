<?php
abstract class Mysql{

    final static function connect($db_server_name){

		if(!isset($GLOBALS['connection'][$db_server_name])){
            $db_username=$GLOBALS['DB_CONFIG']['MYSQL_SERVER'][$db_server_name]['username'];
            $db_password=$GLOBALS['DB_CONFIG']['MYSQL_SERVER'][$db_server_name]['password'];
            $db_host=$GLOBALS['DB_CONFIG']['MYSQL_SERVER'][$db_server_name]['host'];
            $GLOBALS['connection'][$db_server_name] = mysql_connect($db_host,$db_username,$db_password) or die('--');
            mysql_query("set names " . DATABASE_CHARSET , $GLOBALS['connection'][$db_server_name]);
        }
        return $GLOBALS['connection'][$db_server_name];
    }

    final static function query($sql,$db_server_name, $specialFlag = false){
        $query_type=substr(trim($sql),0,6);
        $connection=self::connect($db_server_name);
        $result=mysql_query($sql,$connection);
        if(false===$result){
			if(IS_DEBUG == 1){
				debug('<b>Mysql query error:</b><br><br>Sql string: '.$sql.'<br><br><b>Error Info: </b>' . mysql_error($connection), 111);
			}else{
				return false;
			}
        }
		$aResults = array(
			'mysql_return' => $result,
		);
        if($specialFlag){
			return true;
		}if ($query_type=='insert' || $query_type=='INSERT'){
            $aResults['mysql_affected_rows']=mysql_affected_rows($connection);
            $aResults['mysql_insert_id']=mysql_insert_id($connection);
            if($aResults['mysql_return']){
				if($aResults['mysql_insert_id']){
					return $aResults['mysql_insert_id'];
				}else{
					return $aResults['mysql_affected_rows'];
				}
            }else{
                return $aResults['mysql_return'];
            }
        }elseif($query_type=='delete' || $query_type=='DELETE'){
            $aResults['mysql_affected_rows']=mysql_affected_rows($connection);
            if($aResults['mysql_return']){
                return $aResults['mysql_affected_rows'];
            }else{
                return $aResults['mysql_return'];
            }
        }elseif($query_type=='update' || $query_type=='UPDATE'){
            $aResults['mysql_affected_rows']=mysql_affected_rows($connection);
            if($aResults['mysql_return']){
                return $aResults['mysql_affected_rows'];
            }else{
                return $aResults['mysql_return'];
            }
        }elseif($query_type=='select' || $query_type=='SELECT'){
            $aResults['mysql_num_rows']=mysql_num_rows($result);
            $aResults['mysql_num_fields']=mysql_num_fields($result);
            $aResults['data']=array();
            while($rows=mysql_fetch_assoc($result)){$aResults['data'][]=$rows;}
            return $aResults['data'];
        }else{
            die( (IS_DEBUG==1) ? '<b>Mysql query error:</b><br><br>Sql string: '.$sql.'<br><br><b>Error Info: </b>'.mysql_error() : 'Server Error DBOI500' );
        }
    }

    final static function getStep($db_server_name){
        $sql = "SHOW VARIABLES LIKE 'auto_increment_increment'";
        $connection=self::connect($db_server_name);
        $queryID = mysql_query($sql, $connection);
        $result = mysql_result($queryID, 0, 'value');
        return $result;
    }

}
?>