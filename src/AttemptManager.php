<?php declare(strict_types=1);

namespace uberpass;

use stdClass;

class SerializedUser {
	public $email;
	public $attempts;
	public $lastTime;

	public function __construct(?stdClass $object = null) {
		if ($object) {
			$this->email    = $object->email;
			$this->attempts = intval($object->attempts);
			$this->lastTime = $object->lastTime;
		} else {
			$this->attempts = 0;
			$this->lastTime = time();
		}
	}

	public function increase() : void {
		$this->attempts += 1;
		$this->lastTime = time();
	}

	public function reset() : void {
		$this->attempts = 0;
		$this->lastTime = time();
	}
}

class AttemptManager {
	private $fileName;
	private $data;
	private $settings;

	public function __construct(string $fileName, Settings $settings) {
		$this->settings = $settings;
		$this->data     = [];
		if (!file_exists($fileName))
			file_put_contents($fileName, "[]");
		foreach (json_decode(file_get_contents($fileName)) as $user) {
			array_push($this->data, new SerializedUser($user));
		}
		$this->fileName = $fileName;
	}

	private function save() : void {
		file_put_contents($this->fileName, json_encode($this->data));
	}

	public function attempts(string $email) : int {
		foreach ($this->data as $user) {
			if ($user->email !== $email)
				continue;
			$user = new SerializedUser($user);
			
			return $user->attempts;
		}

		return 0;
	}

	public function canTry(string $email) : bool {
		foreach ($this->data as $user) {
			if (!$user->email === $email)
				continue;
			if ((time() - $user->lastTime) > 60 * 60 * intval($this->settings->get("HOURS_TO_WAIT", "1"))) {
				$user->reset();
				$this->save();
			}
			if ($user->attempts >= intval($this->settings->get("MAX_TRIES", "3")))
				return false;
		}

		return true;
	}

	public function falseAttempt(string $email) : void {
		$user = null;
		foreach ($this->data as $localUser) {
			if ($localUser->email !== $email)
				continue;
			$localUser->increase();
			$this->save();
			return;
		}

		if (!$user) {
			$user = new SerializedUser();
			$user->email    = $email;
		}

		$user->increase();
		array_push($this->data, $user);
		$this->save();
	}
}