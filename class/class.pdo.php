<?php
/**
 * User: Leo
 * Date: 2018-04-01
 */
 
class PdoConnection
{
	protected $dsn;
	protected $user;
	protected $password;
	protected $options;

	protected $pdo;

	protected $stmt;

	private $isConnected = false;

	protected $defaultFetchMode = PDO::FETCH_ASSOC;

	/**
	 * __construct
	 * @param string $dsn
	 * @param string $user
	 * @param string $password
	 * @param array $options
	 * @return void
	 */
	public function __construct($dsn, $user, $password, $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"))
	{
		$this->dsn = $dsn;
		$this->user = $user;
		$this->password = $password;
		$this->options = $options;
		$this->pdo = new PDO($dsn, $user, $password, $options);
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->isConnected = true;
	}

	/**
	 * connect
	 * @return boolean
	 */
	public function connect()
	{
		if ( $this->isConnected ) return false;
		$this->pdo = new PDO($this->dsn, $this->user, $this->password, $this->options);
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->isConnected = true;
		return true;
	}

	/**
	 * close
	 * @return void
	 */
	public function close()
	{
		unset($this->_conn);

		$this->_isConnected = false;
	}

	/**
	 * getPdo
	 * @return PDO
	 */
	public function getPdo()
	{
		return $this->pdo;
	}

	/**
	 * getFetchMode
	 * @return integer
	 */
	public function getFetchMode()
	{
		return $this->defaultFetchMode;
	}

	/**
	 * setFetchMode
	 * @param integer $fetchMode
	 * @return void
	 */
	public function setFetchMode($fetchMode)
	{
		$this->defaultFetchMode = $fetchMode;
	}

	/**
	 * isConnected
	 * @return boolean
	 */
	public function isConnected()
	{
		return $this->isConnected;
	}

	/**
	 * errorCode
	 * @return integer
	 */
	public function errorCode()
	{
		return $this->pdo->errorCode();
	}

	/**
	 * errorInfo
	 * @return array
	 */
	public function errorInfo()
	{
		return $this->pdo->errorInfo();
	}

	/**
	 * bindTypedValues
	 * @param Statement $stmt
	 * @param array $params
	 * @param array $types
	 * @return void
	 */
	private function bindTypedValues($stmt, array $params, array $types)
	{
		// Check whether parameters are positional or named. Mixing is not allowed, just like in PDO.
		if (is_int(key($params))) {
			// Positional parameters
			$typeOffset = array_key_exists(0, $types) ? -1 : 0;
			$bindIndex = 1;
			foreach ($params as $value) {
				$typeIndex = $bindIndex + $typeOffset;
				if (isset($types[$typeIndex])) {
					$stmt->bindValue($bindIndex, $value, $types[$typeIndex]);
				} else {
					$stmt->bindValue($bindIndex, $value);
				}
				++$bindIndex;
			}
		} else {
			// Named parameters
			foreach ($params as $name => $value) {
				if (isset($types[$name])) {
					$stmt->bindValue($name, $value, $types[$name]);
				} else {
					$stmt->bindValue($name, $value);
				}
			}
		}
	}

	/**
	 * executeQuery
	 * @param string $sql
	 * @param array $params
	 * @param array $types
	 * @return Statement
	 */
	public function executeQuery($sql, array $params = array(), $types = array())
	{
		$stmt = null;

		if ($params) {
			$stmt = $this->pdo->prepare($sql);
			if ($types) {
				$this->bindTypedValues($stmt, $params, $types);
				$stmt->execute();
			} else {
				$stmt->execute($params);
			}
		} else {
			$stmt = $this->pdo->query($sql);
		}

		$stmt->setFetchMode($this->defaultFetchMode);

		return $stmt;
	}

	/**
	 * executeQuery
	 * @param string $sql
	 * @param array $params
	 * @param array $types
	 * @return Result
	 */
	public function executeUpdate($sql, array $params = array(), $types = array())
	{
		$result = null;

		if ($params) {
			$stmt = $this->pdo->prepare($sql);
			if ($types) {
				$this->bindTypedValues($stmt, $params, $types);
				$stmt->execute();
			} else {
				$stmt->execute($params);
			}
			$result = $stmt->rowCount();
		} else {
			$result = $this->pdo->exec($sql);
		}

		return $result;
	}

	/**
	 * fetchAll
	 * @param string $sql
	 * @param array $params
	 * @param array $types
	 * @return array
	 */
	public function fetchAll($sql, array $params = array(), $types = array())
	{
		return $this->executeQuery($sql, $params, $types)->fetchAll();
	}

	/**
	 * fetchRow
	 * @param string $sql
	 * @param array $params
	 * @return array
	 */
	public function fetchRow($sql, array $params = array())
	{
		return $this->executeQuery($sql, $params)->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * fetchAssoc
	 * @param string $sql
	 * @param array $params
	 * @return array
	 */
	public function fetchAssoc($sql, array $params = array())
	{
		return $this->executeQuery($sql, $params)->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * fetchArray
	 * @param string $sql
	 * @param array $params
	 * @return array
	 */
	public function fetchArray($sql, array $params = array())
	{
		return $this->executeQuery($sql, $params)->fetch(PDO::FETCH_NUM);
	}

	/**
	 * fetchColumn
	 * @param string $sql
	 * @param array $params
	 * @param integer $colnum
	 * @return array
	 */
	public function fetchColumn($sql, array $params = array(), $colnum = 0)
	{
		return $this->executeQuery($sql, $params)->fetchColumn($colnum);
	}

	/**
	 * fetchPairs
	 * @param string $sql
	 * @param array $params
	 * @return array
	 */
	public function fetchPairs($sql, array $params = array())
	{
		$data = array();
		$stmt = $this->executeQuery($sql, $params);
		while ( $row = $stmt->fetch(PDO::FETCH_NUM) ) {
			$data[$row[0]] = $row[1];
		}
		return $data;
	}

	/**
	 * prepare
	 * @param string $sql
	 * @return Statement
	 */
	public function prepare($sql)
	{
		$stmt = $this->pdo->prepare($sql);
		$stmt->setFetchMode($this->defaultFetchMode);
		return $stmt;
	}

	/**
	 * query
	 * @param string $sql
	 * @return Statement
	 */
	public function query($sql)
	{
		$this->stmt = $this->pdo->query($sql);
		$this->stmt->setFetchMode($this->defaultFetchMode);
		return $this->stmt;
	}

	/**
	 * execute
	 * @param array $params
	 * @return Statement
	 */
	public function execute(array $params = array())
	{
		if ( $params ) {
			return $this->stmt->execute($params);
		} else {
			return $this->stmt->execute();
		}
	}

	/**
	 * rowCount
	 * @return integer
	 */
	public function rowCount()
	{
		return $this->stmt->rowCount();
	}

	/**
	 * exec
	 * @param string $sql
	 * @return Statement
	 */
	public function exec($sql)
	{
		return $this->pdo->exec($sql);
	}

	/**
	 * extractTypeValues
	 * @param array $data
	 * @param array $types
	 * @return array
	 */
	private function extractTypeValues(array $data, array $types)
	{
		$typeValues = array();

		foreach ($data as $k => $_) {
			$typeValues[] = isset($types[$k])
				? $types[$k]
				: PDO::PARAM_STR;
		}

		return $typeValues;
	}

	/**
	 * Inserts a table row with specified data.
	 *
	 * @param string $tableName The name of the table to insert data into.
	 * @param array  $data	  An associative array containing column-value pairs.
	 * @param array  $types	 Types of the inserted data.
	 *
	 * @return integer The number of affected rows.
	 */
	public function insert($tableName, array $data, array $types = array())
	{
		if (empty($data)) {
			return $this->executeUpdate('INSERT INTO ' . $tableName . ' ()' . ' VALUES ()');
		}

		return $this->executeUpdate(
			'INSERT INTO ' . $tableName . ' (' . implode(', ', array_keys($data)) . ')' .
			' VALUES (' . implode(', ', array_fill(0, count($data), '?')) . ')',
			array_values($data),
			is_int(key($types)) ? $types : $this->extractTypeValues($data, $types)
		);
	}

	/**
	 * Executes an SQL UPDATE statement on a table.
	 *
	 * @param string $tableName  The name of the table to update.
	 * @param array  $data	   An associative array containing column-value pairs.
	 * @param array  $identifier The update criteria. An associative array containing column-value pairs.
	 * @param array  $types	  Types of the merged $data and $identifier arrays in that order.
	 *
	 * @return integer The number of affected rows.
	 */
	public function update($tableName, array $data, array $identifier, array $types = array())
	{
		$set = array();

		foreach ($data as $columnName => $value) {
			$set[] = $columnName . ' = ?';
		}

		if ( ! is_int(key($types))) {
			$types = $this->extractTypeValues(array_merge($data, $identifier), $types);
		}

		$params = array_merge(array_values($data), array_values($identifier));

		$sql  = 'UPDATE ' . $tableName . ' SET ' . implode(', ', $set)
				. ' WHERE ' . implode(' = ? AND ', array_keys($identifier))
				. ' = ?';

		return $this->executeUpdate($sql, $params, $types);
	}

	/**
	 * Executes an SQL DELETE statement on a table.
	 *
	 * @param string $tableName  The name of the table on which to delete.
	 * @param array  $identifier The deletion criteria. An associative array containing column-value pairs.
	 * @param array  $types	  The types of identifiers.
	 *
	 * @return integer The number of affected rows.
	 */
	public function delete($tableName, array $identifier, array $types = array())
	{
		 $criteria = array();

		foreach (array_keys($identifier) as $columnName) {
			$criteria[] = $columnName . ' = ?';
		}

		if ( ! is_int(key($types))) {
			$types = $this->extractTypeValues($identifier, $types);
		}

		$query = 'DELETE FROM ' . $tableName . ' WHERE ' . implode(' AND ', $criteria);

		return $this->executeUpdate($query, array_values($identifier), $types);
	}

	/**
	 * lastInsertId
	 * @param string $seqName
	 * @return integer
	 */
	public function lastInsertId($seqName = null)
	{
		return $this->pdo->lastInsertId($seqName);
	}

	/**
	 * quote
	 * @param string
	 * @return string
	 */
	public function quote($string)
	{
		return $this->pdo->quote($string);
	}

	/**
	 * escape_string
	 * @param string
	 * @return string
	 */
	public function escape_string($string)
	{
		return $this->pdo->quote($string);
	}

}
