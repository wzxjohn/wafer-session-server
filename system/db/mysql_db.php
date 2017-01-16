<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
/**
 * Created by PhpStorm.
 * User: ayisun
 * Date: 2016/10/1
 * Time: 16:47
 */
class mysql_db
{

    private $host= null;
    private $port= null;
    private $username= null;
    private $passwd = null;
    private $database = null;

    public function __construct()
    {
        require_once "system/load_config.php";
        require_once('system/log/log.php');
        $load_config = new load_config();
        $config = $load_config->fc_load_config("system/db/db.ini");
        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->username = $config['user_name'];
        $this->passwd = $config['pass_wd'];
        $this->database = $config['data_base'];
    }

    /**
     * @param $sql
     * @return string
     * 描述:安全过滤sql,防止sql注入
     */
    function safe($sql)
    {
        if (get_magic_quotes_gpc()) {
            $sql = stripslashes($sql);
        }
        $sql = mysql_real_escape_string($sql);
        return $sql;
    }

    /**
     * @param $sql
     * @return bool
     * 描述:执行Mysql增删改操作
     */
    public function query_db($sql)
    {
        $con = new mysqli($this->host, $this->username, $this->passwd, $this->database, $this->port);
        if (!$con->connect_error) {
            $con->set_charset("utf8mb4");
            $mysql_result = $con->query($sql);
            if ($mysql_result === false) {
                $con->close();
                log_message("ERROR","$sql mysql_err $con->connect_errno $con->connect_error");
                return false;
            }
            $con->close();
            return true;
        } else {
            log_message("ERROR","$sql mysql_connect_err $con->connect_errno $con->connect_error");
            return false;
        }
    }

    /**
     * @param $sql
     * @return bool|resource
     * 描述：执行mysql查询操作
     */
    public function select_db($sql)
    {
        $con = new mysqli($this->host, $this->username, $this->passwd, $this->database, $this->port);
        if (!$con->connect_error) {
            $con->set_charset("utf8mb4");
            $result = $con->query($sql);
            if($result->num_rows < 1) {
                $result->close();
                $con->close();
                return false;
            }
            return $result->fetch_array();
        } else {
            log_message("ERROR","$sql mysql_connect_err $con->connect_errno $con->connect_error");
            return false;
        }
    }

    public function init_db($sql){
        $con = new mysqli($this->host, $this->username, $this->passwd, $this->database, $this->port);
        if (!$con->connect_error) {
            $con->set_charset("utf8mb4");
            $result = $con->query("$sql",$con);
            if($result===false){
                log_message("ERROR","$sql mysql_err $con->connect_errno $con->connect_error");
                return false;
            }
            return true;
        }else{
            log_message("ERROR","$sql mysql_connect_err $con->connect_errno $con->connect_error");
            return false;
        }
    }
}