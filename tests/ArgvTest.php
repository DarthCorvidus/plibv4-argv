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
class ArgvTest extends TestCase {
	/**
	 * Checks if --help is contained within $argv.
	 */
	function testHasHelp() {
		$argv = array("example.php", "--help");
		$this->assertEquals(true, Argv::hasHelp($argv));
	}
	
	/**
	 * Checks if --help is not contained within $argv.
	 */
	function testHasNoHelp() {
		$argv = array("example.php", "--test");
		$this->assertEquals(FALSE, Argv::hasHelp($argv));
	}


	/**
	 * Tests to define three boolean parameter, using them in $argv and check
	 * whether they equal TRUE. 
	 */
	function testArgBoolean() {
		$generic = new ArgvGeneric();
		$generic->addBooleanArg("first");
		$generic->addBooleanArg("second");
		$generic->addBooleanArg("third");
		$argv = new Argv(array("example.php", "--first", "--second", "--third"), $generic);
		$this->assertEquals($argv->getBoolean("first"), TRUE);
		$this->assertEquals($argv->getBoolean("second"), TRUE);
		$this->assertEquals($argv->getBoolean("third"), TRUE);
	}
	
	/**
	 * Tests to define three boolean parameters without using them in $argv,
	 * checking them to default to FALSE
	 */
	function testArgBooleanFalseIfNotUsed() {
		$generic = new ArgvGeneric();
		$generic->addBooleanArg("first");
		$generic->addBooleanArg("second");
		$generic->addBooleanArg("third");
		$argv = new Argv(array("example.php"), $generic);
		$this->assertEquals($argv->getBoolean("first"), FALSE);
		$this->assertEquals($argv->getBoolean("second"), FALSE);
		$this->assertEquals($argv->getBoolean("third"), FALSE);
	}
	
	/**
	 * Test that accessing an undefined boolean parameter results in an
	 * exception.
	 */
	function testArgBooleanUndefined() {
		$generic = new ArgvGeneric();
		$generic->addBooleanArg("first");
		$argv = new Argv(array("example.php"), $generic);
		$this->expectException(OutOfRangeException::class);
		$argv->getBoolean("second");
	}

	/**
	 * Test that an undefined boolean parameter which is mistakenly used by a
	 * script's caller results in an exception as well (therefore alerting the
	 * user of possible typos)
	 */
	function testArgBooleanUnexpected() {
		$generic = new ArgvGeneric();
		$this->expectException(ArgvException::class);
		$argv = new Argv(array("example.php", "--first"), $generic);
	}

	function testArgBooleanWithValue() {
		$generic = new ArgvGeneric();
		$generic->addBooleanArg("first");
		$this->expectException(ArgvException::class);
		$this->expectExceptionMessage("boolean parameter 'first' must not have a value");
		$argv = new Argv(array("example.php", "--first=someValue"), $generic);
	}

	/**
	 * Test to define a named argument and import it's value from $argv.
	 */
	function testNamedArg() {
		$genericArgv = new ArgvGeneric();
		$genericArgv->addNamedArg("input", UserValue::asMandatory());
		$argvImport = new Argv(array("example.php", "--input=/root/inputfile.txt"), $genericArgv);
		$this->assertEquals($argvImport->getValue("input"), "/root/inputfile.txt");
	}

	/**
	 * Some arguments are optional. If $argv does not contain them, no error is
	 * thrown when constructing Argv, and Argv::hasValue is false.
	 */
	function testOptionalNamedArg() {
		$genericArgv = new ArgvGeneric();
		$genericArgv->addNamedArg("optional", UserValue::asOptional());
		$argvImport = new Argv(array("example.php"), $genericArgv);
		$this->assertEquals($argvImport->hasValue("optional"), FALSE);
	}
	
	/**
	 * You're not supposed to access an optional argument without checking if
	 * it exists first.
	 */
	function testOptionalNamedArgAccess() {
		$genericArgv = new ArgvGeneric();
		$genericArgv->addNamedArg("optional", UserValue::asOptional());
		$argvImport = new Argv(array("example.php"), $genericArgv);
		$this->expectException(OutOfRangeException::class);
		$argvImport->getValue("optional");		
	}

