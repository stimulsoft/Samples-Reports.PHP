<?php

namespace Stimulsoft;

abstract class SQLAdapter
{
	abstract public function parse($connectionString);

	abstract public function test();

	abstract public function execute($queryString);
}
