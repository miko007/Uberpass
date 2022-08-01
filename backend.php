<?php declare(strict_types=1);

require_once("./src/Uberpass.php");


use uberpass\Uberpass;
use uberpass\User;
use uberpass\Response;

header("Content-type: application/json");

$app            = new Uberpass();
$settings       = $app->settings();
$post           = $app->post();
$attemptManager = $app->attempmtManager();

$passwordLength  = $settings->get("passwordLength", "8");
$password        = $post->get("password");
$passwordConfirm = $post->get("passwordConfirm");
$passwordsMatch  = $password === $passwordConfirm;
$email           = $post->get("email");
$email           = $email ? $email : "";

$user      = null;
$userError = false;

try {
	$user = new User($email, $settings);
} catch (Error $error) {
	if ($email !== "" && !$post->hasErrors() && $passwordsMatch)
		$userError = true;
}

$error = null;
if ($post->hasErrors())
	$error = new Error("Es sind nicht alle erforderlichen Felder ausgefüllt.", 400);
else if (strlen($password) < $passwordLength)
	$error = new Error("Das Passwort muss mindestens $passwordLength Zeichen lang sein", 401);
else if (!$passwordsMatch)
	$error = new Error("Die angegebenen Passwörter stimmen nicht überein", 402);
else if ($userError)
	$error = new Error("Benutzername und/oder Passwort sind inkorrekt", 503);
else if (!$attemptManager->canTry($email))
	$error = new Error("Maximale Anzahl an Änderungsversuchen erreicht. Versuchen Sie es später nochmal.");

if (!$error && $user) {
	if ($user->validPassword($post->get("currentPassword"))) {
		$user->setPassword($password);
		$attemptManager->reset($email);
	} else {
		$attemptManager->falseAttempt($email);
		$error = new Error("Benutzername und/oder Passwort sind inkorrekt", 503);
	}
}

$response = new Response($error);

echo $response;