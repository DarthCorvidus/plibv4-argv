<?php
/**
 * @copyright (c) 2021, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license LGPL
 */

/**
 * Generic implementation of ArgModel
 * 
 * The idea behind Argv is to write your own implementations of ArgModel, but
 * sometimes, it may be sufficient to use a generic implementation. It is also
 * needed for unit tests.
 */
class ArgGeneric implements ArgModel {
	private $validate;
	private $convert;
	private $default;
	private $mandatory = false;
	/**
	 * Set a validator
	 * 
	 * Set a validator, which will be run against user input or default value.
	 * @param Validate $validate
	 */
	public function setValidate(Validate $validate) {
		$this->validate = $validate;
	}
	
	/**
	 * Set a converter
	 * 
	 * Set an implementation of Convert, which will be run against user input or
	 * a default value. It is recommended to use a validator in conjunction with
	 * an implementation of convert, as Validate will be executed before
	 * convert, therefore protecting the converter from unusable values.
	 * @param Convert $convert
	 */
	public function setConvert(Convert $convert) {
		$this->convert = $convert;
	}

	/**
	 * Set a default value
	 * 
	 * Sets a default value which will be used instead of user input, if the
	 * user fails to provide a parameter. Using default values may spare you
	 * some if/else-constructs.
	 */
	public function setDefault(string $default) {
		$this->default = $default;
	}
	
	/**
	 * Sets an argument to be mandatory
	 * 
	 * A mandatory argument forces the user to supply an argument, unless a
	 * default value is set.
	 * @param bool $boolean
	 */
	public function setMandatory() {
		$this->mandatory = TRUE;
	}
	
	public function getConvert(): Convert {
		return $this->convert;
	}

	public function getDefault(): string {
		return $this->default;
	}

	public function getValidate(): Validate {
		return $this->validate;
	}

	public function hasConvert(): bool {
		return $this->convert!==NULL;
	}

	public function hasDefault(): bool {
		return $this->default!==NULL;
	}

	public function hasValidate(): bool {
		return $this->validate!==NULL;
	}

	public function isMandatory(): bool {
		return $this->mandatory;
	}

}