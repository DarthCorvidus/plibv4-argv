<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license LGPL
 */
namespace plibv4\argv;
use plibv4\longeststring\LongestString;
/**
 * ArgvReference prints out a simple reference of defined arguments.
 */
class ArgvReference {
	private $argvModel;
	function __construct(ArgvModel $model) {
		$this->argvModel = $model;
	}
	
	function getReference(): string {
		$lines = $this->getReferenceLines();
		return implode(PHP_EOL, $lines);
	}
	
	/**
	 * @return list<string>
	 */
	public function getReferenceLines(): array {
		$ref  = [];
		if($this->argvModel->getPositionalCount()>0) {
			$ref = array_merge($ref, $this->getPositionalReference());
		}
		if(count($this->argvModel->getArgNames())>0) {
			$ref = array_merge($ref, $this->getNamedReference());
		}
		if(count($this->argvModel->getBoolean())>0) {
			$ref = array_merge($ref, $this->getBooleanReference());
		}
	return $ref;
	}
	
	/**
	 * @return list<string>
	 */
	private function getPositionalReference(): array {
		$count = $this->argvModel->getPositionalCount();
		$lines = [];
		$lines[] = "Positional Arguments:";
		for($i=0;$i<$count;$i++) {
			$line = "\tArgument ".($i+1).": ";
			$line .= $this->argvModel->getPositionalName($i);
			if($this->argvModel->getPositionalArg($i)->isMandatory()) {
				$line .= " (mandatory)";
			} else {
				$line .= " (optional)";
			}
			$lines[] = $line;
		}
	return $lines;
	}

	/**
	 * @return list<string>
	 */
	private function getBooleanReference(): array{
		$lines = [];
		$lines[] = "Boolean Arguments:";
		$longest = new LongestString();
		$longest->addArray($this->argvModel->getBoolean());
		foreach($this->argvModel->getBoolean() as $value) {
			$lines[] = "\t--".$value;
		}
	return $lines;
	}

	/**
	 * @return list<string>
	 */
	private function getNamedReference(): array {
		$names = $this->argvModel->getArgNames();
		$longest = new LongestString();
		$longest->addArray($names);
		$lines = [];
		$lines[] = "Named Arguments:";
		foreach($names as $name) {
			$arg = $this->argvModel->getNamedArg($name);
			$line = "\t--".str_pad($name, $longest->getLength(), " ");
			if($arg->isMandatory()) {
				$line .= " (mandatory)";
			} else {
				$line .= " (optional)";
			}
			$lines[] = $line;
		}
	return $lines;
	}
}
