<?php
class ArgvParser {
	/** var list<string> */
	private array $argv = [];
	/** var list<string> */
	private array $positional = [];
	/** var array<string, string> */
	private array $named = [];
	/** var list<string> */
	private array $boolean = [];
	/**
	 * 
	 * @param list<string> $argv
	 */
	function __construct(array $argv) {
		$this->argv = $argv;
		self::extractArgv();
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
			$stringValue = (string)$this->argv[$i];
			if($stringValue==="--") {
				throw new ArgvException("Named argument with no name found (--)");
			}
			if(substr($stringValue, 0, 2)=="--") {
				$exp = explode("=", $stringValue, 2);
				if(count($exp)==2) {
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
	function getPositional(): array {
		/** @var list<string> */
		return $this->positional;
	}
	
	/**
	 * Returns named values ('--name=value') as associative array.
	 * @return array<string, string>
	 */
	function getNamed(): array {
		/** @var array<string, string> */
		return $this->named;
	}
	
	/**
	 * Returns boolean values ('--flag') as numeric array
	 * @return list<string>
	 */
	function getBoolean(): array {
		/** @var list<string> */
		return $this->boolean;
	}
	
}