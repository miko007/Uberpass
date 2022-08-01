<?php declare(strict_types=1);

namespace uberpass;

class Settings {
	private $data;

	public function __construct(string $settingsFile) {
		$this->data = parse_ini_file($settingsFile);
	}

	public function get($key, $defaultValue = null) : ?string {
		if (array_key_exists($key, $this->data))
			return $this->data[$key];
		
			return $defaultValue;
	}
}