<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license LGPL
 */

/**
 * Argv extracts parameters from $argv, as defined in an ArgvModel. As of yet,
 * it only handles long parameters, such as --param, both with or without a
 * value.<br />
 * Argv does basic plausibility checks; it throws Exceptions if boolean
 * parameters are used with a value and vice versa or if unknown parameters are
 * used.
 */
class Argv {
	private ArgvModel $model;
	private array $argv;
	private array $availablePositional = array();
	private array $availableNamed = array();
	private array $availableBoolean = array();
	const X_ALL = 1;
	const X_NAMED = 2;
	const X_BOOL = 3;
	const X_POS = 4;
	function __construct(array $argv, ArgvModel $model) {
		$this->model = $model;
		$this->argv = $argv;
		$this->import();
		#$this->getAvailable();
		#$this->sanityCheck();
		#$this->validate();
		#$this->convert();
	}
	
	private function import(): void {
		$this->availableNamed = $this->importNamed();
		$this->availableBoolean = $this->importBoolean();
		$this->availablePositional = $this->importPositional();
	}
	
	private function importBoolean(): array {
		$extract = $this->extractArgv($this->argv, self::X_BOOL);
		$result = array();
		foreach($this->model->getBoolean() as $value) {
			if(isset($extract[$value])) {
				$result[] = $value;
				continue;
			}
			/**
			 * Psalm is not quite correct here; I suppose that it expects 'else',
			 * but I dislike else.
			 * @psalm-suppress RedundantCondition
			 */
			if(!isset($extract[$value])) {
				continue;
			}
			throw new ArgvException("boolean argument must not have a value");
		}
		foreach(array_keys($extract) as $key) {
			if(!in_array($key, $this->model->getBoolean())) {
				throw new ArgvException("unexpected boolean parameter '".$key."'");
			}
		}
	return $result;
	}
	
	private function importNamed(): array {
		$extract = $this->extractArgv($this->argv, self::X_NAMED);
		$result = array();
		foreach($this->model->getArgNames() as $value) {
			$uservalue = $this->model->getNamedArg($value);
			try {
				if(isset($extract[$value])) {
					$uservalue->setValue($extract[$value]);
				}
				if($uservalue->getValue()!=="") {
					$result[$value] = $uservalue->getValue();
				}
			} catch (MandatoryException $e) {
				throw new ArgvException("--".$value.": ".$e->getMessage());
			} catch (ValidateException $e) {
				throw new ArgvException("--".$value.": ".$e->getMessage());
			}
		}
		foreach(array_keys($extract) as $key) {
			if(!in_array($key, $this->model->getArgNames())) {
				throw new ArgvException("unexpected named parameter '".$key."'");
			}
		}
	return $result;
	}
	
	private function importPositional(): array {
		$extract = $this->extractArgv($this->argv, self::X_POS);
		$result = array();
		for($i=0;$i<$this->model->getPositionalCount();$i++) {
			$uservalue = $this->model->getPositionalArg($i);
			try {
				if(isset($extract[$i])) {
					$uservalue->setValue($extract[$i]);
				}
				if($uservalue->getValue()!=="") {
					$result[$i] = $uservalue->getValue();
				}
			} catch (MandatoryException $e) {
				throw new ArgvException($e->getMessage());
			} catch (ValidateException $e) {
				throw new ArgvException($e->getMessage());
			}

		}
		if(count($extract)>$this->model->getPositionalCount()) {
			throw new ArgvException("Unexpected positional argument ".($this->model->getPositionalCount()+1));
		}

	return $result;
	}
	
	/**
	 * Parses $argv and returns array
	 * 
	 * Argv::extractArgv extracts array from $argv, keeping the order of
	 * arguments. Positional arguments will have a numeric index, boolean and
	 * named arguments a associative index.
	 * Note that this does no sanity checks whatsoever beside malformed
	 * arguments.
	 * @param array $argv
	 * @return array
	 * @throws ArgvException
	 */
	static function extractArgv(array $argv, int $filter = self::X_ALL): array {
		$raw = array();
		$pos = array();
		$bool = array();
		$named = array();
		unset($argv[0]);
		foreach($argv as $value) {
			if($value==="--") {
				throw new ArgvException("Named argument with no name found (--)");
			}
			if(substr($value, 0, 2)=="--") {
				$exp = explode("=", $value, 2);
				if(count($exp)==2) {
					$raw[substr($exp[0], 2)] = $exp[1];
					$named[substr($exp[0], 2)] = $exp[1];
					continue;
				}
				$raw[substr($exp[0], 2)] = true;
				$bool[substr($exp[0], 2)] = true;
			continue;
			}
			$raw[] = $value;
			$pos[] = $value;
		}
		if($filter==self::X_BOOL) {
			return $bool;
		}
		
		if($filter==self::X_NAMED) {
			return $named;
		}
		
		if($filter==self::X_POS) {
			return $pos;
		}
	return $raw;
	}
	
	/**
	 * Check for --help
	 * 
	 * This function checks whether boolean argument --help is present within
	 * a call. This allows you to exit your script early with an online help.
	 * @param array $argv
	 * @return bool
	 */
	static function hasHelp(array $argv): bool {
		$extract = self::extractArgv($argv);
	return isset($extract["help"]) && $extract["help"]===TRUE;
	}
	
	/**
	 * Checks whether a certain parameter is available or not. A parameter is
	 * available if it was used by the calling user or if it's ArgModel has a
	 * default value.
	 * @param string $key
	 * @return bool
	 */
	function hasValue($key): bool {
		return isset($this->availableNamed[$key]);
	}
	/**
	 * Gets the value of a specific parameter. Note that parameters which are
	 * not available will throw an exception, so hasValue should be called
	 * beforehand if a value is not mandatory and has no default value.
	 * 
	 * @param string $key
	 * @return string
	 * @throws Exception
	 */
	function getValue($key): string {
		if(!$this->hasValue($key)) {
			throw new OutOfRangeException("argument value ".$key." doesn't exist");
		}
	return $this->availableNamed[$key];
	}
	
	function hasPositional(int $pos): bool {
		return isset($this->availablePositional[$pos]);
	}
	
	function getPositional(int $pos): string {
		if(!$this->hasPositional($pos)) {
			throw new OutOfRangeException("positional argument ".$pos." doesn't exist");
		}
	return $this->availablePositional[$pos];
	}
	
	/**
	 * getBoolean will evaluate to true if a parameter was set (like --force),
	 * and to false, if it was not set. It will throw an Exception if it was
	 * not defined in an instance of ArgvModel.
	 * @param string $key
	 * @return bool
	 * @throws Exception
	 */
	function getBoolean($key):bool {
		if(!in_array($key, $this->model->getBoolean())) {
			throw new OutOfRangeException("boolean argument ".$key." is not defined");
		}
		return in_array($key, $this->availableBoolean);
	}
	
	/**
	 * Get all named values as array.
	 * @return array
	 */
	function getNamedValues(): array {
		return $this->availableNamed;
	}
	
	/**
	 * Get all positional values as array (numerical index)
	 * @return array
	 */
	function getPositionalValues(): array {
		return $this->availablePositional;
	}
	
	/**
	 * Get all positional values as array (associative index)
	 * @return array
	 */
	function getNamedPositionalValues(): array {
		$named = array();
		foreach($this->availablePositional as $key => $value) {
			$named[$this->model->getPositionalName($key)] = $value;
		}
	return $named;
	}
}
