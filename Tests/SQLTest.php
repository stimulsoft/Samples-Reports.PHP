<?php

namespace Tests;

class SQLTest extends \PHPUnit\Framework\TestCase
	{

	public function testFirebird()
		{
		$connectionString = 'server=server;port=123;database=db;user=user;password=password';
		$adapter = new \Stimulsoft\Adapter\Firebird($connectionString);
		$info = $adapter->getInfo();
		$this->assertIsObject($info);
		$this->assertObjectHasAttribute('isPdo', $info);
		$this->assertFalse($info->isPdo);
		$this->assertObjectHasAttribute('host', $info);
		$this->assertEquals($info->host, 'server');
		$this->assertObjectHasAttribute('port', $info);
		$this->assertEquals($info->port, '123');
		$this->assertObjectHasAttribute('database', $info);
		$this->assertEquals($info->database, 'db');
		$this->assertObjectHasAttribute('userId', $info);
		$this->assertEquals($info->userId, 'user');
		$this->assertObjectHasAttribute('password', $info);
		$this->assertEquals($info->password, 'password');
		$this->assertObjectHasAttribute('charset', $info);
		$this->assertEquals($info->charset, 'UTF8');

		$connectionString = 'firebird:server=server;database=db;user=user;password=password';
		$adapter = new \Stimulsoft\Adapter\Firebird($connectionString);
		$info = $adapter->getInfo();
		$this->assertIsObject($info);
		$this->assertObjectHasAttribute('isPdo', $info);
		$this->assertTrue($info->isPdo);
		$this->assertObjectHasAttribute('host', $info);
		$this->assertEquals($info->host, 'server');
		$this->assertObjectHasAttribute('port', $info);
		$this->assertEquals($info->port, '3050');
		$this->assertObjectHasAttribute('database', $info);
		$this->assertEquals($info->database, 'db');
		$this->assertObjectHasAttribute('userId', $info);
		$this->assertEquals($info->userId, 'user');
		$this->assertObjectHasAttribute('password', $info);
		$this->assertEquals($info->password, 'password');
		$this->assertObjectHasAttribute('charset', $info);
		$this->assertEquals($info->charset, 'UTF8');
		$this->assertStringContainsString('firebird:', $info->dsn);
		}

	public function testMSSQL()
		{
		$connectionString = 'sqlsrv:host=server;dbname=db;uid=user;pwd=password';
		$adapter = new \Stimulsoft\Adapter\MSSQL($connectionString);
		$info = $adapter->getInfo();
		$this->assertIsObject($info);
		$this->assertObjectHasAttribute('isPdo', $info);
		$this->assertTrue($info->isPdo);
		$this->assertObjectHasAttribute('host', $info);
		$this->assertEquals($info->host, 'server');
		$this->assertObjectNotHasAttribute('port', $info);
		$this->assertObjectHasAttribute('database', $info);
		$this->assertEquals($info->database, 'db');
		$this->assertObjectHasAttribute('userId', $info);
		$this->assertEquals($info->userId, 'user');
		$this->assertObjectHasAttribute('password', $info);
		$this->assertEquals($info->password, 'password');
		$this->assertObjectHasAttribute('charset', $info);
		$this->assertEquals($info->charset, 'UTF-8');
		$this->assertObjectHasAttribute('dsn', $info);
		$this->assertStringContainsString('sqlsrv:', $info->dsn);
		}

	public function testMySQL()
		{
		$connectionString = 'mysql:host=server;dbname=db;uid=user;pwd=password';
		$adapter = new \Stimulsoft\Adapter\MySQL($connectionString);
		$info = $adapter->getInfo();
		$this->assertIsObject($info);
		$this->assertObjectHasAttribute('isPdo', $info);
		$this->assertTrue($info->isPdo);
		$this->assertObjectHasAttribute('host', $info);
		$this->assertEquals($info->host, 'server');
		$this->assertObjectHasAttribute('port', $info);
		$this->assertEquals($info->port, '3306');
		$this->assertObjectHasAttribute('database', $info);
		$this->assertEquals($info->database, 'db');
		$this->assertObjectHasAttribute('userId', $info);
		$this->assertEquals($info->userId, 'user');
		$this->assertObjectHasAttribute('password', $info);
		$this->assertEquals($info->password, 'password');
		$this->assertObjectHasAttribute('charset', $info);
		$this->assertEquals($info->charset, 'utf8');
		$this->assertObjectHasAttribute('dsn', $info);
		$this->assertStringContainsString('mysql:', $info->dsn);
		}

	public function testODBC()
		{
		$connectionString = 'odbc:host=server;dbname=db;uid=user;pwd=password';
		$adapter = new \Stimulsoft\Adapter\ODBC($connectionString);
		$info = $adapter->getInfo();
		$this->assertIsObject($info);
		$this->assertObjectHasAttribute('isPdo', $info);
		$this->assertTrue($info->isPdo);
		$this->assertObjectHasAttribute('host', $info);
		$this->assertEquals($info->host, 'server');
		$this->assertObjectNotHasAttribute('port', $info);
		$this->assertObjectHasAttribute('database', $info);
		$this->assertEquals($info->database, 'db');
		$this->assertObjectHasAttribute('userId', $info);
		$this->assertEquals($info->userId, 'user');
		$this->assertObjectHasAttribute('password', $info);
		$this->assertEquals($info->password, 'password');
		$this->assertObjectNotHasAttribute('charset', $info);
		$this->assertObjectHasAttribute('dsn', $info);
		$this->assertStringContainsString('odbc:', $info->dsn);
		}

	public function testOracle()
		{
		$connectionString = 'oci:host=server;dbname=db;uid=user;pwd=password';
		$adapter = new \Stimulsoft\Adapter\Oracle($connectionString);
		$info = $adapter->getInfo();
		$this->assertIsObject($info);
		$this->assertObjectHasAttribute('isPdo', $info);
		$this->assertTrue($info->isPdo);
		$this->assertObjectHasAttribute('host', $info);
		$this->assertEquals($info->host, 'server');
		$this->assertObjectNotHasAttribute('port', $info);
		$this->assertObjectHasAttribute('database', $info);
		$this->assertEquals($info->database, 'db');
		$this->assertObjectHasAttribute('userId', $info);
		$this->assertEquals($info->userId, 'user');
		$this->assertObjectHasAttribute('password', $info);
		$this->assertEquals($info->password, 'password');
		$this->assertObjectHasAttribute('charset', $info);
		$this->assertEquals($info->charset, 'AL32UTF8');
		$this->assertObjectHasAttribute('dsn', $info);
		$this->assertStringContainsString('oci:', $info->dsn);
		}

	public function testPostgreSQL()
		{
		$connectionString = 'pgsql:host=server;dbname=db;uid=user;pwd=password;';
		$adapter = new \Stimulsoft\Adapter\PostgreSQL($connectionString);
		$info = $adapter->getInfo();
		$this->assertIsObject($info);
		$this->assertObjectHasAttribute('isPdo', $info);
		$this->assertTrue($info->isPdo);
		$this->assertObjectHasAttribute('host', $info);
		$this->assertEquals($info->host, 'server');
		$this->assertObjectHasAttribute('port', $info);
		$this->assertEquals($info->port, '5432');
		$this->assertObjectHasAttribute('database', $info);
		$this->assertEquals($info->database, 'db');
		$this->assertObjectHasAttribute('userId', $info);
		$this->assertEquals($info->userId, 'user');
		$this->assertObjectHasAttribute('password', $info);
		$this->assertEquals($info->password, 'password');
		$this->assertObjectHasAttribute('charset', $info);
		$this->assertEquals($info->charset, 'utf8');
		$this->assertObjectHasAttribute('dsn', $info);
		$this->assertStringContainsString('pgsql:', $info->dsn);
		}

	}
