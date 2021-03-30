<?php
/**
 * @copyright (c) 2021, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license LGPL
 */

/**
 * Generic implementation of ArgvModel
 * 
 * The idea behind Argv is that you write your own implementations of Argv to
 * tuck away parameter definitions into one class. However, this may seem to
 * much of an effort if you write a small script with only a few parameters.
 * ArgvGeneric lets you define parameters outside of your own implementation.
 */
class ArgvGeneric implements ArgvModel {
	private $namedArgs = array();
	private $booleanArgs = array();
	private $positionalArgs = array();
	private $positionalNames = array();
	
	/**
	 * Add a named argument, such as example.php --file=<filename>
	 * @param type $name
	 * @param ArgModel $arg
	 */
	public function addNamedArg($name, ArgModel $arg) {
		$this->namedArgs[$name] = $arg;
	}
	
	/**
	 * Set an array of boolean arguments which will resolve to true if used and
	 * false if not, like example.php --confirm.
	 *
	 * Note that setBooleanArgs will replace any preexisting boolean arguments.
	 * @param array $booleanArgs
	 */
	public function setBooleanArgs(array $booleanArgs) {
		$this->booleanArgs = $booleanArgs;
	}
	
	/**
	 * Add a single boolean arguments, appending to existing arguments.
	 * @param string $booleanArg
	 */
	public function addBooleanArg(string $booleanArg) {
		$this->booleanArgs[] = $booleanArg;
	}
	
	/**
	 * Add a positional argument
	 * 
	 * Add a positional argument, like "example.php filename.txt". Note that 
	 * Argv allows to mix positional, boolean and named arguments:
	 * 
	 * example.php in.txt --test --out.txt --input=latin1 --output=utf-8
	 * This behaviour may change.
	 * 
	 * @param string $name Name will be used in error messages.
	 * @param ArgModel $arg
	 */
	public function addPositionalArg(string $name, ArgModel $arg) {
		$this->positionalArgs[] = $arg;
		$this->positionalNames[] = $name;
	}
	
	public function getArgNames(): array {
		return array_keys($this->namedArgs);
	}

	public function getBoolean(): array {
		return $this->booleanArgs;
	}

	public function getNamedArg(string $name): \ArgModel {
		if(!isset($this->namedArgs[$name])) {
			throw new OutOfRangeException("named argument '".$name."' is not defined.");
		}
		return $this->namedArgs[$name];
	}

	public function getPositionalArg(int $i): \ArgModel {
		if(!isset($this->positionalArgs[$i])) {
			throw new OutOfRangeException("positional argument '".$i."' is not defined.");
		}
	return $this->positionalArgs[$i];
	}

	public function getPositionalCount(): int {
		return count($this->positionalArgs);
	}

	public function getPositionalName(int $i): string {
		if(!isset($this->positionalNames[$i])) {
			throw new OutOfRangeException("positional argument name '".$i."' is not defined.");
		}
	return $this->positionalNames[$i];
		
	}

}