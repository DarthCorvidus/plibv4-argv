<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license LGPL
 */

/**
 * Example implementation of ArgvModel
 *
 * @author hm
 */
class ArgvExample implements ArgvModel {
	private $positional = array();
	private $positionalNames = array();
	private $namedArg = array();
	private $boolean = array();
	function __construct() {
		$this->positional[0] = new ArgGeneric();
		$this->positional[0]->setMandatory();
		$this->positionalNames[] = "input";
		$this->positional[1] = new ArgGeneric();
		$this->positional[0]->setMandatory();
		$this->positionalNames[] = "output";
		$this->boolean = array("locked", "test");
		$this->namedArg["time"] = new ArgGeneric();
		$this->namedArg["time"]->setValidate(new ValidateTime());
		$this->namedArg["time"]->setConvert(new ConvertTime(ConvertTime::HMS, ConvertTime::SECONDS));
		$this->namedArg["time"]->setDefault("00:00:00");
		$this->namedArg["user"] = new ArgGeneric();
		$this->namedArg["user"]->setMandatory();
	}

	public function getArgNames(): array {
		return array_keys($this->namedArg);
	}

	public function getBoolean(): array {
		return $this->boolean;
	}

	public function getNamedArg(string $name): \ArgModel {
		return $this->namedArg[$name];
	}

	public function getPositionalArg(int $i): \ArgModel {
		return $this->positional[$i];
	}

	public function getPositionalCount(): int {
		return count($this->positional);
	}

	public function getPositionalName(int $i): string {
		return $this->positionalNames[$i];
	}

}
