<?php declare(strict_types=1);

namespace uberpass;

use Exception;

class Endianness {
	const LITTLE_ENDIAN = 1;
	const BIG_ENDIAN    = 2;
}

class I18n {
	const MAGIC_BIG    = 0x950412de;
	const MAGIC_LITTLE = 0xde120495;

	private $locale;
	private $rawData;

	public function __construct(string $directory, string $locale) {
		$this->locale    = $locale;
		$this->rawData   = self::ParseMOFile($directory."/$this->locale/LC_MESSAGES/uberpass.mo");

		putenv("LC_ALL=$locale");
		setlocale(LC_ALL, $locale);
		bindtextdomain("uberpass", $directory);
		bind_textdomain_codeset("uberpass", "utf-8");
		textdomain("uberpass");	
	}

	public function raw() : array {
		return $this->rawData;
	}

	/**
	 * a simple gettext `.mo` file parser
	 * 
	 * @author MikO <miko@maschinendeck.org>
	 * @see spec: https://www.gnu.org/software/gettext/manual/html_node/MO-Files.html
	 */
	public static function ParseMOFile(string $path) {
		$fp    = fopen($path, "rb"); // open file for reading in binary
		$magic = fread($fp, 4);      // magic number is 4 bytes long

		$data = unpack("Nmagic", $magic);
		extract($data);

		// checking file type and endianness
		if ($magic === self::MAGIC_BIG)
			$endianness = Endianness::BIG_ENDIAN;
		else if ($magic === self::MAGIC_LITTLE)
			$endianness = Endianness::LITTLE_ENDIAN;
		else
			throw new Exception("trying to parse a non `.mo` file");
			
		// using matching parameters for `::unpack()` depending on endianness
		switch ($endianness) {
			case Endianness::BIG_ENDIAN:
				$uint16 = "n";
				$uint32 = "N";
				break;
			case Endianness::LITTLE_ENDIAN:
				$uint16 = "v";
				$uint32 = "V";
				break;
			default:
				assert(false, "not reachable");
		}

		// extracting the file header
		// 4 - minor version
		// 4 - major version
		// 8 - number of string
		// 8 - offset for first original string's description
		// 8 - offset for first translation string's description
		$header = fread($fp, 16);
		$data   = unpack("${uint16}minorVersion/${uint16}majorVersion/${uint32}N/${uint32}O/${uint32}T", $header);
		extract($data);

		// reading string's offsets and lenghts
		$offset          = $O;
		$stringLocations = [];
		fseek($fp, $offset);

		$skipHeader = false;

		while ($offset <= ($O + (($N - 1) * 8))) {
			$stringInfo = fread($fp, 8);
			$stringInfo = unpack("${uint32}sLength/${uint32}sOffset", $stringInfo);
			extract($stringInfo);

			// if there is a 0 length string, that means the first string of
			// the translations will be the header, which we skip, because
			// we do not need it
			if ($offset === $O && $sLength < 1)
				$skipHeader = true;
			array_push($stringLocations, [$sLength, $sOffset]);
			$offset += 8;
		}

		// reading translations's offsets and lenghts
		$offset = $skipHeader ? $T + 8 : $T;
		$translationLocations = [];
		fseek($fp, $offset);

		while ($offset <= ($T + (($N - 1) * 8))) {
			$translastionInfo = fread($fp, 8);
			$translastionInfo = unpack("${uint32}sLength/${uint32}sOffset", $translastionInfo);
			extract($translastionInfo);
			array_push($translationLocations, [$sLength, $sOffset]);
			$offset += 8;
		}

		$readStrings = function(array $offsets) use ($fp) : array {
			$strings = [];
			foreach ($offsets as $location) {
				$sLength = $location[0];
				$sOffset = $location[1];

				if ($sLength < 1)
					continue;

				fseek($fp, $sOffset);
				$string = fread($fp, $sLength);
				array_push($strings, $string);
			}

			return $strings;
		};

		$strings      = $readStrings($stringLocations);
		$translations = $readStrings($translationLocations);

		return array_combine($strings, $translations);
	}
}