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
	$error = new Error(_("Not all nessecary fields are propagated."), 400);
else if (strlen($password) < $passwordLength)
	$error = new Error(sprintf(_("The password has to be at least %d characters long."), $passwordLength), 401);
else if (!$passwordsMatch)
	$error = new Error(_("The supplied passwords do not match."), 402);
else if ($userError)
	$error = new Error(_("Username and/or passwort are incorrect."), 503);
else if (!$attemptManager->canTry($email))
	$error = new Error(_("The maximum amount of attempts has been reached. Try again later."));

if (!$error && $user) {
	if ($user->validPassword($post->get("currentPassword"))) {
		$user->setPassword($password);
	} else {
		$attemptManager->falseAttempt($email);
		$error = new Error(_("Username and/or passwort are incorrect."), 503);
	}
}

$response = new Response($error);

echo $response;