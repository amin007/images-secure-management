<?php
namespace Aplikasi\Kitab;//echo __NAMESPACE__;
class DB_Pdo extends \PDO
{
#==================================================================================================================
#------------------------------------------------------------------------------------------------------------------
	public function __construct($DB_TYPE, $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS)
	{
		try
		{
			parent::__construct($DB_TYPE . ':host=' . $DB_HOST . ';dbname=' . $DB_NAME
			. ';charset=utf8', $DB_USER, $DB_PASS);
			//parent::setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTIONS);
			//https://www.barberriley.com/web-development/php/solved-pdo-message-malformed-utf-8-characters-possibly-incorrectly-encoded
		}
		catch (PDOException $e)
		{
			echo '<pre>';
			echo $e->getMessage();
			echo '</pre>';
			//<br><a href="' . URL . 'ruangtamu/logout">Keluar</a>
			exit;
		}
	}
#------------------------------------------------------------------------------------------------------------------
	/**
	 * bigError
	 * @param papar $problem yang dialami
	 * @exit
	 */
	public function bigError($sth,$problem)
	{
		//echo '<hr>Name class :' . __METHOD__ . '()<hr>';
		//$sth->debugDumpParams(); # papar sql balik
		# true flag returns val rather than print;
		$errorInfo = print_r($problem, true);
		$error  = 'PDO::errorInfo():';
		$error .= '<pre>' . $errorInfo . '</pre>';
		echo $error; # do what you wish with this param, write to log file etc...
		exit;
	}
#------------------------------------------------------------------------------------------------------------------
	/**
	 * selectAll
	 * @param string $sql An SQL string
	 * @param array $array Paramters to bind
	 * @param constant $fetchMode A PDO Fetch mode
	 * @return mixed
	 */
	public function selectAll($sql, $array = array(), $fetchMode = \PDO::FETCH_ASSOC)
	{
		//echo '<hr>Name class :' . __METHOD__ . '()<hr>';
		//echo '<hr><pre>'; print_r($sql) . '</pre><hr>';
		$sth = $this->prepare($sql);
		foreach ($array as $key => $value)
		{
			$sth->bindValue("$key", $value);
		}

		$sth->execute();
		$problem = $sth->errorInfo(); # semak jika ada error
		if($problem[0]=='00000')# pulangkan pembolehubah
			return $sth->fetchAll($fetchMode);
		else
			$this->bigError($sth,$problem);//*/
	}
#------------------------------------------------------------------------------------------------------------------
	/**
	 * select
	 * @param string $sql An SQL string
	 * @param array $array Paramters to bind
	 * @param constant $fetchMode A PDO Fetch mode
	 * @return mixed
	 */
	public function select($sql, $array = array(), $fetchMode = \PDO::FETCH_ASSOC)
	{
		//echo '<hr>Name class :' . __METHOD__ . '()<hr>';
		//echo '<hr><pre>'; print_r($sql) . '</pre><hr>';
		$sth = $this->prepare($sql);
		foreach ($array as $key => $value)
		{
			$sth->bindValue("$key", $value);
		}

		$sth->execute();
		$problem = $sth->errorInfo(); # semak jika ada error
		if($problem[0]=='00000')# pulangkan pembolehubah
			return $sth->fetchAll($fetchMode);
		else
			$this->bigError($sth,$problem);//*/
	}
#------------------------------------------------------------------------------------------------------------------
	/**
	 * selectJson
	 * @param string $sql An SQL string
	 * @param array $array Paramters to bind
	 * @param constant $fetchMode A PDO Fetch mode
	 * @return mixed
	 */
	public function selectJson($sql, $array = array(), $fetchMode = \PDO::FETCH_NUM)
	{
		//echo '<hr>Name class :' . __METHOD__ . '()<hr>';
		//echo '<hr><pre>'; print_r($sql) . '</pre><hr>';
		$sth = $this->prepare($sql);
		foreach ($array as $key => $value)
		{
			$sth->bindValue("$key", $value);
		}

		$sth->execute();
		$problem = $sth->errorInfo(); # semak jika ada error
		if($problem[0]=='00000')# pulangkan pembolehubah
			return $sth->fetchAll($fetchMode);
		else
			$this->bigError($sth,$problem);//*/
	}
#------------------------------------------------------------------------------------------------------------------
	/**
	 * rowCount()
	 * @param string $sql An SQL string
	 * @param array $array Paramters to bind
	 * @param constant $fetchMode A PDO Fetch mode
	 * @return mixed
	 */
	public function rowCount($sql, $array = array(), $fetchMode = \PDO::FETCH_ASSOC)
	{
		//echo '<hr>Name class :' . __METHOD__ . '()<hr>';
		//echo '<hr><pre>'; print_r($sql) . '</pre><hr>';
		$sth = $this->prepare($sql);
		foreach ($array as $key => $value)
		{
			$sth->bindValue("$key", $value);
		}

		$sth->execute();
		$problem = $sth->errorInfo(); # semak jika ada error
		if($problem[0]=='00000')# pulangkan pembolehubah
			return $sth->rowCount(); //$sth->fetchAll($fetchMode);
		else
			$this->bigError($sth,$problem);//*/
	}
#------------------------------------------------------------------------------------------------------------------
	/**
	 * insert
	 * @param string $table A name of table to insert into
	 * @param string $data An associative array
	 */
	public function insert($table, $data)
	{
		//echo '<hr>Name class :' . __METHOD__ . '()<hr>';
		ksort($data);

		$fieldNames = implode('`, `', array_keys($data));
		$fieldValues = ':' . implode(', :', array_keys($data));

		//echo $sql = "INSERT INTO $table (`$fieldNames`) VALUES ($fieldValues)";
		$sth = $this->prepare("INSERT INTO $table (`$fieldNames`) VALUES ($fieldValues)");

		foreach ($data as $key => $value)
			$sth->bindValue(":$key", $value);

		$sth->execute();
		$problem = $sth->errorInfo(); # semak jika ada error
		if($problem[0]=='00000')# pulangkan pembolehubah
			return $sth->fetchAll($fetchMode);
		else
			$this->bigError($sth,$problem);//*/
	}
#------------------------------------------------------------------------------------------------------------------
	/**
	 * insertAll
	 * @param string $sql An SQL string
	 * @param array $array Paramters to bind
	 * @param constant $fetchMode A PDO Fetch mode
	 * @return mixed
	 */
	public function insertAll($sql, $array = array(), $fetchMode = \PDO::FETCH_ASSOC)
	{
		//echo '<hr>Name class :' . __METHOD__ . '()<hr>';
		//echo '<hr><pre>'; print_r($sql) . '</pre><hr>';
		$sth = $this->prepare($sql);
		/*foreach ($array as $key => $value)
		{
			//$sth->bindValue("$key", $value);
			echo "<br>\$sth->bindValue(\"$key\", $value) ";
		}//*/

		$sth->execute();
		$problem = $sth->errorInfo(); # semak jika ada error
		if($problem[0]=='00000')# pulangkan pembolehubah
			return $sth->fetchAll($fetchMode);
		else
			$this->bigError($sth,$problem);//*/
	}
#------------------------------------------------------------------------------------------------------------------
	/**
	 * insertAllNew
	 * @param string $sql An SQL string
	 * @param array $array Paramters to bind
	 * @param constant $fetchMode A PDO Fetch mode
	 * @return mixed
	 */
	public function insertAllNew($sql, $array = array(), $fetchMode = \PDO::FETCH_ASSOC)
	{
		//echo '<hr>Name class :' . __METHOD__ . '()<hr>';
		//echo '<hr><pre>'; print_r($sql); echo '</pre><hr>';
		//echo '<hr><pre>array::'; print_r($array); echo '</pre><hr>';
		$sth = $this->prepare($sql);
		foreach ($array as $key => $value)
		{
			$sth->bindValue(":$key", (!empty($value) ? $value : NULL) );
			//echo '<hr>$sth->bindValue(":' . $key . '", ' . $value . ')';
		}	//echo '<hr>';

		$sth->execute();
		$problem = $sth->errorInfo(); # semak jika ada error
		if($problem[0]=='00000')# pulangkan pembolehubah
			return $sth->fetchAll($fetchMode);
		else
			$this->bigError($sth,$problem);//*/
	}
#------------------------------------------------------------------------------------------------------------------
	/**
	 * updateNew
	 * @param string $sql An SQL string
	 * @param array $array Paramters to bind
	 * @param constant $fetchMode A PDO Fetch mode
	 * @return mixed
	 */
	public function updateNew($sql, $array = array(), $fetchMode = \PDO::FETCH_ASSOC)
	{
		//echo '<hr>Name class :' . __METHOD__ . '()<hr>';
		//echo '<hr><pre>'; print_r($sql); echo '</pre><hr>';
		$sth = $this->prepare($sql);
		foreach ($array as $key => $value)
		{
			$sth->bindValue(":$key", (!empty($value) ? $value : NULL) );
			//echo '<hr>$sth->bindValue(":' . $key . '", ' . $value . ')';
		}	//echo '<hr>';

		$sth->execute();
		$problem = $sth->errorInfo(); # semak jika ada error
		if($problem[0]=='00000')# pulangkan pembolehubah
			return $sth->fetchAll($fetchMode);
		else
			$this->bigError($sth,$problem);//*/
	}
#------------------------------------------------------------------------------------------------------------------
	/**
	 * delete
	 * @param string $table
	 * @param string $where
	 * @param integer $limit
	 * @return integer Affected Rows
	 */
	public function delete($table, $where, $limit = 1)
	{
		//echo '<hr>Name class :' . __METHOD__ . '()<hr>';
		return $this->exec("DELETE FROM $table WHERE $where LIMIT $limit");
	}
#------------------------------------------------------------------------------------------------------------------
	public function getColumnNames($table,$database)
	{
		//echo '<hr>Name class :' . __METHOD__ . '()<hr>';
		# https://stackoverflow.com/questions/1526688/get-table-column-names-in-mysql
		$col  = 'COLUMN_NAME,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH as max,COLUMN_TYPE';
		$sql  = ' SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS';
		$sql .= ' WHERE table_schema = :database';
		$sql .= ' AND table_name = :table';

		//echo htmlentities($sql) . '<br>';

		try {
			$sth = $this->prepare($sql);
			$sth->bindValue(':database', $database, \PDO::PARAM_STR);
			$sth->bindValue(':table', $table, \PDO::PARAM_STR);
			$sth->execute();
			$output = array();
			while($row = $sth->fetch(\PDO::FETCH_ASSOC))
			{
				$output[] = $row['COLUMN_NAME'];
			}
			return $output;
		}
		catch(PDOException $pe)
		{
			trigger_error('Could not connect to MySQL database. ' . $pe->getMessage() , E_USER_ERROR);
		}
	}
#------------------------------------------------------------------------------------------------------------------
	/**
	 * selectAllMeta
	 * @param string $sql An SQL string
	 * @param array $array Paramters to bind
	 * @param constant $fetchMode A PDO Fetch mode
	 * @return mixed
	 */
	public function selectAllMeta($sql, $array = array(), $fetchMode = \PDO::FETCH_ASSOC)
	{
		//echo '<hr>Name class :' . __METHOD__ . '()<hr>';
		//echo '<hr><pre>'; print_r($sql); echo '</pre><hr>';
		$sth = $this->prepare($sql);
		foreach ($array as $key => $value)
			$sth->bindValue("$key", $value);
		$sth->execute();
		for($mula = 0; $mula < $sth->columnCount(); $mula++):
			$meta[$mula] = $sth->getColumnMeta($mula);
		endfor;

		$problem = $sth->errorInfo(); # semak jika ada error
		if($problem[0]=='00000')# pulangkan pembolehubah
			return array($sth->fetchAll($fetchMode),$meta);
		else
			$this->bigError($sth,$problem);//*/
	}
#------------------------------------------------------------------------------------------------------------------
#==================================================================================================================
}

