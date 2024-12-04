<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license LGPL
 */

/**
 * ArgvReference prints out a simple reference of defined arguments.
 */
class ArgvReference {
	private $argvModel;
	function __construct(ArgvModel $model) {
		$this->argvModel = $model;
	}
	
	function getReference(): string {
		$ref  = [];
		$ref[] = $this->getPositionalReference();
		$ref[] = $this->getNamedReference();
		$ref[] = $this->getBooleanReference();
	return implode(PHP_EOL, $ref);
	}
	
	private function getPositionalReference(): string {
		$count = $this->argvModel->getPositionalCount();
		if($count==0) {
			return "";
		}
		$return = "";
		$return .= "Positional Arguments:".PHP_EOL;
		for($i=0;$i<$count;$i++) {
			$return .= "\tArgument ".($i+1).": ";
			$return .= $this->argvModel->getPositionalName($i);
			$return .= PHP_EOL;
		}
	return $return;
	}
	
	private function getBooleanReference(): string {
		if(empty($this->argvModel->getBoolean())) {
			return "";
		}
		$return = "";
		$return .= "Boolean Arguments:".PHP_EOL;
		$longest = new LongestString();
		$longest->addArray($this->argvModel->getBoolean());
		foreach($this->argvModel->getBoolean() as $value) {
			$return .= "\t--".$value.PHP_EOL;
		}
	return $return;
	}
	
	private function getNamedReference(): string {
		$names = $this->argvModel->getArgNames();
		if(count($names)==0) {
			return "";
		}
		$longest = new LongestString();
		$longest->addArray($names);
		$return = "";
		$return .= "Named Arguments:".PHP_EOL;
		foreach($names as $name) {
			$arg = $this->argvModel->getNamedArg($name);
			$return .= "\t--".str_pad($name, $longest->getLength(), " ");
			if($arg->isMandatory()) {
				$return .= " (mandatory)".PHP_EOL;
			} else {
				$return .= " (optional)".PHP_EOL;
			}
		}
	return $return;
	}
}
