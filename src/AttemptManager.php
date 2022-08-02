<?php declare(strict_types=1);

namespace uberpass;

use stdClass;

/*
	FILE FORMAT

	{
		"foo@mail.com" : {
			"a" : 0,           // attempts
			"t" : 1659475007   // last timestamp
		}
	}
*/

class AttemptManager {
	private $path;
	private $data;
	private $datasets;		

	public function __construct(string $path) {
		$this->path     = $path;
		$this->data     = [];
		$this->datasets = [];

		$this->load();
	}

	public function __destruct() {
		$this->save();
	}

	private function load() : void {
		if (!file_exists($this->path))
			return;
		$data = json_decode(file_get_contents($this->path));
		if (!$data)
			return;
		// load file as hash map
		$this->data     = (array) $data;
		$this->datasets = array_keys($this->data);
	}

	private function save() : void {
		file_put_contents($this->path, json_encode($this->data));
	}

	private function exists(string $email) : bool {
		return in_array($email, $this->datasets);
	}

	public function attempts(string $email) : int {
		return $this->exists($email) ? intval($this->data[$email]->a) : 0;
	}

	public function reset(string $email) : void {
		if (!$this->exists($email))
			return;
		unset($this->data[$email]);
	}

	public function fail(string $email) : void {
		if ($this->exists($email))
			$this->data[$email]->a += 1;
		else {
			$user = new stdClass();
			$user->a = 1;
			$user->t = time();
			$this->data[$email] = $user;
		}
	}
}