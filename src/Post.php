<?php declare(strict_types=1);

namespace uberpass;

class Post {
	private $data;
	private $errors;

	public function __construct(...$require) {
		$this->errors = [];
		$this->data   = json_decode(file_get_contents("php://input"));
		if (!$this->data || count(get_object_vars($this->data)) < 1)
			return;	

		foreach ($require as $key) {
			if (!in_array($key, array_keys(get_object_vars($this->data))) || trim($this->data->$key) === "")
				array_push($this->errors, $key);
		}
	}

	public function posted() : bool {
		return count(get_object_vars($this->data)) > 0;
	}

	public function get($key) : ?string {
		if (in_array($key, array_keys(get_object_vars($this->data))))
			return $this->data->$key;

		return null;
	}

	public function errors() : array {
		return $this->errors;
	}

	public function hasErrors() : bool {
		return count($this->errors) > 0;
	}
}