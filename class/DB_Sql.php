<?php
namespace Aplikasi\Kitab;//echo __NAMESPACE__;
class DB_Sql
{
#==================================================================================================
#--------------------------------------------------------------------------------------------------
	function __construct()
	{
		$this->db = new \Aplikasi\Kitab\DB_Pdo(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);
		//$this->db = new \Aplikasi\Kitab\DB_Mysqli(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);
	}
#--------------------------------------------------------------------------------------------------
	# show sql only without run to database
	function showSql($key = 0)
	{
		$sql[0] = 'SHOW TABLES LIKE "' . DB_TABLE . '"';
		$sql[1] = " CREATE TABLE `" . DB_TABLE . "` (\r"
		. " `id` INT(11) NOT NULL AUTO_INCREMENT,\r"
		. " `name` VARCHAR(64) NOT NULL,\r"
		. " `original_name` VARCHAR(64) NOT NULL,\r"
		. " `mime_type` VARCHAR(20) NOT NULL,\r"
		. " PRIMARY KEY(`id`)\r"
		. " ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";

		return $sql[$key];
	}
#--------------------------------------------------------------------------------------------------
	# show table
	function showTables()
	{
		$sql = 'SHOW TABLES LIKE "' . DB_TABLE . '"';
		//$bindArray = array('id' => $id);
		$result = $this->db->selectAllMeta($sql);
		$rowCount = $this->db->rowCount($sql);

		return array($result,$rowCount);		
	}
#--------------------------------------------------------------------------------------------------
	# create table
	function createTable()
	{
		$sql = " CREATE TABLE `" . DB_TABLE . "` (\r"
		. " `id` INT(11) NOT NULL AUTO_INCREMENT,\r"
		. " `name` VARCHAR(64) NOT NULL,\r"
		. " `original_name` VARCHAR(64) NOT NULL,\r"
		. " `mime_type` VARCHAR(20) NOT NULL,\r"
		. " PRIMARY KEY(`id`)\r"
		. " ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";
		$result = $this->db->selectAllMeta($sql);

		return $result;
	}
#--------------------------------------------------------------------------------------------------
	# insert new image in database
	function insertDatabase01($uploadfile,$file,$mtype)
	{
		$sql = ' INSERT INTO `' . DB_TABLE . '`'
		. ' (name, original_name, mime_type)'
		. ' VALUES (:name, :oriname, :mime)';
		$bindArray = array(
			':name' => basename($uploadfile),
			':oriname' => basename($file['name']),
			':mime' => $mtype
		);
		list($lastInsertId,$meta) = $this->db->insertAll($sql,$bindArray);

		return array($lastInsertId,$meta);
	}
#--------------------------------------------------------------------------------------------------
	# list Image from database
	function listImage()
	{
		$sql = ' SELECT id, name, original_name, mime_type'
		. ' FROM `' . DB_TABLE .'`';
		$result = $this->db->selectAllMeta($sql);

		return $result;
	}
#--------------------------------------------------------------------------------------------------
	# find the image in the browser
	public function findImage($id)
	{
		$sql = 'SELECT name, original_name, mime_type '
		. 'FROM `' . DB_TABLE . '` WHERE id=:id';
		$bindArray = array('id' => $id);
		$result = $this->db->selectAllMeta($sql,$bindArray);

		return $result;
	}
#--------------------------------------------------------------------------------------------------
	# delete the image in the browser
	public function deleteImage($id)
	{
		$sql = 'DELETE FROM `' . DB_TABLE . '` WHERE id=:id';
		$bindArray = array('id' => $id);
		$result = $this->db->selectAllMeta($sql,$bindArray);

		return $result;
	}
#--------------------------------------------------------------------------------------------------
#==================================================================================================
}