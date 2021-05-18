<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once __DIR__."/../example/ArgvExample.php";
class ArgvReferenceTest extends TestCase {
	function testPrintReference() {
		$argv = new ArgvExample();
		$ref = new ArgvReference($argv);
		$expect[] = "Positional Arguments:";
		$expect[] = "\tArgument 1: input";
		$expect[] = "\tArgument 2: output";
		$expect[] = "";
		$expect[] = "Named Arguments:";
		$expect[] = "\t--time (optional)";
		$expect[] = "\t--user (mandatory)";
		$expect[] = "";
		$expect[] = "Boolean Arguments:";
		$expect[] = "\t--locked";
		$expect[] = "\t--test";
		$expect[] = "";
		$this->assertEquals(implode(PHP_EOL, $expect), $ref->getReference());
	}
}