	/**
	 * If a parameter is defaulted, Argv falls back to the default value if the
	 * parameter is not used on launch.
	 */
	function testOptionalDefaulted() {
		$genericArgv = new ArgvGeneric();
		$userValue = UserValue::asMandatory();
		$userValue->setValue("graceful");
		
		$genericArgv->addNamedArg("shutdown", $userValue);
		$argvImport = new Argv(array("example.php"), $genericArgv);
		$this->assertEquals("graceful", $argvImport->getValue("shutdown"));
	}

	/**
	 * @todo Decide on behaviour here. Should the user be allowed to override
	 * a default value by supplying an empty string?
	 */
	function testDefaultedEmpty() {
		$genericArgv = new ArgvGeneric();
		$userValue = UserValue::asMandatory();
		$userValue->setValue("graceful");
		
		$genericArgv->addNamedArg("shutdown", $userValue);
		$this->expectException(ArgvException::class);
		$argvImport = new Argv(array("example.php", "--shutdown="), $genericArgv);
		$this->assertEquals("", $argvImport->getValue("shutdown"));
	}

	
	/**
	 * If a parameter is defaulted, Argv is supposed to honor the parameters
	 * given at launch.
	 */
	function testOptionalDefaultOverrule() {
		$genericArgv = new ArgvGeneric();
		$userValue = UserValue::asMandatory();
		$userValue->setValue("graceful");
		
		$genericArgv->addNamedArg("shutdown", $userValue);
		$argvImport = new Argv(array("example.php", "--shutdown=hard"), $genericArgv);
		$this->assertEquals("hard", $argvImport->getValue("shutdown"));
	}

	/**
	 * Users are forced to supply mandatory parameters on program launch.
	 */
	function testNamedArgMandatoryMissing() {
		$genericArgv = new ArgvGeneric();
		$genericArgv->addNamedArg("frontend", UserValue::asMandatory());

		$this->expectException(ArgvException::class);
		$argvImport = new Argv(array("example.php"), $genericArgv);
	}

	/**
	 * Users are not allowed to sneak around the mighty programmer's will by
	 * supplying an empty argument.
	 */
	#function testNamedArgMandatoryEmpty() {
	#	$genericArgv = new ArgvGeneric();
	#	$userValue= new ArgGeneric();
	#	$genericArg->setMandatory(TRUE);
	#	$genericArgv->addNamedArg("frontend", $userValue);
	#	$this->expectException(ArgvException::class);
	#	$argvImport = new Argv(array("example.php", "--frontend="), $genericArgv);
	#}	

	/**
	 * '0' should not be treated as 'empty' [typical PHP error].
	 */
	function testNamedArgMandatoryZero() {
		$genericArgv = new ArgvGeneric();
		
		$genericArgv->addNamedArg("frontend", UserValue::asMandatory());
		$argvImport = new Argv(array("example.php", "--frontend=0"), $genericArgv);
		$this->assertEquals("0", $argvImport->getValue("frontend"));
	}
	
	/**
	 * Unexpected named arguments will throw an error.
	 */
	function testNamedArgUnexpected() {
		$genericArgv = new ArgvGeneric();
		$this->expectException(ArgvException::class);
		$argvImport = new Argv(array("example.php", "--frontend=new"), $genericArgv);
	}
	
	function testPositionalArgument() {
		$genericArgv = new ArgvGeneric();
		$genericArgv->addPositionalArg("input", UserValue::asMandatory());
		$genericArgv->addPositionalArg("output", UserValue::asMandatory());
		
		$argvImport = new Argv(array("example.php", "input.txt", "output.txt"), $genericArgv);
		$this->assertEquals("input.txt", $argvImport->getPositional(0));
		$this->assertEquals("output.txt", $argvImport->getPositional(1));
	}
	/**
	 * Positional arguments are relative: accept that a named argument is placed
	 * in between (user should be punished though, for lack of aesthetics).
	 */
	function testPositionalArgumentWithMuddledNamed() {
		$genericArgv = new ArgvGeneric();
		$genericArgv->addPositionalArg("input", UserValue::asMandatory());
		$genericArgv->addPositionalArg("output", UserValue::asMandatory());
		$genericArgv->addNamedArg("date", UserValue::asMandatory());
		$argvImport = new Argv(array("example.php", "input.txt", "--date=2020-01-01", "output.txt"), $genericArgv);
		$this->assertEquals("input.txt", $argvImport->getPositional(0));
		$this->assertEquals("output.txt", $argvImport->getPositional(1));
	}

