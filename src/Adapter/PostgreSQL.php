<?php

namespace Stimulsoft\Adapter;

class PostgreSQL extends \Stimulsoft\SQLAdapter
{
	private $info = null;

	private $link = null;

	public function parse($connectionString)
	{
		$connectionString = \trim($connectionString);

		$info = new \stdClass();
		$info->isPdo = false !== \mb_strpos($connectionString, 'pgsql:');
		$info->dsn = '';
		$info->host = '';
		$info->port = 5432;
		$info->database = '';
		$info->userId = '';
		$info->password = '';
		$info->charset = 'utf8';

		$parameters = \explode(';', $connectionString);

		foreach ($parameters as $parameter) {
			if (\mb_strpos($parameter, '=') < 1) {
				if ($info->isPdo) {
					$info->dsn .= $parameter . ';';
				}

				continue;
			}

			$pos = \mb_strpos($parameter, '=');
			$name = \mb_strtolower(\trim(\mb_substr($parameter, 0, $pos)));
			$value = \trim(\mb_substr($parameter, $pos + 1));

			switch ($name) {
				case 'server':
				case 'host':
				case 'location':
					$info->host = $value;

					if ($info->isPdo) {
						$info->dsn .= $parameter . ';';
					}

					break;

				case 'port':
					$info->port = $value;

					if ($info->isPdo) {
						$info->dsn .= $parameter . ';';
					}

					break;

				case 'database':
				case 'data source':
				case 'dbname':
					$info->database = $value;

					if ($info->isPdo) {
						$info->dsn .= $parameter . ';';
					}

					break;

				case 'uid':
				case 'user':
				case 'userid':
				case 'user id':
				case 'username':
					$info->userId = $value;

					break;

				case 'pwd':
				case 'password':
					$info->password = $value;

					break;

				case 'charset':
					$info->charset = $value;

					break;

				default:
					if ($info->isPdo && \mb_strlen($parameter) > 0) {
						$info->dsn .= $parameter . ';';
					}

					break;
			}
		}

		if (\mb_strlen($info->dsn) > 0 && ';' == \mb_substr($info->dsn, \mb_strlen($info->dsn) - 1)) {
			$info->dsn = \mb_substr($info->dsn, 0, \mb_strlen($info->dsn) - 1);
		}

		$this->info = $info;
	}

	public function test()
	{
		$result = $this->connect();

		if ($result->success) {
			$this->disconnect();
		}

		return $result;
	}

	public function execute($queryString)
	{
		$result = $this->connect();

		if ($result->success) {
			$query = $this->info->isPdo ? $this->link->query($queryString) : \pg_query($this->link, $queryString);

			if (! $query) {
				return $this->getLastErrorResult();
			}

			$result->types = array();
			$result->columns = array();
			$result->rows = array();

			if ($this->info->isPdo) {
				$result->count = $query->columnCount();

				for ($i = 0; $i < $result->count; $i++) {
					$meta = $query->getColumnMeta($i);
					$result->columns[] = $meta['name'];
					$result->types[] = $this->parseType($meta);
				}

				while ($rowItem = $query->fetch()) {
					$row = array();

					for ($i = 0; $i < $result->count; $i++) {
						$type = \count($result->types) >= $i + 1 ? $result->types[$i] : 'string';

						if ('array' == $type) {
							$row[] = \base64_encode($rowItem[$i]);
						} elseif ('datetime' == $type) {
							$row[] = \gmdate("Y-m-d\TH:i:s.v\Z", \strtotime($rowItem[$i]));
						} else {
							$row[] = $rowItem[$i];
						}
					}
					$result->rows[] = $row;
				}
			} else {
				$result->count = \pg_num_fields($query);

				for ($i = 0; $i < $result->count; $i++) {
					$result->columns[] = \pg_field_name($query, $i);
					$type = \pg_field_type($query, $i);
					$result->types[] = $this->parseType($type);
				}

				while ($rowItem = \pg_fetch_assoc($query)) {
					$row = array();

					foreach ($rowItem as $key => $value) {
						$type = \count($result->types) >= \count($row) + 1 ? $result->types[\count($row)] : 'string';

						if ('array' == $type) {
							$row[] = \base64_encode($value);
						} elseif ('datetime' == $type) {
							$row[] = \gmdate("Y-m-d\TH:i:s.v\Z", \strtotime($value));
						} else {
							$row[] = $value;
						}
					}
					$result->rows[] = $row;
				}
			}

			$this->disconnect();
		}

		return $result;
	}

	private function getLastErrorResult()
	{
		$code = 0;
		$message = 'Unknown';

		if ($this->info->isPdo) {
			$info = $this->link->errorInfo();
			$code = $info[0];

			if (\count($info) >= 3) {
				$message = $info[2];
			}
		} else {
			$error = \pg_last_error();

			if ($error) {
				$message = $error;
			}
		}

		if (0 == $code) {
			return \Stimulsoft\Result::error($message);
		}

		return \Stimulsoft\Result::error("[{$code}] {$message}");
	}

	private function connect()
	{
		if ($this->info->isPdo) {
			try {
				$this->link = new \PDO($this->info->dsn, $this->info->userId, $this->info->password);
			} catch (\PDOException $e) {
				$code = $e->getCode();
				$message = $e->getMessage();

				return \Stimulsoft\Result::error("[{$code}] {$message}");
			}

			return \Stimulsoft\Result::success();
		}

		if (! \function_exists('pg_connect')) {
			return \Stimulsoft\Result::error('PostgreSQL driver not found. Please configure your PHP server to work with PostgreSQL.');
		}

		$connectionString = "host='" . $this->info->host . "' port='" . $this->info->port . "' dbname='" . $this->info->database . "' user='" . $this->info->userId . "' password='" . $this->info->password . "' options='--client_encoding=" . $this->info->charset . "'";
		$this->link = \pg_connect($connectionString);

		if (! $this->link) {
			return $this->getLastErrorResult();
		}

		return \Stimulsoft\Result::success();
	}

	private function disconnect()
	{
		if (! $this->link) {
			return;
		}

		if (! $this->info->isPdo) {
			\pg_close($this->link);
		}
		$this->link = null;
	}

	private function parseType($meta)
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
