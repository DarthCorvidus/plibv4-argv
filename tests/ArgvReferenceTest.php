<?php
declare(strict_types=1);
namespace plibv4\argv;
use plibv4\uservalue\UserValue;
use PHPUnit\Framework\TestCase;
require_once __DIR__."/../example/ArgvExample.php";
class ArgvReferenceTest extends TestCase {
	function testGetReferenceLines() {
		$argv = new ArgvExample();
		$ref = new ArgvReference($argv);
		$expect[] = "Positional Arguments:";
		$expect[] = "\tArgument 1: input (mandatory)";
		$expect[] = "\tArgument 2: output (mandatory)";
		$expect[] = "Named Arguments:";
		$expect[] = "\t--time (optional)";
		$expect[] = "\t--user (mandatory)";
		$expect[] = "Boolean Arguments:";
		$expect[] = "\t--locked";
		$expect[] = "\t--test";
		$this->assertEquals(implode(PHP_EOL, $expect), $ref->getReference());
	}
	
	function testPrintPositionalMandatory() {
		$expect = [];
		$expect[] = "Positional Arguments:";
		$expect[] = "\tArgument 1: input (mandatory)";
		$expect[] = "\tArgument 2: output (mandatory)";
		$argvModel = new ArgvGeneric();
		$ref = new ArgvReference($argvModel);
		$argvModel->addPositionalArg("input", UserValue::asMandatory());
		$argvModel->addPositionalArg("output", UserValue::asMandatory());
		$this->assertEquals($expect, $ref->getReferenceLines());
	}
	
	function testPrintPositionalOptional() {
		$expect = [];
		$expect[] = "Positional Arguments:";
		$expect[] = "\tArgument 1: input (optional)";
		$expect[] = "\tArgument 2: output (optional)";
		$argvModel = new ArgvGeneric();
		$ref = new ArgvReference($argvModel);
		$argvModel->addPositionalArg("input", UserValue::asOptional());
		$argvModel->addPositionalArg("output", UserValue::asOptional());
		$this->assertEquals($expect, $ref->getReferenceLines());
	}
	
	function testPrintPositionalMixed() {
		$expect = [];
		$expect[] = "Positional Arguments:";
		$expect[] = "\tArgument 1: input (mandatory)";
		$expect[] = "\tArgument 2: output (optional)";
		$argvModel = new ArgvGeneric();
		$ref = new ArgvReference($argvModel);
		$argvModel->addPositionalArg("input", UserValue::asMandatory());
		$argvModel->addPositionalArg("output", UserValue::asOptional());
		$this->assertEquals($expect, $ref->getReferenceLines());
	}

	function testPrintNamed() {
		$expect = [];
		$expect[] = "Named Arguments:";
		$expect[] = "\t--time (optional)";
		$expect[] = "\t--user (mandatory)";
		$argvModel = new ArgvGeneric();
		$ref = new ArgvReference($argvModel);
		$argvModel->addNamedArg("time", UserValue::asOptional());
		$argvModel->addNamedArg("user", UserValue::asMandatory());
		$this->assertEquals($expect, $ref->getReferenceLines());
	}

}
