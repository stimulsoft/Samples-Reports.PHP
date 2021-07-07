<?php

namespace Stimulsoft\Adapter;

class PostgreSQL extends \Stimulsoft\SQLAdapter
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
		$info->port = 5432;
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

		$query = \pg_query($this->link, $queryString);

		if (! $query)
			{
			return $this->getLastErrorResult();
			}

		$result->types = array();
		$result->columns = array();
		$result->rows = array();

		$result->count = \pg_num_fields($query);

		for ($i = 0; $i < $result->count; $i++)
			{
			$result->columns[] = \pg_field_name($query, $i);
			$type = \pg_field_type($query, $i);
			$result->types[] = $this->parseType($type);
			}

		while ($rowItem = \pg_fetch_assoc($query))
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
		return 'pgsql';
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

		$message = 'Unknown PostgreSQL error';

		$error = \pg_last_error();

		if ($error)
			{
			$message = $error;
			}

		return \Stimulsoft\Result::error($message);
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

		if (! \function_exists('pg_connect'))
			{
			return \Stimulsoft\Result::error('PostgreSQL driver not found. Please configure your PHP server to work with PostgreSQL.');
			}

		$connectionString = "host='" . $this->info->host . "' port='" . $this->info->port . "' dbname='" . $this->info->database . "' user='" . $this->info->userId . "' password='" . $this->info->password . "' options='--client_encoding=" . $this->info->charset . "'";
		$this->link = \pg_connect($connectionString);

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

		\pg_close($this->link);
		$this->link = null;
		}

	protected function parseType($meta)
	{
		$type = \strtolower($this->info->isPdo ? $meta['native_type'] : $meta);

		if ('_' == \substr($type, 0, 1)) {
			$type = 'array';
		}

		switch ($type) {
			case 'int2':
			case 'int4':
			case 'int8':
				return 'int';

			case 'float4':
			case 'float8':
			case 'numeric':
				return 'number';

			case 'bool':
				return 'boolean';

			case 'date':
			case 'time':
				return 'datetime';

			case 'bytea':
			case 'array':
				return 'array';
		}

		return 'string';
	}
}
