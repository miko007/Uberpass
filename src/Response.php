<?php declare(strict_types=1);

namespace uberpass;

use Error;

class Response {
	public $status;
	public $data;

	public function __construct(?Error $error) {
		$this->status = $error ? $error->getCode()    : 200;
		$this->data   = $error ? $error->getMessage() : "OK";
	}

	public function __toString() : string {
		return json_encode($this, JSON_PRETTY_PRINT);
	}
}