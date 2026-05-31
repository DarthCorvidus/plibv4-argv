<?php
declare(strict_types=1);
namespace plibv4\argv;
use plibv4\uservalue\UserValue;
use PHPUnit\Framework\TestCase;
use OutOfRangeException;
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
final class ArgvGenericTest extends TestCase {
	function testEmptyGeneric(): void {
		$generic = new ArgvGeneric();
		$this->assertEquals($generic->getArgNames(), array());
		$this->assertEquals($generic->getBoolean(), array());
		$this->assertEquals($generic->getPositionalCount(), 0);
	}

	function testGetBooleanArgs(): void {
		$generic = new ArgvGeneric();
		$generic->setBooleanArgs(array("force", "skip"));
		$generic->addBooleanArg("test");
		$this->assertEquals($generic->getBoolean(), array("force", "skip", "test"));
	}

	function testGetArgNames(): void {
		$generic = new ArgvGeneric();
		$generic->addNamedArg("input", UserValue::asMandatory());
		$generic->addNamedArg("output", UserValue::asMandatory());
		$this->assertEquals($generic->getArgNames(), array("input", "output"));
	}
	
	function testGetNamedArg(): void {
		$generic = new ArgvGeneric();
		$generic->addNamedArg("input", UserValue::asMandatory());
		$this->assertInstanceOf(UserValue::class, $generic->getNamedArg("input"));
	}

	function testGetNamedArgUndefined(): void {
		$generic = new ArgvGeneric();
		$generic->addNamedArg("input", UserValue::asMandatory());
		$this->expectException(OutOfRangeException::class);
		$generic->getNamedArg("output");
	}
	
	function testGetPositionalArgCount(): void {
		$generic = new ArgvGeneric();
		$generic->addPositionalArg("input", UserValue::asMandatory());
		$generic->addPositionalArg("output", UserValue::asMandatory());
		$this->assertEquals(2, $generic->getPositionalCount());
	}
	
	function testGetPositionalArg(): void {
		$generic = new ArgvGeneric();
		$generic->addPositionalArg("input", UserValue::asMandatory());
		$generic->addPositionalArg("output", UserValue::asMandatory());
		$this->assertInstanceOf(UserValue::class, $generic->getPositionalArg(0));
	}
	
	function testGetPositionalName(): void {
		$generic = new ArgvGeneric();
		$generic->addPositionalArg("input", UserValue::asMandatory());
		$generic->addPositionalArg("output", UserValue::asMandatory());
		$this->assertEquals("input", $generic->getPositionalName(0));
	}
	
	function testGetPositionalArgMissing(): void {
		$generic = new ArgvGeneric();
		$generic->addPositionalArg("input", UserValue::asMandatory());
		$generic->addPositionalArg("output", UserValue::asMandatory());
		$this->expectException(OutOfRangeException::class);
		$generic->getPositionalArg(3);
	}

	function testGetPositionalNameMissing(): void {
		$generic = new ArgvGeneric();
		$generic->addPositionalArg("input", UserValue::asMandatory());
		$generic->addPositionalArg("output", UserValue::asMandatory());
		$this->expectException(OutOfRangeException::class);
		$generic->getPositionalName(3);
	}

}
