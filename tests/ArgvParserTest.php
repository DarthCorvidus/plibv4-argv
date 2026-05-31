<?php
declare(strict_types=1);
namespace plibv4\argv;

use OutOfBoundsException;
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
class ArgvParserTest extends TestCase {
	/**
	 * Test to extract $argv to a raw array, keeping the position of arguments.
	 */
	function testParseArgvPositional() {
		$argv = array("example.php", "positional01", "positional02", "--date=2021-01-01", "--funrun", "--novalue=");
		$expectedPositional = array("positional01", "positional02");
		$expectedNamed = array("date"=>"2021-01-01", "novalue"=>"");
		$expectedBoolean = array("funrun");
		$raw = new ArgvParser($argv);
		
		$this->assertEquals($expectedPositional, $raw->getPositionalArgs());
		$this->assertEquals($expectedNamed, $raw->getNamedArgs());
		$this->assertEquals($expectedBoolean, $raw->getBooleanFlags());
	}
	
	/**
	 * Tests if an empty $argv results in an empty argument array.
	 */
	function testExtractArgvEmpty() {
		$parser = new ArgvParser(array("index.php"));
		$this->assertEquals(array(), $parser->getPositionalArgs());
		$this->assertEquals(array(), $parser->getNamedArgs());
		$this->assertEquals(array(), $parser->getBooleanFlags());
	}
	
	/**
	 * Test if exception is thrown if user provides a single -- without a value.
	 */
	function testExtractArgvNoName() {
		$this->expectException(ArgvException::class);
		$parser = new ArgvParser(array("index.php", "--"));
	}
	
	function testExtractArgvNamed() {
		$argv = array("example.php", "positional01", "positional02", "--date=2021-01-01", "--funrun", "--novalue=", "--conf=/etc/example.conf");
		$expect = array();
		$expect["date"] = "2021-01-01";
		$expect["novalue"] = "";
		$expect["conf"] = "/etc/example.conf";
		$parser = new ArgvParser($argv);
		$this->assertEquals($expect, $parser->getNamedArgs());
	}

	function testHasNamedArg() {
		$argv = array("example.php", "positional01", "positional02", "--date=2021-01-01", "--funrun", "--novalue=", "--conf=/etc/example.conf");
		$parser = new ArgvParser($argv);
		$this->assertEquals(true, $parser->hasNamedArg("date"));
	}

	function testGetNamedArg() {
		$argv = array("example.php", "positional01", "positional02", "--date=2021-01-01", "--funrun", "--novalue=", "--conf=/etc/example.conf");
		$parser = new ArgvParser($argv);
		$this->assertEquals("2021-01-01", $parser->getNamedArg("date"));
	}

	function testGetNamedArgNotExisting() {
		$argv = array("example.php", "positional01", "positional02", "--date=2021-01-01", "--funrun", "--novalue=", "--conf=/etc/example.conf");
		$parser = new ArgvParser($argv);
		$this->expectException(OutOfBoundsException::class);
		$this->expectExceptionMessage("Named option --bogus does not exist or has no value.");
		$parser->getNamedArg("bogus");
	}

	function testExtractArgvBoolean() {
		$argv = array("example.php", "positional01", "positional02", "--date=2021-01-01", "--funrun", "--novalue=", "--conf=/etc/example.conf");
		$expect = array();
		$expect[] = "funrun";
		$parser = new ArgvParser($argv);
		$this->assertEquals($expect, $parser->getBooleanFlags());
	}

	function testExtractArgvPositional() {
		$argv = array("example.php", "positional01", "positional02", "--date=2021-01-01", "--funrun", "--novalue=", "--conf=/etc/example.conf", "positional03");
		$expect = array();
		$expect[0] = "positional01";
		$expect[1] = "positional02";
		$expect[2] = "positional03";
		$parser = new ArgvParser($argv);
		$this->assertEquals($expect, $parser->getPositionalArgs());
	}

	function testGetPositionalArg() {
		$argv = array("example.php", "positional01", "positional02", "--date=2021-01-01", "--funrun", "--novalue=", "--conf=/etc/example.conf");
		$parser = new ArgvParser($argv);
		$this->assertEquals("positional01", $parser->getPositionalArg(0));
	}

	function testGetPositionalArgNotExisting() {
		$argv = array("example.php", "positional01", "positional02", "--date=2021-01-01", "--funrun", "--novalue=", "--conf=/etc/example.conf");
		$parser = new ArgvParser($argv);
		$this->expectException(OutOfBoundsException::class);
		$this->expectExceptionMessage("Positional option 2 does not exist.");
		$parser->getPositionalArg(2);
	}

	function testHasBooleanFlag() {
		$argv = array("example.php", "positional01", "positional02", "--date=2021-01-01", "--funrun", "--novalue=", "--conf=/etc/example.conf");
		$parser = new ArgvParser($argv);
		$this->assertEquals(true, $parser->hasBooleanFlag("funrun"));
	}

	function testDoNotHaveBooleanFlag() {
		$argv = array("example.php", "positional01", "positional02", "--date=2021-01-01", "--funrun", "--novalue=", "--conf=/etc/example.conf");
		$parser = new ArgvParser($argv);
		$this->assertEquals(false, $parser->hasBooleanFlag("exec"));
	}

	/**
	 * Checks if --help is contained within $argv.
	 */
	function testHasHelp() {
		$argv = array("example.php", "positional01", "positional02", "--date=2021-01-01", "--funrun", "--novalue=", "--conf=/etc/example.conf", "--help");
		$parser = new ArgvParser($argv);
		$this->assertEquals(true, $parser->hasHelp());
	}
	
	/**
	 * Checks if --help is not contained within $argv.
	 */
	function testHasNoHelp() {
		$argv = array("example.php", "positional01", "positional02", "--date=2021-01-01", "--funrun", "--novalue=", "--conf=/etc/example.conf");
		$parser = new ArgvParser($argv);
		$this->assertEquals(false, $parser->hasHelp());
	}
}