	/**
	 * If trying to access an undefined positional argument, an exception will
	 * be thrown.
	 */
	function testPositionalArgumentUndefined() {
		$genericArgv = new ArgvGeneric();
		$genericArgv->addPositionalArg("input", UserValue::asMandatory());
		$argvImport = new Argv(array("example.php", "input.txt"), $genericArgv);
		$this->assertEquals("input.txt", $argvImport->getPositional(0));
		$this->expectException(OutOfRangeException::class);
		$this->assertEquals("output.txt", $argvImport->getPositional(1));
	}

	/**
	 * If the user supplies an unexpected positional argument, an ArgvException
	 * will be thrown.
	 */
	function testPositionalArgumentUnexpected() {
		$genericArgv = new ArgvGeneric();
		$genericArgv->addPositionalArg("input", UserValue::asMandatory());
		$this->expectException(ArgvException::class);
		new Argv(array("example.php", "input.txt", "output.txt"), $genericArgv);
	}

	function testPositionalArgumentMissing() {
		$genericArgv = new ArgvGeneric();
		$genericArgv->addPositionalArg("input", UserValue::asMandatory());
		$genericArgv->addPositionalArg("output", UserValue::asMandatory());
		$this->expectException(ArgvException::class);
		new Argv(array("example.php", "input.txt"), $genericArgv);
	}
	
	
	/**
	 * Validates for ISO date.
	 */
	function testValidateNamedPass() {
		$genericArgv = new ArgvGeneric();
		$userValue = UserValue::asMandatory();
		$userValue->setValidate(new ValidateDate(ValidateDate::ISO));
		
		$genericArgv->addNamedArg("date", $userValue);
		$argvImport = new Argv(array("example.php", "--date=2020-01-01"), $genericArgv);
		$this->assertEquals("2020-01-01", $argvImport->getValue("date"));
	}

	/**
	 * Validates for ISO date, but has wrong format as parameter.
	 */
	function testValidateNamedFail() {
		$genericArgv = new ArgvGeneric();
		$userValue = UserValue::asMandatory();
		$userValue->setValidate(new ValidateDate(ValidateDate::ISO));
		$genericArgv->addNamedArg("date", $userValue);
		
		$this->expectException(ArgvException::class);
		$argvImport = new Argv(array("example.php", "--date=01.01.2020"), $genericArgv);
	}

	/**
	 * Validates default value
	 * @Todo: this must not throw an ArgvException - this is a compile time
	 * and not a runtime exception.
	 */
	function testValidateDefaultFail() {
		$genericArgv = new ArgvGeneric();
		$userValue = UserValue::asMandatory();
		$userValue->setValidate(new ValidateDate(ValidateDate::ISO));
		$this->expectException(ValidateException::class);
		$userValue->setValue("01.01.2020");
		
		$argvImport = new Argv(array("example.php"), $genericArgv);
	}

	/**
	 * Validates for ISO date in positional parameter
	 */
	function testValidatePositionalPass() {
		$genericArgv = new ArgvGeneric();
		$userValue = UserValue::asMandatory();
		$userValue->setValidate(new ValidateDate(ValidateDate::ISO));
		
		$genericArgv->addPositionalArg("date", $userValue);
		$argvImport = new Argv(array("example.php", "2020-01-01"), $genericArgv);
		$this->assertEquals("2020-01-01", $argvImport->getPositional(0));
	}