/*
	/**
	 * entah mana mamat ini jumpa, masih tidak faham
	 * https://www.sitepoint.com/community/t/pdo-getcolumnmeta-bug/3430/3
#------------------------------------------------------------------------------------------------------------------
	/**
	 *	Automatically get column metadata
	 *
	protected function getColumnMeta($getTableName, $primaryKey)
	{
		# Clear any previous column/field info
		$this->_fields = array();
		$this->_fieldMeta = array();
		$this->_primaryKey = NULL;

		# Automatically retrieve column information if column info not specified
		if(count($this->_fields) == 0 || count($this->_fieldMeta) == 0)
		{
			# Fetch all columns and store in $this->fields
			$columns = $this->select('SHOW COLUMNS FROM ' . $getTableName);
			foreach($columns as $key => $col)
			{
				# Insert into fields array
				$colname = $col['Field'];
				$this->_fields[$colname] = $col;
				if($col['Key'] == "PRI" && empty($primaryKey))
					$this->_primaryKey = $colname;

				# Set field types
				$colType = $this->parseColumnType($col['Type']);
				$this->_fieldMeta[$colname] = $colType;
			}
		}
		return true;
	}
#------------------------------------------------------------------------------------------------------------------
	/**
	 *	Parse PDO-produced column type
	 *	[internal function]
	 *
	protected function parseColumnType($colType)
	{
		$colInfo = array();
		$colParts = explode(" ", $colType);
		if($fparen = strpos($colParts[0], "("))
		{
			$colInfo['type'] = substr($colParts[0], 0, $fparen);
			$colInfo['pdoType'] = '';
			$colInfo['length']  = str_replace(")", "", substr($colParts[0], $fparen+1));
			$colInfo['attributes'] = isset($colParts[1]) ? $colParts[1] : NULL;
		}
		else
		{
			$colInfo['type'] = $colParts[0];
		}

		# PDO Bind types
		$pdoType = '';
		foreach($this->_pdoBindTypes as $pKey => $pType)
		{
			if(strpos(' '.strtolower($colInfo['type']).' ', $pKey)) {
				$colInfo['pdoType'] = $pType;
				break;
			} else {
				$colInfo['pdoType'] = PDO::PARAM_STR;
			}
		}

		return $colInfo;
	}
#------------------------------------------------------------------------------------------------------------------
	/**
	 *	Will attempt to bind columns with datatypes based on parts of the column type name
	 *	Any part of the name below will be picked up and converted unless otherwise sepcified
	 * 	Example: 'VARCHAR' columns have 'CHAR' in them, so 'char' => PDO::PARAM_STR will convert
	 *	all columns of that type to be bound as PDO::PARAM_STR
	 *	If there is no specification for a column type, column will be bound as PDO::PARAM_STR
	 *
	protected $_pdoBindTypes = array(
		'char' => PDO::PARAM_STR,
		'int' => PDO::PARAM_INT,
		'bool' => PDO::PARAM_BOOL,
		'date' => PDO::PARAM_STR,
		'time' => PDO::PARAM_INT,
		'text' => PDO::PARAM_STR,
		'blob' => PDO::PARAM_LOB,
		'binary' => PDO::PARAM_LOB
		);
#------------------------------------------------------------------------------------------------------------------
//*/