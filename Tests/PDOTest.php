<?php

namespace Tests;

class PDOTest extends \PHPUnit\Framework\TestCase
	{

	private string $database = 'Tests/data/Northwind_small.sqlite';

	public function testPDO()
		{
		$this->assertFileIsReadable($this->database, "Can't read database");
		$connectionString = 'sqlite:' . $this->database;
		$adapter = new \Tests\Fixtures\SQLite($connectionString);
		$info = $adapter->getInfo();
		$this->assertIsObject($info);
		$this->assertObjectHasAttribute('isPdo', $info);
		$this->assertTrue($info->isPdo);
		$this->assertObjectHasAttribute('dsn', $info);
		$this->assertEquals($connectionString, $info->dsn);

		$result = $adapter->execute('SELECT * FROM `Order`');
		$this->assertIsObject($result);
		$this->assertObjectHasAttribute('success', $result);
		$this->assertTrue($result->success);
		$this->assertObjectHasAttribute('types', $result);
		$this->assertObjectHasAttribute('types', $result);
		$this->assertObjectHasAttribute('columns', $result);
		$this->assertObjectHasAttribute('rows', $result);
		$this->assertIsArray($result->columns);
		$this->assertCount(14, $result->columns);
		$this->assertIsArray($result->types);
		$this->assertCount(14, $result->types);
		$this->assertIsArray($result->rows);
		$this->assertCount(830, $result->rows);
		$this->assertCount(14, $result->rows[0]);
		$this->assertCount(14, $result->rows[829]);
		}

	}
