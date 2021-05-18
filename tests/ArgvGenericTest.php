<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AssertTest
 *
 * @author hm
 */
class ArgvGenericTest extends TestCase {
	function testEmptyGeneric() {
		$generic = new ArgvGeneric();
		$this->assertEquals($generic->getArgNames(), array());
		$this->assertEquals($generic->getBoolean(), array());
		$this->assertEquals($generic->getPositionalCount(), 0);
	}

	function testGetBooleanArgs() {
		$generic = new ArgvGeneric();
		$generic->setBooleanArgs(array("force", "skip"));
		$generic->addBooleanArg("test");
		$this->assertEquals($generic->getBoolean(), array("force", "skip", "test"));
	}

	function testGetArgNames() {
		$generic = new ArgvGeneric();
		$generic->addNamedArg("input", UserValue::asMandatory());
		$generic->addNamedArg("output", UserValue::asMandatory());
		$this->assertEquals($generic->getArgNames(), array("input", "output"));
	}
	
	function testGetNamedArg() {
		$generic = new ArgvGeneric();
		$generic->addNamedArg("input", UserValue::asMandatory());
		$this->assertInstanceOf(UserValue::class, $generic->getNamedArg("input"));
	}

	function testGetNamedArgUndefined() {
		$generic = new ArgvGeneric();
		$generic->addNamedArg("input", UserValue::asMandatory());
		$this->expectException(OutOfRangeException::class);
		$generic->getNamedArg("output");
	}
	
	function testGetPositionalArgCount() {
		$generic = new ArgvGeneric();
		$generic->addPositionalArg("input", UserValue::asMandatory());
		$generic->addPositionalArg("output", UserValue::asMandatory());
		$this->assertEquals(2, $generic->getPositionalCount());
	}
	
	function testGetPositionalArg() {
		$generic = new ArgvGeneric();
		$generic->addPositionalArg("input", UserValue::asMandatory());
		$generic->addPositionalArg("output", UserValue::asMandatory());
		$this->assertInstanceOf(UserValue::class, $generic->getPositionalArg(0));
	}
	
	function testGetPositionalName() {
		$generic = new ArgvGeneric();
		$generic->addPositionalArg("input", UserValue::asMandatory());
		$generic->addPositionalArg("output", UserValue::asMandatory());
		$this->assertEquals("input", $generic->getPositionalName(0));
	}
	
	function testGetPositionalArgMissing() {
		$generic = new ArgvGeneric();
		$generic->addPositionalArg("input", UserValue::asMandatory());
		$generic->addPositionalArg("output", UserValue::asMandatory());
		$this->expectException(OutOfRangeException::class);
		$generic->getPositionalArg(3);
	}

	function testGetPositionalNameMissing() {
		$generic = new ArgvGeneric();
		$generic->addPositionalArg("input", UserValue::asMandatory());
		$generic->addPositionalArg("output", UserValue::asMandatory());
		$this->expectException(OutOfRangeException::class);
		$generic->getPositionalName(3);
	}

}
