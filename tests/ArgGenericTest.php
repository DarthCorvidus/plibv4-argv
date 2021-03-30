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
class ArgGenericTest extends TestCase {
	function testEmptyGeneric() {
		$generic = new ArgGeneric();
		$this->assertEquals($generic->hasConvert(), FALSE);
		$this->assertEquals($generic->hasDefault(), FALSE);
		$this->assertEquals($generic->hasValidate(), FALSE);
		$this->assertEquals($generic->isMandatory(), FALSE);
	}

	function testFullGeneric() {
		$generic = new ArgGeneric();
		$generic->setConvert(new ConvertDate(ConvertDate::ISO, ConvertDate::GERMAN));
		$generic->setValidate(new ValidateDate(ValidateDate::ISO));
		$generic->setDefault("2020-01-01");
		// To combine setDefault with mandatory is actually unnecessary, as a
		// defaulted argument can never trip over a missing parameter - the
		// default will just be used instead.
		$generic->setMandatory(true);

		$this->assertEquals($generic->hasConvert(), TRUE);
		$this->assertEquals($generic->hasDefault(), TRUE);
		$this->assertEquals($generic->hasValidate(), TRUE);
		$this->assertEquals($generic->isMandatory(), TRUE);
	}
	
	function testGetConvert() {
		$generic = new ArgGeneric();
		$generic->setConvert(new ConvertDate(ConvertDate::ISO, ConvertDate::GERMAN));
		$this->assertInstanceOf(ConvertDate::class, $generic->getConvert());
	}
	
	
	
	function testGetConvertMissing() {
		$generic = new ArgGeneric();
		$this->expectException(TypeError::class);
		$generic->getConvert();
	}
	
	function testGetValidate() {
		$generic = new ArgGeneric();
		$generic->setValidate(new ValidateDate(ValidateDate::ISO));
		$this->assertInstanceOf(ValidateDate::class, $generic->getValidate());
	}
	
	
	
	function testGetValidateMissing() {
		$generic = new ArgGeneric();
		$this->expectException(TypeError::class);
		$generic->getValidate();
	}

	function testGetDefault() {
		$generic = new ArgGeneric();
		$generic->setDefault("2020-01-01");
		$this->assertEquals("2020-01-01", $generic->getDefault());
	}

	function testHasDefault() {
		$generic = new ArgGeneric();
		$generic->setDefault("2020-01-01");
		$this->assertEquals(TRUE, $generic->hasDefault());
	}
	
	function testGetDefaultMissing() {
		$generic = new ArgGeneric();
		$this->expectException(TypeError::class);
		$generic->getDefault();
	}
	
	function testGetMandatoryTrue() {
		$generic = new ArgGeneric();
		$generic->setMandatory(TRUE);
		$this->assertEquals(TRUE, $generic->isMandatory());
	}

	function testGetMandatoryFalse() {
		$generic = new ArgGeneric();
		$this->assertEquals(FALSE, $generic->isMandatory());
	}

	function testGetMandatoryDefault() {
		$generic = new ArgGeneric();
		$this->assertEquals(FALSE, $generic->isMandatory());
	}
}
