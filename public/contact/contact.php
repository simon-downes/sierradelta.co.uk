<?php

// recaptcha site key 6LdIwUUUAAAAAJSOVWfHr5BOOqlDlQaehrvktk2g

use ReCaptcha\ReCaptcha;
use Mailgun\Mailgun;

use spf\contracts\support\Type;
use spf\support\Fieldset;

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../secrets.php';

$form = new Fieldset();
$form->add('name',    Type::TEXT,  ['required' => true]);
$form->add('email',   Type::EMAIL, ['required' => true]);
$form->add('message', Type::TEXT,  ['required' => true]);
$form->add('captcha', Type::TEXT,  ['required' => true]);

list($cleaned, $errors) = $form->validate([
	'name'    => $_POST['name']    ?? '',
	'email'   => $_POST['email']   ?? '',
	'message' => $_POST['message'] ?? '',
	'captcha' => $_POST['g-recaptcha-response'] ?? '',
]);

if( $errors ) {

	$messages = [
		'required' => "This field is required",
		'email'    => "Must be a valid email address",
	];

	foreach( $errors as $field => &$error ) {
		$error = $messages[$error] ?? $error;
	}

	http_response_code(400);
	header('Content-type: application/json');
	echo json_encode($errors);

	return;

}

extract($cleaned);

$recaptcha = new ReCaptcha(RECAPTCHA_KEY);

$resp = $recaptcha->verify($captcha, $_SERVER['REMOTE_ADDR']);

if( $resp->isSuccess() ) {

	// verified!
	// if Domain Name Validation turned off don't forget to check hostname field
	// if($resp->getHostName() === $_SERVER['SERVER_NAME']) {  }

	$transport = (new Swift_SmtpTransport('smtp.mailgun.org', 587, 'tls'))
		->setUsername(MAILGUN_USER)
		->setPassword(MAILGUN_PASS)
	;

	// Create the Mailer using your created Transport
	$mailer = new Swift_Mailer($transport);

	// Create a message
	$message = (new Swift_Message('Web Enquiry'))
		->setFrom([$email => $name])
		->setTo(['simon@sierradelta.co.uk' => 'Simon Downes'])
		->setBody($message)
	 ;

	// Send the message
	$result = $mailer->send($message);

	echo "OK";

}
else {
	http_response_code(400);
	echo json_encode(['captcha' => 'Invalid recaptcha']);
}
