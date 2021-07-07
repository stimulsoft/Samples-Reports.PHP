<?php

namespace Stimulsoft\Adapter;

class MSSQL extends \Stimulsoft\SQLAdapter
{
	/**
	 * Construct the connection with appropriate defaults
	 *
	 * @param string $connectionString
	 */
	public function __construct($connectionString)
		{
		$info = new \stdClass();
		$info->dsn = '';
		$info->host = '';
		$info->database = '';
		$info->userId = '';
		$info->password = '';
		$info->charset = 'UTF-8';

		parent::parse($connectionString, $info);
		}

	/**
	 * @inheritDoc
	 */
	public function execute($queryString)
		{
		if ($this->info->isPdo)
			{
			return parent::execute($queryString);
			}

		$result = $this->connect();

		if (! $result->success)
			{
			return $result;
			}

		$query = \sqlsrv_query($this->link, $queryString);

		if (! $query)
			{
			return $this->getLastErrorResult();
			}

		$result->types = array();
		$result->columns = array();
		$result->rows = array();

		foreach (\sqlsrv_field_metadata($query) as $meta)
			{
			$result->columns[] = $meta['Name'];
			$result->types[] = $this->parseType($meta);
			}

		$isColumnsEmpty = 0 == \count($result->columns);

		while ($rowItem = \sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC))
			{
			$row = array();

			foreach ($rowItem as $key => $value)
				{
				if ($isColumnsEmpty && \count($result->columns) < \count($rowItem))
					{
					$result->columns[] = $key;
					}
				$type = \count($result->types) >= \count($row) + 1 ? $result->types[\count($row)] : 'string';
				$row[] = $this->getValue($value, $type);
				}
			$result->rows[] = $row;
			}

		$this->disconnect();

		return $result;
	}

	/**
	 * @inheritDoc
	 */
	protected function getPDOType()
		{
		return 'sqlsrv';
		}

	/**
	 * @inheritDoc
	 */
	protected function getLastErrorResult()
		{
		if ($this->info->isPdo)
			{
			return parent::getLastErrorResult();
			}

		$code = 0;
		$message = 'Unknown MSSQL error';

		if (($errors = \sqlsrv_errors()) != null)
			{
			$error = $errors[\count($errors) - 1];
			$code = $error['code'];
			$message = $error['message'];
			}

		if (0 == $code)
			{
			return \Stimulsoft\Result::error($message);
			}

		return \Stimulsoft\Result::error("[{$code}] {$message}");
		}

	/**
	 * @inheritDoc
	 */
	protected function connect()
		{
		if ($this->info->isPdo)
			{
			return parent::connect();
			}

		if (! \function_exists('sqlsrv_connect'))
			{
			return \Stimulsoft\Result::error('MS SQL driver not found. Please configure your PHP server to work with MS SQL.');
			}

		$this->link = \sqlsrv_connect(
			$this->info->host,
			array(
				'UID' => $this->info->userId,
				'PWD' => $this->info->password,
				'Database' => $this->info->database,
				'LoginTimeout' => 10,
				'ReturnDatesAsStrings' => true,
				'CharacterSet' => $this->info->charset
			)
		);

		if (! $this->link)
			{
			return $this->getLastErrorResult();
			}

		return \Stimulsoft\Result::success();
		}

	/**
	 * @inheritDoc
	 */
	protected function disconnect()
		{
		if (! $this->link)
			{
			return;
			}

		if ($this->info->isPdo)
			{
			parent::disconnect();

			return;
			}

		\sqlsrv_close($this->link);
		$this->link = null;
		}

	protected function parseType($meta)
	{
		$type = 'string';
		$length = 0;

		if ($this->info->isPdo)
			{
			$type = $meta['sqlsrv:decl_type'];
			$length = $meta['len'];
			}
		else
			{
			$type = $this->getStringType($meta['Type']);
			$length = $meta['Size'];
			}

		switch ($type)
			{
			case 'bigint':
			case 'int':
			case 'smallint':
			case 'tinyint':
				return 'int';

			case 'decimal':
			case 'float':
			case 'money':
			case 'numeric':
			case 'real':
			case 'smallmoney':
				return 'number';

			case 'bit':
				return 'boolean';

			case 'char':
			case 'nchar':
			case 'ntext':
			case 'nvarchar':
			case 'text':
			case 'timestamp':
			case 'uniqueidentifier':
			case 'varchar':
			case 'xml':
				return 'string';

			case 'date':
			case 'datetime':
			case 'datetime2':
			case 'datetimeoffset':
			case 'smalldatetime':
			case 'time':
				return 'datetime';

			case 'binary':
			case 'image':
			case 'sql_variant':
			case 'varbinary':
			case 'cursor':
				return 'array';
			}

		return 'string';
	}

	private function getStringType($type)
	{
		switch ($type) {
			case -6:
			case -5:
			case 4:
			case 5:
				return 'int';

			case 2:
			case 3:
			case 6:
			case 7:
				return 'decimal';

			case -7:
				return 'bit';

			case -155:
			case -154:
			case 91:
			case 93:
				return 'datetime';

			case -152:
			case -11:
			case -10:
			case -9:
			case -8:
			case -2:
			case -1:
			case 1:
			case 12:
				return 'string';

			case -151:
				return 'geometry'; // 'udt'

			case -150:
			case -4:
			case -3:
			case -2:
				return 'binary';
		}

		return 'string';
	}
}
