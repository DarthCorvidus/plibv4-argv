<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license LGPL
 */

/**
 * Main Class for Argv example.
 */
class Main {
	private $argv;
	private $argvImport;
	function __construct(array $argv) {
		$model = new ArgvExample();
		if(count($argv)==1) {
			$reference = new ArgvReference($model);
			echo $reference->getReference();
			die();
		}
		try {
			$this->argvImport = new Argv($argv, $model);
		} catch(ArgvException $e) {
			echo $e->getMessage().PHP_EOL;
			die();
		}
	}
	
	function run() {
		echo "Input file:  ".$this->argvImport->getPositional(0).PHP_EOL;
		echo "Output file: ".$this->argvImport->getPositional(1).PHP_EOL;
		echo "Time:        ".$this->argvImport->getValue("time").PHP_EOL;
		echo "User:        ".$this->argvImport->getValue("user").PHP_EOL;
		echo "Locked:      ".$this->argvImport->getBoolean("locked").PHP_EOL;
		echo "Test:        ".$this->argvImport->getBoolean("test").PHP_EOL;
	}
}