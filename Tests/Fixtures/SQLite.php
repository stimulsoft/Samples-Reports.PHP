<?php

namespace Tests\Fixtures;

/**
 * Sample class to test PDO functions for unit tests
 *
 * It does not implement or use the SQLite3 class
 */
class SQLite extends \Stimulsoft\SQLAdapter
{
	/**
	 * @inheritDoc
	 */
	public function __construct($connectionString)
		{
		// set defaults
		$info = new \stdClass();
		$info->dsn = '';
		$info->userId = '';
		$info->password = '';

		parent::parse($connectionString, $info);
		}

	/**
	 * @inheritDoc
	 */
	protected function getPDOType()
		{
		return 'sqlite';
		}

	protected function parseType($meta)
	{
		$type = 'string';
		$binary = false;
		$length = 0;

		foreach ($meta['flags'] as $value)
			{
			if ('blob' == $value)
				{
				$binary = true;
				}
			}
		$type = $meta['native_type'];
		$length = $meta['len'];

		$type = \strtolower($type);

		switch ($type) {
			case 'integer':
				return 'int';

			case 'double':
				return 'number';

			case 'boolean':
				if (1 == $length) {
					return 'boolean';
				}

				return 'int';

			case 'text':
				if ($binary) {
					return 'array';
				}

				return 'string';

			case 'date':
			case 'datetime':
			case 'timestamp':
				return 'datetime';

			case 'blob':
				return 'array';
		}

		return 'string';
		}

	}