	/**
	 * Validates for ISO date, but has wrong format as parameter.
	 */
	function testValidatePositionalFail() {
		$genericArgv = new ArgvGeneric();
		$userValue = UserValue::asMandatory();
		$userValue->setValidate(new ValidateDate(ValidateDate::ISO));
		$genericArgv->addPositionalArg("date", $userValue);
		
		$this->expectException(ArgvException::class);
		new Argv(array("example.php", "01.01.2020"), $genericArgv);
	}

	
	/**
	 * Test if a defined converter class is applied to an imported value.
	 */
	function testConvert() {
		$genericArgv = new ArgvGeneric();
		$userValue = UserValue::asMandatory();
		$userValue->setValidate(new ValidateTime());
		$userValue->setConvert(new ConvertTime(ConvertTime::HMS, ConvertTime::SECONDS));
		
		$genericArgv->addNamedArg("time", $userValue);
		$argvImport = new Argv(array("example.php", "--time=01:15:00"), $genericArgv);
		$this->assertEquals("4500", $argvImport->getValue("time"));
	}
	/**
	 * Test if a converter class is applied to a predefined default value; 
	 */
	function testConvertDefaulted() {
		$genericArgv = new ArgvGeneric();
		$userValue = UserValue::asMandatory();
		$userValue->setValidate(new ValidateTime());
		$userValue->setConvert(new ConvertTime(ConvertTime::HMS, ConvertTime::SECONDS));
		$userValue->setValue("02:00:00");
		
		$genericArgv->addNamedArg("time", $userValue);
		$argvImport = new Argv(array("example.php"), $genericArgv);
		$this->assertEquals("7200", $argvImport->getValue("time"));
	}

	function testGetNamedValues() {
		$argv = array("example.php", "positional01", "positional02", "--date=2021-01-01", "--funrun", "--novalue=", "--conf=/etc/example.conf");
		$expected["date"] = "2021-01-01";
		$expected["conf"] = "/etc/example.conf";
		$genericArgv = new ArgvGeneric();
		$genericArgv->addNamedArg("date", UserValue::asMandatory());
		$genericArgv->addNamedArg("conf", UserValue::asMandatory());
		$genericArgv->addNamedArg("novalue", UserValue::asOptional());
		$genericArgv->addBooleanArg("funrun");
		$genericArgv->addPositionalArg("input", UserValue::asMandatory());
		$genericArgv->addPositionalArg("output", UserValue::asMandatory());
		$argvImport = new Argv($argv, $genericArgv);
		$this->assertEquals($expected, $argvImport->getNamedValues());
	}

	function testGetPositionalValues() {
		$argv = array("example.php", "positional01", "positional02", "--date=2021-01-01", "--funrun", "--novalue=", "--conf=/etc/example.conf");
		$expected[0] = "positional01";
		$expected[1] = "positional02";
		$genericArgv = new ArgvGeneric();
		$genericArgv->addNamedArg("date", UserValue::asMandatory());
		$genericArgv->addNamedArg("conf", UserValue::asMandatory());
		$genericArgv->addNamedArg("novalue", UserValue::asOptional());
		$genericArgv->addBooleanArg("funrun");
		$genericArgv->addPositionalArg("input", UserValue::asMandatory());
		$genericArgv->addPositionalArg("output", UserValue::asMandatory());
		$argvImport = new Argv($argv, $genericArgv);
		$this->assertEquals($expected, $argvImport->getPositionalValues());
	}

	function testGetNamedPositionalValues() {
		$argv = array("example.php", "positional01", "positional02", "--date=2021-01-01", "--funrun", "--novalue=", "--conf=/etc/example.conf");
		$expected["input"] = "positional01";
		$expected["output"] = "positional02";
		$genericArgv = new ArgvGeneric();
		$genericArgv->addNamedArg("date", UserValue::asMandatory());
		$genericArgv->addNamedArg("conf", UserValue::asMandatory());
		$genericArgv->addNamedArg("novalue", UserValue::asOptional());
		$genericArgv->addBooleanArg("funrun");
		$genericArgv->addPositionalArg("input", UserValue::asMandatory());
		$genericArgv->addPositionalArg("output", UserValue::asMandatory());
		$argvImport = new Argv($argv, $genericArgv);
		$this->assertEquals($expected, $argvImport->getNamedPositionalValues());
	}
	
	

}
