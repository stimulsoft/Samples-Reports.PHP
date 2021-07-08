<?php

namespace Tests\Unit;

class SyntaxTest extends \PHPFUI\PHPUnitSyntaxCoverage\Extensions
	{
	public function testDirectory() : void
		{
		$this->assertValidPHPDirectory(__DIR__ . '/../src', 'src directory has an error', true);
		}
	}
