<?php

namespace Stimulsoft\Adapter;

class ODBC extends \Stimulsoft\SQLAdapter
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
		$info->userId = '';
		$info->password = '';

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

		$query = \odbc_exec($this->link, $queryString);

		if (! $query)
			{
			return $this->getLastErrorResult();
			}

		$result->types = array();
		$result->columns = array();
		$result->rows = array();

		$result->count = \odbc_num_fields($query);

		for ($i = 1; $i <= $result->count; $i++)
			{
			$type = \odbc_field_type($query, $i);
			$result->types[] = $this->parseType($type);
			$result->columns[] = \odbc_field_name($query, $i);
			}

		while (\odbc_fetch_row($query))
			{
			$row = array();

			for ($i = 1; $i <= $result->count; $i++)
				{
				$type = $result->types[$i - 1];
				$value = \odbc_result($query, $i);
				$row[] = $this->getValue($value, $type);
				}

			$result->rows[] = $row;
		}

		\odbc_free_result($query);
		$this->disconnect();

		return $result;
		}

	/**
	 * @inheritDoc
	 */
	protected function getPDOType()
		{
		return 'odbc';
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

		$code = \odbc_error($result);
		$message = \odbc_errormsg($result);

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

		if (! \function_exists('odbc_connect'))
			{
			return \Stimulsoft\Result::error('ODBC driver not found. Please configure your PHP server to work with ODBC.');
			}

		$this->link = \odbc_connect($this->info->dsn, $this->info->userId, $this->info->password);

		if (! $this->link)
			{
			return $this->getLastErrorResult(null);
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

		\odbc_close($this->link);
		$this->link = null;
		}

	protected function parseType($type)
	{
		$type = \strtolower($type);

		switch ($type) {
			case 'short':
			case 'int':
			case 'int2':
			case 'int4':
			case 'int8':
			case 'int24':
			case 'integer':
			case 'long':
			case 'longlong':
			case 'smallint':
			case 'bigint':
			case 'tinyint':
			case 'byte':
			case 'counter':
				return 'int';

			case 'bit':
				return 'boolean';

			case 'float':
			case 'float4':
			case 'float8':
			case 'double':
			case 'decimal':
			case 'newdecimal':
			case 'money':
			case 'numeric':
			case 'real':
			case 'smallmoney':
			case 'currency':
				return 'number';

			case 'string':
			case 'var_string':
			case 'char':
			case 'nchar':
			case 'ntext':
			case 'varchar':
			case 'nvarchar':
			case 'text':
			case 'uniqueidentifier':
			case 'xml':
				return 'string';

			case 'date':
			case 'datetime':
			case 'datetime2':
			case 'datetimeoffset':
			case 'smalldatetime':
			case 'time':
			case 'timestamp':
			case 'year':
				return 'datetime';

			case 'blob':
			case 'geometry':
			case 'binary':
			case 'image':
			case 'sql_variant':
			case 'varbinary':
			case 'longbinary':
			case 'cursor':
			case 'bytea':
				return 'array';
		}

		return 'string';
	}
}
