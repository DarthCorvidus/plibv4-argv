<?php
/**
 * @copyright (c) 2024, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license LGPL
 */
namespace plibv4\argv;

use \OutOfBoundsException;

final class ArgvParser {
	/** @var list<string> */
	private array $argv = [];
	/** @var list<string> */
	private array $positional = [];
	/** @var array<string, string> */
	private array $named = [];
	/** @var list<string> */
	private array $boolean = [];
	/**
	 * 
	 * @param list<string> $argv
	 */
	function __construct(array $argv) {
		$this->argv = $argv;
		$this->extractArgv();
	}
	/**
	 * Shortcut to check if flag --help was used.
	 * @return bool
	 */
	public function hasHelp(): bool {
		return in_array("help", $this->boolean);
	}
	
	private function extractArgv(): void {
		$len = count($this->argv);
		for($i = 0; $i<$len; $i++) {
			if($i === 0) {
				continue;
			}
			$stringValue = $this->argv[$i];
			if($stringValue==="--") {
				throw new ArgvException("Named argument with no name found (--)");
			}
			if(substr($stringValue, 0, 2) === "--") {
				$exp = explode("=", $stringValue, 2);
				if(count($exp) === 2) {
					$this->named[substr($exp[0], 2)] = $exp[1];
					continue;
				}
				$this->boolean[] = substr($exp[0], 2);
			continue;
			}
			$this->positional[] = $stringValue;
		}
	}

	/**
	 * returns positional values ('script php positional1') as numeric array.
	 * @return list<string>
	 */
	public function getPositionalArgs(): array {
		/** @var list<string> */
		return $this->positional;
	}
	
	/**
	 * Check if positional argument exists.
	 * @return bool 
	 */
	public function hasPositionalArg(int $i): bool {
		return isset($this->positional[$i]);
	}

	/**
	 * Get positional argument value.
	 * @param int $i 0-indexed position
	 * @return string
	 * @throws OutOfBoundsException
	 */
	public function getPositionalArg(int $i): string {
		if(!$this->hasPositionalArg($i)) {
			throw new OutOfBoundsException("Positional option {$i} does not exist.");
		}
		return $this->positional[$i];
	}

	/**
	 * Returns named values ('--name=value') as associative array.
	 * @return array<string, string>
	 */
	public function getNamedArgs(): array {
		/** @var array<string, string> */
		return $this->named;
	}

	/**
	 * Check if positional arg exists.
	 * @return bool 
	 */
	public function hasNamedArg(string $name): bool {
		return isset($this->named[$name]);
	}

	/**
	 * Get named argument value.
	 * @return string
	 * @throws OutOfBoundsException
	 */
	public function getNamedArg(string $name): string {
		if(!$this->hasNamedArg($name)) {
			throw new OutOfBoundsException("Named option --{$name} does not exist or has no value.");
		}
		return $this->named[$name];
	}

	/**
	 * Returns boolean values ('--flag') as numeric array
	 * @return list<string>
	 */
	public function getBooleanFlags(): array {
		/** @var list<string> */
		return $this->boolean;
	}

	public function hasBooleanFlag(string $key): bool {
		return in_array($key, $this->boolean, true);
	}
}