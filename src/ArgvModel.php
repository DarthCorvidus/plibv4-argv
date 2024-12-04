<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license LGPL
 */

/**
 * Interface for model as expected by Argv; ArgvModel is supposed to contain
 * several ArgModels, one for each argument.
 */
interface ArgvModel {
	/**
	 * Get the names of named arguments as an array, eg. [input, output] for
	 * what should be 'example.php --input=<value> --output=<value>'.
	 * @return string[]
	 */
	public function getArgNames(): array;
	
	/**
	 * Get model for named argument.
	 * @param string $name
	 * @return UserValue Description
	 */
	public function getNamedArg(string $name): UserValue;

	/**
	 * Get number of positional accounts
	 */
	public function getPositionalCount(): int;
	
	/**
	 * Get ArgModel for positional account (0-indexed)
	 * @param int $i
	 */
	public function getPositionalArg(int $i): UserValue;
	
	/**
	 * Get positional name; the name will be used in error messages if the user
	 * fails to deliver a positional argument.
	 * @param int $i
	 */
	public function getPositionalName(int $i): string;
	
	/**
	 * return an array of pure boolean parameters without a value, which will
	 * evaluate to true if set; eg. the array("run", "log") stands for the
	 * boolean parameters 'example.php --run --log'.
	 * @return string[] Description
	 */
	function getBoolean(): array;
}
