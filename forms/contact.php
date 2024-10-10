<?php
/**
 * Requires the "PHP Email Form" library
 */

$receiving_email_address = 'shahidafreed643@gmail.com'; // Replace with your email

if (file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php')) {
    include($php_email_form);
} else {
    die('Unable to load the "PHP Email Form" Library!');
}

$contact = new PHP_Email_Form;
$contact->ajax = true;

$contact->to = $receiving_email_address;
$contact->from_name = $_POST['name'];
$contact->from_email = $_POST['email'];
$contact->subject = $_POST['subject'];

$contact->add_message($_POST['name'], 'From');
$contact->add_message($_POST['email'], 'Email');
$contact->add_message($_POST['message'], 'Message', 10);

// Send email
$email_sent = $contact->send();

// Prepare data for Google Sheets
$sheet_url = 'https://script.google.com/macros/s/AKfycbzQImT5PHk2iHcgFhGcP0lqV4uNRGnrHQZjf6y_F13vEuleWhl-FqbKjD76xeNOKThk-w/exec';
$data = [
    'name' => $_POST['name'],
    'email' => $_POST['email'],
    'subject' => $_POST['subject'],
    'message' => $_POST['message']
];

// Send data to Google Sheets
$options = [
    'http' => [
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ],
];

$context  = stream_context_create($options);
$response = @file_get_contents($sheet_url, false, $context);

// Check if email was sent successfully
if ($email_sent && $response !== false) {
    echo json_encode(['success' => true, 'message' => 'Your message has been sent and recorded.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send message or record in Google Sheets.']);
}
?>
