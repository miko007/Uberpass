<?php

use uberpass\Uberpass;

require_once("src/Uberpass.php");

$app      = new Uberpass();
$settings = $app->settings();

?>
<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel=stylesheet type=text/css hreF=css/uberpass.css />
	<script type="module">
		import App from "./javascript/uberpass.js";

		document.addEventListener("DOMContentLoaded", () => {
			const _ = identifier => {
				const langData = {
				<?php
					$langData = $app->i18n();
					
					foreach ($langData->rawData() as $key => $value)
						echo "\"$key\" : \"$value\",\n";
				?>
				};

				return langData[identifier] || identifier;
			};
			const l = identifier => {};
			new App(_);
		});
	</script>
	<title><?php echo $settings->get("title", "Uberpass"); ?></title>
</head>
<body>
	<div id="background"></div>
	<section class="content">
		<section class="card">
			<section class="logo">
				<img src="images/uberspace_rocket.svg" alt="Uberspace Rakete" />
				<h1><?php echo $settings->get("title", "Uberpass"); ?></h1>
			</section>
			<p>
				<?php
					$hint = $settings->get("hint");	
					echo $hint ? $hint : _("Here you can change the password for your Uberspace mail account.");
				?>
			</p>
			<section id=error></section>
			<form action="." method="POST" autocomplete="off" id=form>
				<input type=text name=email placeholder="<?php echo $settings->get("mail_placeholder", _("mail address")); ?>" autocomplete="false" />
				<input type=password name=currentPassword placeholder="<?php echo _("current password") ?>" autocomplete="false" />
				<input type=password name=password placeholder="<?php echo _("new password"); ?>" />
				<input type=password name=passwordConfirm placeholder="<?php echo _("repeat new password"); ?>" />
				<button><?php echo _("change"); ?></button>
			</form>
		</section>
		<nav>
			<?php
				$links = explode(',', $settings->get("links", ""));
				foreach($links as $link) {
					$parts = explode(' ', $link);
					if (count($parts) < 2)
						continue;
					$url   = array_shift($parts);
					$label = implode(' ', $parts);
					echo "<a href='$url'>$label</a>";
				}
			?>
		</nav>
	</section>
</body>
</html>