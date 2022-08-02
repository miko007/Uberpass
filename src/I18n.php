<?php declare(strict_types=1);

namespace uberpass;

class I18n {
	private $directory;
	private $locale;

	public function __construct(string $directory, string $locale) {
		$this->directory = $directory;
		$this->locale    = $locale;

		putenv("LC_ALL=$locale");
		setlocale(LC_ALL, $locale);
		bindtextdomain("uberpass", $directory);
		textdomain("uberpass");	
	}

	public function rawData() : array {
		$content = file_get_contents($this->directory."/$this->locale/LC_MESSAGES/uberpass.po");
		$lines   = explode("\n", $content);
		$data    = [];
		$lastId  = "";

		foreach($lines as $line) {
			$parts      = explode(' ', $line);
			$identifier = array_shift($parts);
			$value      = trim(implode(' ', $parts), "\t\r\0\x0b\"");
			if ($identifier === "msgid")
				$lastId = $value;
			else if ($identifier === "msgstr" && $lastId !== "")
				$data[$lastId] = $value;
		}

		return $data;
	}
}