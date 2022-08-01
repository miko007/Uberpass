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
	<script type=module src=javascript/uberpass.js></script>
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
				<?php echo $settings->get("hint", "Hier kannst du das Passwort für deinen Uberspace Mailkonto ändern."); ?>
			</p>
			<section id=error></section>
			<form action="." method="POST" autocomplete="off" id=form>
				<input type=text name=email placeholder="E-Mail-Adresse" autocomplete="false" />
				<input type=password name=currentPassword placeholder="aktuelles Passwort" autocomplete="false" />
				<input type=password name=password placeholder="neues Passwort" />
				<input type=password name=passwordConfirm placeholder="neues Passwort wiederholen" />
				<button>ändern</button>
			</form>
		</section>
	</section>
</body>
</html>