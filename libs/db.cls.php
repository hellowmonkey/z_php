<?php

defined('V') OR exit('非法访问');

Class DB
{

  private $link_id;
  private $handle;

  //构造函数
  public function __construct($config=array())
  {
    if(empty($config)) $config = C('database');
    $this->time = $this->microtime_float();
    $this->connect($config["hostname"], $config["username"], $config["password"], $config["database"], $config["pconnect"]);
  }

  //数据库连接
  public function connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect = 0, $charset = 'utf8')
  {
    if ($pconnect == 0) {
      $this->link_id = @mysql_connect($dbhost, $dbuser, $dbpw, true);
      if (!$this->link_id) {
        $this->halt("数据库连接失败");
      }
    } else {
      $this->link_id = @mysql_pconnect($dbhost, $dbuser, $dbpw);
      if (!$this->link_id) {
        $this->halt("数据库持久连接失败");
      }
    }
    if (!@mysql_select_db($dbname, $this->link_id)) {
      $this->halt('数据库选择失败');
    }
    @mysql_query("set names " . $charset);
  }

  //查询
  public function query($sql)
  {
    $query = mysql_query($sql, $this->link_id);
    if (!$query) $this->halt('Query Error: ' . $sql);
    return $query;
  }

  //获取一条记录（MYSQL_ASSOC，MYSQL_NUM，MYSQL_BOTH）
  public function get_one($sql, $result_type = MYSQL_ASSOC)
  {
    $query = $this->query($sql);
    $rt = mysql_fetch_array($query, $result_type);
    return $rt;
  }

	public function get_row($sql)
  {
    $query = $this->query($sql);
    $rt = mysql_fetch_row($query);
    return $rt[0];
  }

  //获取全部记录
  public function get_all($sql, $result_type = MYSQL_ASSOC)
  {
    $query = $this->query($sql);
    $i = 0;
    $rt = array();
    while ($row = mysql_fetch_array($query, $result_type)) {
      $rt[$i] = $row;
      $i++;
    }
    return $rt;
  }

  //插入
  public function insert($table, $dataArray)
  {
    if (!is_array($dataArray) || count($dataArray) <= 0) {
      $this->halt('没有要插入的数据');
      return 0;
    }
    $fields=array();
		$values=array();
    while (list($key, $val) = each($dataArray)) {
      $fields[]= "`$key`";
      $values[]= "'$val'";
    }
    $fields = implode(' , ', $fields);
    $values = implode(' , ', $values);
    $sql = "INSERT INTO `$table`( $fields ) VALUES ( $values )";
    if (!$this->query($sql)) return 0;
    return $this->insert_id();
  }

  //更新
  public function update_result($table, $dataArray, $condition = "")
  {
    if (!is_array($dataArray) || count($dataArray) <= 0) {
      $this->halt('没有要更新的数据');
      return false;
    }
    $sets = array();
    while (list($key, $val) = each($dataArray))
      $sets[] .= "`$key` = '$val'";
	  $sets=implode(' , ',$sets);
    $sql = "UPDATE `$table` SET  $sets ";
	if($condition) $sql.= " WHERE  $condition ";
    if (!$this->query($sql)) return false;
    return true;
  }
  
    //更新
  public function update($table, $dataArray, $condition = "")
  {
    return $this->update_result($table,$dataArray,$condition);
  }
  //删除
  public function del($table, $condition = "")
  {
    if (empty($condition)) {
      $this->halt('没有设置删除的条件');
      return false;
    }
    $sql = "DELETE FROM `$table` ";
	  if($condition) $sql.= " WHERE  $condition ";
    if (!$this->query($sql)) return false;
    return true;
  }

  //返回结果集
  public function fetch_array($query, $result_type = MYSQL_ASSOC)
  {
    return mysql_fetch_array($query, $result_type);
  }

  //获取记录条数
  public function num_rows($results)
  {
    if (!is_bool($results)) {
      $num = mysql_num_rows($results);
      return $num;
    } else {
      return 0;
    }
  }

  //释放结果集
  public function free_result()
  {
    $void = func_get_args();
    foreach ($void as $query) {
      if (is_object($query) && get_resource_type($query) === 'mysql result') {
        return mysql_free_result($query);
      }
    }
  }

  //获取最后插入的id
  public function insert_id()
  {
    $id = mysql_insert_id($this->link_id);
    return $id;
  }

  //关闭数据库连接
  protected function close()
  {
    $this->write_log("已关闭数据库连接");
    return @mysql_close($this->link_id);
  }

  //错误提示
  private function halt($msg = '')
  {
    $msg .= "\r\n" . mysql_error();
    die($msg);
  }

  //析构函数
  public function __destruct()
  {

  }


  //获取毫秒数
  public function microtime_float()
  {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
  }

  function begintrans(){
    mysql_query('START TRANSACTION',$this->link_id);

  }

  function rollback(){
    mysql_query('ROLLBACK',$this->link_id);
    mysql_query('END');
  }

  function commit(){
      mysql_query('COMMIT',$this->link_id);
      mysql_query('END');
  }
}
?>