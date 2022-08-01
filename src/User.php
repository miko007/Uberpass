<?php declare(strict_types=1);

namespace uberpass;

/**
 * Class User
 *
 * handles user authentication and password changing
 */
class User {
	private $passwordHash;
	private $username;
	private $isValidUser;
	private $settings;
	private $debug;
	
	private static $DebugInfo = 'Name: ttt
Encrypted-Password: $1$Kt0F66a1$h0W.KWr7GbwWMeuN.UjMc1
Directory: ./users/ttt
Hard-Quota: N/A
Soft-Quota: N/A
Message-Size-Limit: N/A
Message-Count-Limit: N/A
Creation-Time: 1659102075
Expiry-Time: N/A
Has-Mailbox: true
Mailbox-Enabled: true';

	/**
	 * Constructor
	 *
	 * @param $kayosID
	 */
	public function __construct(string $email, Settings $settings) {
		$this->settings    = $settings;
		$this->debug       = $this->settings->get("DEBUG") ? true : false;
		$this->username    = self::EmailToUserId($email);
		$this->isValidUser = $this->getUserInfo();
	}

	private static function EmailToUserId(string $email) : string {
		$parts = explode('@', $email);

		if (count($parts) < 2)
			throw new \Error("not a valid email");
		
		return $parts[0];
	}

	/**
	 * scrape Vuser info from shell command "dumpvuser"
	 *
	 * @return bool
	 */
	private function getUserInfo() : bool {
		$info = $this->debug ? self::$DebugInfo : shell_exec("dumpvuser " . escapeshellarg($this->username));
		$info = explode("\n", $info);
		if (count($info) < 2)
			return false;
		
		$passwordHash = explode(": ", $info[1]);
		if (count($passwordHash) !== 2)
			return false;
		
		$this->passwordHash = $passwordHash[1];

		return true;
	}

	/**
	 * check if submitted password equals existing password, scraped vom "dumpvuser"
	 *
	 * @param $password
	 *
	 * @return bool
	 */
	public function validPassword(string $password) : bool {
		if ($this->passwordHash == crypt($password, $this->passwordHash))
			return true;

		return false;
	}

	/**
	 * getter for attribute $isValidUser
	 *
	 * @return bool
	 */
	public function validUser() : bool {
		return $this->isValidUser;
	}

	/**
	 * Set Vusers password to the new password
	 *
	 * @param $password
	 *
	 * @return int
	 */
	public function setPassword($password) : int {
		$result = shell_exec("echo " . escapeshellarg($password) . " | vpasswd " . escapeshellarg($this->username));

		return $result ? 0 : 1;
	}
}