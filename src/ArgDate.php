<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license LGPL
 */

class ArgDate extends ArgGeneric {
	public function __construct() {
		$this->setValidate(new ValidateDate(ValidateDate::ISO));
	}
}