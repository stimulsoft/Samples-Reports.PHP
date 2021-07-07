<?php

namespace Stimulsoft;

abstract class SQLAdapter
{
	protected $info = null;

	protected $link = null;

	/**
	 * Get the parsed connection string values
	 *
	 * @return object
	 */
	public function getInfo()
		{
		return $this->info;
		}

	/**
	 * Parse the connection string into the info object
	 *
	 * @param string $connectionString
	 * @param object $info set to default values, can be overriden by connectionString
	 *
	 * @return void
	 */
	public function parse($connectionString, $info)
		{
		$connectionString = \trim($connectionString);

		$type = $this->getPDOType();
		$parts = \explode(':', $connectionString);
		$info->isPdo = count($parts) == 2 && $type == $parts[0];
		if ($info->isPdo)
			{
			$connectionString = $parts[1];
			$info->dsn = $type . ':';
			}

		$parameters = \explode(';', $connectionString);

		foreach ($parameters as $parameter)
			{
			if (\mb_strpos($parameter, '=') < 1)
				{
				if ($info->isPdo)
					{
					$info->dsn .= $parameter . ';';
					}

				continue;
				}

			$pos = \mb_strpos($parameter, '=');
			$name = \mb_strtolower(\trim(\mb_substr($parameter, 0, $pos)));
			$value = \trim(\mb_substr($parameter, $pos + 1));

			switch ($name)
				{
				case 'server':
				case 'host':
				case 'location':
					$info->host = $value;

					if ($info->isPdo)
						{
						$info->dsn .= $parameter . ';';
						}

					break;

				case 'port':
					$info->port = $value;

					if ($info->isPdo)
						{
						$info->dsn .= $parameter . ';';
						}

					break;

				case 'database':
				case 'data source':
				case 'initial catalog':
				case 'dbname':
					$info->database = $value;

					if ($info->isPdo)
						{
						$info->dsn .= $parameter . ';';
						}

					break;

				case 'uid':
				case 'user':
				case 'username':
				case 'userid':
				case 'user id':
					$info->userId = $value;

					break;

				case 'pwd':
				case 'password':
					$info->password = $value;

					break;

				case 'charset':
					$info->charset = $value;

					if ($info->isPdo)
						{
						$info->dsn .= $parameter . ';';
						}

					break;

				case 'dba privilege':
				case 'privilege':
					$value = \strtolower($value);
					$info->privilege = \OCI_DEFAULT;

					if ('sysoper' == $value || 'oci_sysoper' == $value)
						{
						$info->privilege = \OCI_SYSOPER;
						}

					if ('sysdba' == $value || 'oci_sysdba' == $value)
						{
						$info->privilege = \OCI_SYSDBA;
						}

					break;

				default:
					if ($info->isPdo && \mb_strlen($parameter) > 0)
						{
						$info->dsn .= $parameter . ';';
						}

					break;
				}
			}

		if (\mb_strlen($info->dsn) > 0 && ';' == \mb_substr($info->dsn, \mb_strlen($info->dsn) - 1))
			{
			$info->dsn = \mb_substr($info->dsn, 0, \mb_strlen($info->dsn) - 1);
			}

		$this->info = $info;
		}

	public function test()
		{
		$result = $this->connect();

		if ($result->success)
			{
			$this->disconnect();
			}

		return $result;
		}

	/**
	 * Execute the query string and get results
	 *
	 * @param string $queryString SQL statement
	 *
	 * @return \Stimulsoft\Result
	 */
	public function execute($queryString)
		{
		$result = $this->connect();

		if (! $result->success)
			{
			return $result;
			}

		$query = null;
		try
			{
			$query = $this->link->query($queryString);
			}
		catch (\Throwable $e)
			{
			return $this->getLastPDOErrorResult($e);
			}
		catch (\Exception $e)
			{
			return $this->getLastPDOErrorResult($e);
			}
		if (! $query)
			{
			return $this->getLastErrorResult();
			}

		$result->types = array();
		$result->columns = array();
		$result->rows = array();

		$result->count = $query->columnCount();

		$metaData = \method_exists($query, 'getColumnMeta');

		if ($metaData)
			{
			for ($i = 0; $i < $result->count; $i++)
				{
				$meta = $query->getColumnMeta($i);
				$result->columns[] = $meta['name'];
				$result->types[] = $this->parseType($meta);
				}
			}

		while ($rowItem = $query->fetch())
			{
			$row = array();
			$index = 0;

			if ($metaData)
				{
				foreach ($rowItem as $key => $value)
					{
					if (is_string($key))
						{
						$type = isset($result->types[$index]) ? $result->types[$index] : 'string';
						$row[] = $this->getValue($value, $type);
						++$index;
						}
					}
				}
			else
				{
				foreach ($rowItem as $key => $value)
					{
					if (\is_string($key))
						{
						$index++;

						if (\count($result->columns) < $index)
							{
							$result->columns[] = $key;
							}

						if (\count($result->types) < $index)
							{
							$result->types[] = $this->detectType($value);
							}

						$type = $result->types[$index - 1];
						$row[] = $this->getValue($value, $type);
						}
					}
				}
			$result->rows[] = $row;
			}

		$this->disconnect();

		return $result;
		}

	/**
	 * Return the PDO string that identifies the database (ex: mysql, no trailing :)
	 *
	 * @return string
	 */
	abstract protected function getPDOType();

	/**
	 * Return a PDO error from caught excepton
	 *
	 * @return \Stimulsoft\Result
	 */
	protected function getLastPDOErrorResult($e)
		{
		$code = $e->getCode();
		$message = $e->getMessage();

		return \Stimulsoft\Result::error("[{$code}] {$message}");
		}

	/**
	 * Return the last PDO error message
	 *
	 * @return \Stimulsoft\Result
	 */
	protected function getLastErrorResult()
		{
		if (! $this->link)
			{
			return \Stimulsoft\Result::error('Internal Error: PDO not connected');
			}

		$error = $this->link->errorInfo();


		return \Stimulsoft\Result::error("[{$error[0]}] {$error[2]}");
		}

	/**
	 * Connect to the database.
	 *
	 * @return \Stimulsoft\Result
	 */
	protected function connect()
		{
		try
			{
			$this->link = new \PDO($this->info->dsn, $this->info->userId, $this->info->password);
			$this->link->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

			if (\in_array($this->getPDOType(), array('mysql', 'mssql')))
				{
				$this->link->query('use ' . $this->info->database);
				}
			}
		catch (\Throwable $e)
			{
			return $this->getLastPDOErrorResult($e);
			}
		catch (\Exception $e)
			{
			return $this->getLastPDOErrorResult($e);
			}

		return \Stimulsoft\Result::success();
		}

	/**
	 * Closes PDO SQL connection
	 *
	 * @return void
	 */
	protected function disconnect()
		{
		$this->link = null;
		}

	/**
	 * Trys to figure out the type of the field from the field value.
	 *
	 * @param string $value of the field
	 *
	 * @return string valid type
	 */
	protected function detectType($value)
		{
		return '';
		}

	/**
	 * return the value correctly formated for the type
	 *
	 * @param mixed $value
	 * @param string $type
	 *
	 * @return string
	 */
	protected function getValue($value, $type)
		{
		if ('array' == $type)
			{
			return \base64_encode($value);
			}
		elseif ('datetime' == $type)
			{
			return \gmdate("Y-m-d\TH:i:s.v\Z", \strtotime($value));
			}

		return $value;
		}
	}
