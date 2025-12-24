<?php
declare(strict_types=1);
namespace plibv4\argv;
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
		
		$this->assertEquals($expectedPositional, $raw->getPositional());
		$this->assertEquals($expectedNamed, $raw->getNamed());
		$this->assertEquals($expectedBoolean, $raw->getBoolean());
	}
	
	/**
	 * Tests if an empty $argv results in an empty argument array.
	 */
	function testExtractArgvEmpty() {
		$parser = new ArgvParser(array("index.php"));
		$this->assertEquals(array(), $parser->getPositional());
		$this->assertEquals(array(), $parser->getNamed());
		$this->assertEquals(array(), $parser->getBoolean());
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
		$this->assertEquals($expect, $parser->getNamed());
	}

	function testExtractArgvBoolean() {
		$argv = array("example.php", "positional01", "positional02", "--date=2021-01-01", "--funrun", "--novalue=", "--conf=/etc/example.conf");
		$expect = array();
		$expect[] = "funrun";
		$parser = new ArgvParser($argv);
		$this->assertEquals($expect, $parser->getBoolean());
	}

	function testExtractArgvPositional() {
		$argv = array("example.php", "positional01", "positional02", "--date=2021-01-01", "--funrun", "--novalue=", "--conf=/etc/example.conf", "positional03");
		$expect = array();
		$expect[0] = "positional01";
		$expect[1] = "positional02";
		$expect[2] = "positional03";
		$parser = new ArgvParser($argv);
		$this->assertEquals($expect, $parser->getPositional());
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
