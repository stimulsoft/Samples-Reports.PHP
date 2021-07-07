<?php

namespace Stimulsoft\Adapter;

class MySQL extends \Stimulsoft\SQLAdapter
{
	/**
	 * Construct the connection with appropriate defaults
	 *
	 * @param string $connectionString
	 */
	public function __construct($connectionString)
		{
		// set defaults
		$info = new \stdClass();
		$info->dsn = '';
		$info->host = '';
		$info->port = 3306;
		$info->database = '';
		$info->userId = '';
		$info->password = '';
		$info->charset = 'utf8';

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

		$query = $this->link->query($queryString);

		$result->types = array();
		$result->columns = array();
		$result->rows = array();

		$result->count = $query->field_count;

		while ($meta = $query->fetch_field())
			{
			$result->columns[] = $meta->name;
			$result->types[] = $this->parseType($meta);
			}

		if ($query->num_rows > 0)
			{
			$isColumnsEmpty = 0 == \count($result->columns);

			while ($rowItem = $query->fetch_assoc())
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
			}

		$this->disconnect();

		return $result;
		}

	/**
	 * @inheritDoc
	 */
	protected function getPDOType()
		{
		return 'mysql';
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
		$message = 'Unknown MySQL error';

		$code = $this->link->errno;

		if ($this->link->error)
			{
			$message = $this->link->error;
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

		$this->link = new \mysqli($this->info->host, $this->info->userId, $this->info->password, $this->info->database, $this->info->port);

		if ($this->link->connect_error)
			{
			return \Stimulsoft\Result::error("[{$this->link->connect_errno}] {$this->link->connect_error}");
			}

		if (! $this->link->set_charset($this->info->charset))
			{
			return $this->getLastErrorResult();
			}

		$this->link->query('use ' . $this->info->database);

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

		$this->link->close();
		$this->link = null;
		}

	private function getStringType($type)
	{
		switch ($type)
			{
			case 1:
				return 'tiny';

			case 2:
			case 3:
			case 8:
			case 9:
				return 'int';

			case 16:
				return 'bit';

			case 4:
			case 5:
			case 246:
				return 'decimal';

			case 7:
			case 10:
			case 11:
			case 12:
			case 13:
				return 'datetime';

			case 252:
			case 253:
				return 'string';

			case 254:
			case 255:
				return 'blob';
			}

		return 'string';
	}

	protected function parseType($meta)
	{
		$type = 'string';
		$binary = false;
		$length = 0;

		if ($this->info->isPdo) {
			foreach ($meta['flags'] as $value) {
				if ('blob' == $value) {
					$binary = true;
				}
			}
			$type = $meta['native_type'];
			$length = $meta['len'];
		} else {
			if ($meta->flags & 128) {
				$binary = true;
			}
			$type = $this->getStringType($meta->type);
			$length = $meta->length;
		}

		$type = \strtolower($type);

		switch ($type) {
			case 'short':
			case 'int':
			case 'int24':
			case 'long':
			case 'longlong':
			case 'bit':
				return 'int';

			case 'newdecimal':
			case 'float':
			case 'double':
				return 'number';

			case 'tiny':
				if (1 == $length) {
					return 'boolean';
				}

				return 'int';

			case 'string':
			case 'var_string':
				if ($binary) {
					return 'array';
				}

				return 'string';

			case 'date':
			case 'datetime':
			case 'timestamp':
			case 'time':
			case 'year':
				return 'datetime';

			case 'blob':
			case 'geometry':
				return 'array';
		}

		return 'string';
	}
}
