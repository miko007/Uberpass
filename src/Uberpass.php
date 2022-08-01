<?php declare(strict_types=1);

namespace uberpass;

class Uberpass {
	private $settings;
	private $post;
	private $attemptManager;

	public function __construct() {
		spl_autoload_register([$this, "autoload"]);		

		$this->settings       = new Settings(dirname(__FILE__)."/../settings.ini");
		$this->post           = new Post("email", "currentPassword", "password", "passwordConfirm");
		$this->attemptManager = new AttemptManager(dirname(__FILE__)."/../attempts.json", $this->settings);
	}

	public function settings() : Settings {
		return $this->settings;
	}

	public function post() : Post {
		return $this->post;
	}

	public function attempmtManager() : AttemptManager {
		return $this->attemptManager;
	}

	public function autoload(string $className) : void {
		$className = "src".str_replace(["uberpass", "\\"], ["", "/"], $className).".php";
	
		if (file_exists($className))
			require_once($className);
	}
}