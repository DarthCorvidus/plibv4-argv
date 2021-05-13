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
	private $model;
	private $argv;
	private $availablePositional = array();
	private $availableNamed = array();
	private $availableBoolean = array();
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
	
	private function import() {
		#$this->availableNamed = $this->importNamed();
		$this->availableNamed = $this->importNamed();
		$this->availableBoolean = $this->importBoolean();
		$this->availablePositional = $this->importPositional();
		#var_dump($this->availableBoolean);
		#print_r($this->availableBoolean);
	}
	
	private function importBoolean(): array {
		$extract = $this->extractArgv($this->argv, self::X_BOOL);
		$result = array();
		foreach($this->model->getBoolean() as $value) {
			if(isset($extract[$value])) {
				$result[] = $value;
				continue;
			}
			if(!isset($extract[$value])) {
				continue;
			}
			throw new ArgvException("boolean argument must not have a value");
		}
		foreach($extract as $key => $value) {
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
			if(isset($extract[$value])) {
				$uservalue->setValue($extract[$value]);
			}
			if($uservalue->getValue()!=="") {
				$result[$value] = $uservalue->getValue();
			}
		}
		foreach($extract as $key => $value) {
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
			if(isset($extract[$i])) {
				$uservalue->setValue($extract[$i]);
			}
			if($uservalue->getValue()!=="") {
				$result[$i] = $uservalue->getValue();
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
	static function extractArgv(array $argv, $filter = self::X_ALL): array {
		$raw = array();
		$pos = array();
		$bool = array();
		$named = array();
		unset($argv[0]);
		foreach($argv as $key => $value) {
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
	
	private function getAvailable() {
		foreach($this->argv as $key => $value) {
			if($value==="--") {
				throw new ArgvException("Parameter with no name found (--)");
			}
			if(substr($value, 0, 2)=="--") {
				$this->getAvailableNamedOrBoolean($value);
				continue;
			}
			$this->availablePositional[] = $value;
		}
		$this->getDefaults();
	}
	
	private function getDefaults() {
		foreach ($this->model->getArgNames() as $name) {
			if(isset($this->availableNamed[$name])) {
				continue;
			}
			if(!$this->model->getNamedArg($name)->hasDefault()) {
				continue;
			}
			/**
			 * Usually, default values will be set by programmers, not users.
			 * If validation is set, it will be run against the default value
			 * here; the default value is supposed to be in the same format that
			 * the user should use.
			 * At a later point, Argv would throw an ArgvException, but this
			 * error is a coding error, therefore it will be checked & handled
			 * here.
			 */
			$default = $this->model->getNamedArg($name)->getDefault();
			if($this->model->getNamedArg($name)->hasValidate()) {
				try {
					$this->model->getNamedArg($name)->getValidate()->validate($default);
				} catch (ValidateException $ex) {
					throw new InvalidArgumentException("default value for '".$name."' doesn't validate");
				}
			}
			$this->availableNamed[$name] = $default;
		}
	}
	
	private function getAvailableNamedOrBoolean(string $value) {
		$exp = explode("=", $value, 2);
		if(count($exp)==1) {
			$this->availableBoolean[] = substr($value, 2);
			return;
		}
		$this->availableNamed[substr($exp[0], 2)] = $exp[1];
	}
	
	private function sanityCheck() {
		$this->booleanSanity();
		$this->positionalSanity();
		$this->namedSanity();
	}
	
	private function booleanSanity() {
		$defined = $this->model->getBoolean();
		foreach($this->availableBoolean as $value) {
			if(!in_array($value, $defined)) {
				throw new ArgvException("unknown boolean parameter --".$value);
			}
		}
	}
	
	private function positionalSanity() {
		$defined = $this->model->getPositionalCount();
		for($i=0;$i<$defined;$i++) {
			if(!isset($this->availablePositional[$i])) {
				throw new ArgvException("Argument ".($i+1)." (".$this->model->getPositionalName($i).") missing");
			}
		}
		if(count($this->availablePositional)>$defined) {
			throw new ArgvException("Argument ".($defined+1)." not expected");
		}
	}
	
	private function namedSanity() {
		$defined = $this->model->getArgNames();
		foreach($defined as $name) {
			$arg = $this->model->getNamedArg($name);
			if(!isset($this->availableNamed[$name]) && $arg->isMandatory()) {
				throw new ArgvException("mandatory argument --".$name." missing");
			}
		}
		foreach (array_keys($this->availableNamed) as $value) {
			if(!in_array($value, $defined)) {
				throw new ArgvException("argument --".$value." not expected");
			}
		}
	}
	
	private function validate() {
		foreach($this->availablePositional as $pos => $value) {
			$arg = $this->model->getPositionalArg($pos);
			if(!$arg->hasValidate()) {
				continue;
			}
			try {
				$arg->getValidate()->validate($value);
			} catch (ValidateException $e) {
				throw new ArgvException("argument ".($pos+1)." (".$this->model->getPositionalName($pos)."): ".$e->getMessage());
			}
		}

		foreach($this->availableNamed as $name => $value) {
			$arg = $this->model->getNamedArg($name);
			if(!$arg->hasValidate()) {
				continue;
			}
			try {
				$arg->getValidate()->validate($value);
			} catch (ValidateException $e) {
				throw new ArgvException("--".$name.": ".$e->getMessage());
			}
			
		}

	}

	private function convert() {
		foreach($this->availablePositional as $pos => $value) {
			$arg = $this->model->getPositionalArg($pos);
			if(!$arg->hasConvert()) {
				continue;
			}
			$this->availablePositional[$pos] = $arg->getConvert()->convert($this->availablePositional[$pos]);
		}
		foreach($this->availableNamed as $name => $value) {
			
			$arg = $this->model->getNamedArg($name);
			if(!$arg->hasConvert()) {
				continue;
			}
			$this->availableNamed[$name] = $arg->getConvert()->convert($this->availableNamed[$name]);
		}
	}

	/**
	 * Checks whether a certain parameter is available or not. A parameter is
	 * available if it was used by the calling user or if it's ArgModel has a
	 * default value.
	 * @param type $key
	 * @return bool
	 */
	function hasValue($key):bool {
		return isset($this->availableNamed[$key]);
	}
	/**
	 * Gets the value of a specific parameter. Note that parameters which are
	 * not available will throw an exception, so hasValue should be called
	 * beforehand if a value is not mandatory and has no default value.
	 * 
	 * @param type $key
	 * @return string
	 * @throws Exception
	 */
	function getValue($key): string {
		if(!$this->hasValue($key)) {
			throw new OutOfRangeException("argument value ".$key." doesn't exist");
		}
	return $this->availableNamed[$key];
	}
	
	function hasPositional(int $pos) {
		return isset($this->availablePositional[$pos]);
	}
	
	function getPositional(int $pos) {
		if(!$this->hasPositional($pos)) {
			throw new OutOfRangeException("positional argument ".$pos." doesn't exist");
		}
	return $this->availablePositional[$pos];
	}
	
	/**
	 * getBoolean will evaluate to true if a parameter was set (like --force),
	 * and to false, if it was not set. It will throw an Exception if it was
	 * not defined in an instance of ArgvModel.
	 * @param type $key
	 * @return bool
	 * @throws Exception
	 */
	function getBoolean($key):bool {
		if(!in_array($key, $this->model->getBoolean())) {
			throw new OutOfRangeException("boolean argument ".$key." is not defined");
		}
		return in_array($key, $this->availableBoolean);
	}
}