<?php

namespace Stimulsoft\Adapter;

class Firebird extends \Stimulsoft\SQLAdapter
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
		$info->port = 3050;
		$info->database = '';
		$info->userId = '';
		$info->password = '';
		$info->charset = 'UTF8';

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

		$query = \ibase_query($this->link, $queryString);

		if (! $query)
			{
			return $this->getLastErrorResult();
			}

		$result->types = array();
		$result->columns = array();
		$result->rows = array();

		$result->count = \ibase_num_fields($query);

		for ($i = 0; $i < $result->count; $i++)
			{
			$meta = \ibase_field_info($query, $i);
			$result->columns[] = $meta['name'];
			$result->types[] = $this->parseType($meta['type']);
			}

		while ($rowItem = \ibase_fetch_assoc($query, IBASE_TEXT))
			{
			$row = array();

			foreach ($rowItem as $key => $value)
				{
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
		return 'firebird';
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

		$message = 'Unknown Firebird SQL error';

		$code = \ibase_errcode();
		$error = \ibase_errmsg();

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

		if (! \function_exists('ibase_connect'))
			{
			return \Stimulsoft\Result::error('Firebird driver not found. Please configure your PHP server to work with Firebird.');
			}

		$this->link = \ibase_connect($this->info->host . '/' . $this->info->port . ':' . $this->info->database, $this->info->userId, $this->info->password, $this->info->charset);

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

		\ibase_close($this->link);
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

		if (false !== DateTime::createFromFormat('Y-m-d H:i:s', $value) || false !== DateTime::createFromFormat('Y-m-d', $value) || false !== DateTime::createFromFormat('H:i:s', $value))
			{
			return 'datetime';
			}

		if (\is_string($value))
			{
			return 'string';
			}

		return 'array';
		}

	/**
	 * @inheritDoc
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
		elseif ('string' == $type)
			{
			return \utf8_encode($value);
			}

		return $value;
		}

	protected function parseType($type)
		{
		switch ($type) {
			case 'SMALLINT':
			case 'INTEGER':
			case 'BIGINT':
				return 'int';

			case 'FLOAT':
			case 'DOUBLE PRECISION':
			case 'NUMERIC':
			case 'DECIMAL':
				return 'number';

			case 'DATE':
			case 'TIME':
			case 'TIMESTAMP':
				return 'datetime';

			case 'CHAR':
			case 'VARCHAR':
				return 'string';

			case 'BLOB':
				return 'array';
		}

		return 'string';
		}
	}
