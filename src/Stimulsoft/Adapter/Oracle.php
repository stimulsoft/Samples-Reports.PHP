<?php

namespace Stimulsoft\Adapter;

class Oracle extends \Stimulsoft\SQLAdapter
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
		$info->database = '';
		$info->userId = '';
		$info->password = '';
		$info->charset = 'AL32UTF8';
		$info->privilege = '';

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

		$query = \oci_parse($this->link, $queryString);

		if (! $query || ! \oci_execute($query))
			{
			return $this->getLastErrorResult();
			}

		$result->types = array();
		$result->columns = array();
		$result->rows = array();

		$result->count = \oci_num_fields($query);

		for ($i = 1; $i <= $result->count; $i++)
			{
			$name = \oci_field_name($query, $i);
			$result->columns[] = $name;

			$type = \oci_field_type($query, $i);
			$result->types[] = $this->parseType($type);
			}

		while ($rowItem = \oci_fetch_assoc($query))
			{
			$row = array();

			foreach ($rowItem as $key => $value)
				{
				$type = $result->types[\count($row)];
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
		return 'oci';
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
		$message = 'Unknown Oracle error';

		$error = \oci_error();

		if (false !== $error)
			{
			$code = $error['code'];
			$error = $error['message'];
			}

		if ($error)
			{
			$message = $error;
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

		if (! \function_exists('oci_connect'))
			{
			return \Stimulsoft\Result::error('Oracle driver not found. Please configure your PHP server to work with Oracle.');
			}

		if ('' == $this->info->privilege)
			{
			$this->link = \oci_connect($this->info->userId, $this->info->password, $this->info->database, $this->info->charset);
			}
		else
			{
			$this->link = \oci_pconnect($this->info->userId, $this->info->password, $this->info->database, $this->info->charset, $this->info->privilege);
			}

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

		\oci_close($this->link);
		$this->link = null;
		}

	/**
	 * @inheritDoc
	 */
	protected function detectType($value)
		{
		if (\preg_match('~[^\x20-\x7E\t\r\n]~', $value) > 0)
			{
			return 'array';
			}

		if (\is_numeric($value))
			{
			if (false !== \strpos($value, '.'))
				{
				return 'number';
				}

			return 'int';
			}

		if (false !== DateTime::createFromFormat('Y-M-d', $value))
			{
			return 'datetime';
			}

		if (\is_string($value))
			{
			return 'string';
			}

		return 'array';
		}

	protected function parseType($type)
		{
		switch ($type)
			{
			case 'SMALLINT':
			case 'INTEGER':
			case 'BIGINT':
				return 'int';

			case 'NUMBER':
				return 'number';

			case 'DATE':
			case 'TIMESTAMP':
				return 'datetime';

			case 'CHAR':
			case 'VARCHAR2':
			case 'INTERVAL DAY TO SECOND':
			case 'INTERVAL YEAR TO MONTH':
			case 'ROWID':
				return 'string';

			case 'BFILE':
			case 'BLOB':
			case 'LONG':
			case 'CLOB':
			case 'RAW':
			case 100:
			case 101:
				return 'array';
			}

		return 'string';
		}
	}
