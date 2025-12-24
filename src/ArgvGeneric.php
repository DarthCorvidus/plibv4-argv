<?php
/**
 * @copyright (c) 2021, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license LGPL
 */
namespace plibv4\argv;
use plibv4\uservalue\UserValue;
use OutOfRangeException;
/**
 * Generic implementation of ArgvModel
 * 
 * The idea behind Argv is that you write your own implementations of Argv to
 * tuck away parameter definitions into one class. However, this may seem to
 * much of an effort if you write a small script with only a few parameters.
 * ArgvGeneric lets you define parameters outside of your own implementation.
 */
class ArgvGeneric implements ArgvModel {
	/** @var array<string, UserValue> */
	private array $namedArgs = array();
	/** @var list<string> */
	private array $booleanArgs = array();
	/** @var list<UserValue> */
	private array $positionalArgs = array();
	/** @var list<string> */
	private array $positionalNames = array();
	
	/**
	 * Add a named argument, such as example.php --file=<filename>
	 * @param string $name
	 * @param UserValue $arg
	 */
	public function addNamedArg(string $name, UserValue $arg): void {
		$this->namedArgs[$name] = $arg;
	}
	
	/**
	 * Set an array of boolean arguments which will resolve to true if used and
	 * false if not, like example.php --confirm.
	 *
	 * Note that setBooleanArgs will replace any preexisting boolean arguments.
	 * @param list<string> $booleanArgs
	 */
	public function setBooleanArgs(array $booleanArgs): void {
		$this->booleanArgs = $booleanArgs;
	}
	
	/**
	 * Add a single boolean arguments, appending to existing arguments.
	 * @param string $booleanArg
	 */
	public function addBooleanArg(string $booleanArg): void {
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
	 * @param UserValue $arg
	 */
	public function addPositionalArg(string $name, UserValue $arg): void {
		$this->positionalArgs[] = $arg;
		$this->positionalNames[] = $name;
	}
	
	#[\Override]
	public function getArgNames(): array {
		return array_keys($this->namedArgs);
	}

	#[\Override]
	public function getBoolean(): array {
		return $this->booleanArgs;
	}

	#[\Override]
	public function getNamedArg(string $name): UserValue {
		if(!isset($this->namedArgs[$name])) {
			throw new OutOfRangeException("named argument '".$name."' is not defined.");
		}
		return $this->namedArgs[$name];
	}

	#[\Override]
	public function getPositionalArg(int $i): UserValue {
		if(!isset($this->positionalArgs[$i])) {
			throw new OutOfRangeException("positional argument '".$i."' is not defined.");
		}
	return $this->positionalArgs[$i];
	}

	#[\Override]
	public function getPositionalCount(): int {
		return count($this->positionalArgs);
	}

	#[\Override]
	public function getPositionalName(int $i): string {
		if(!isset($this->positionalNames[$i])) {
			throw new OutOfRangeException("positional argument name '".$i."' is not defined.");
		}
	return $this->positionalNames[$i];
		
	}

}