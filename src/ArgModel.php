<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license LGPL
 */

/**
 * Interface for return value of Argv::getArgModel.
 *
 * ArgvModel allows to define parameters along with conversion and validation.
 * Examples may be to validate that a parameter like --date= contains a valid
 * date.
 */
interface ArgModel {
	/**
	 * returns a default value if it has one. If, for instance, --date is used,
	 * it could be set to „now“.
	 * Note that Argv expects the default value to validate the same way as user
	 * input, ie if you force the user to adhere to, say, HH:MM:SS for time,
	 * your default value has adhere too. It will also be converted the same
	 * way as user input.
	 */
	function getDefault(): string;
	/**
	 * Should return whether an instance of ArgModel has a default value or not.
	 */
	function hasDefault():bool;
	/**
	 * Should return whether an instance of ArgModel has an instance of Validate
	 * defined.
	 */
	function hasValidate():bool;
	/**
	 * Should return an instance of Validate if there is one; Argv will then
	 * call it on the value given by a parameter. getValidate won't be called if
	 * hasValidate returns false.
	 */
	function getValidate():Validate;
	/**
	 * Should return whether an instance of ArgModel has an instance of Convert
	 * defined.
	 */
	function hasConvert():bool;
	/**
	 * Should return an instance of Convert if there is one; Argv will then use
	 * it to convert any input.
	 */
	function getConvert():Convert;
	/**
	 * Should return true if parameter is supposed to be mandatory. Mandatory
	 * parameters will cause Argv an ArgvException when the user forgets to
	 * set it.
	 * If a default value is set, Argv will take the default value if the user
	 * input is missing, without throwing an ArgvException.
	 */
	function isMandatory():bool;
}
