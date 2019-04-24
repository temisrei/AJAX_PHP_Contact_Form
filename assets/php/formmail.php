<?php

// check if ajax request
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')) {
	$output = json_encode(array(
		'type'=>'error',
		'text'=>'Sorry, Request must be Ajax POST'
	));
	die($output);
}

// (1/4) to_email
$to_email = 'reilin@kmu.edu.tw';

// Sanitize input data
$first_name = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
$last_name = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
$affiliation = filter_var($_POST['affiliation'], FILTER_SANITIZE_STRING);
$institution = filter_var($_POST['institution'], FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); // email
$phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
$nation = filter_var($_POST['nation'], FILTER_SANITIZE_STRING);
$title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
$topic = filter_var($_POST['topic'], FILTER_SANITIZE_STRING);

// (2/4) mail subject
$subject  = '2019ALDH2STAR ONLINE SUBMISSION';
$subject .= ", $first_name $last_name, $title";

// Backend Validation
// if (empty($email)) {
// 	$output = json_encode(array(
// 		'type'=>'error',
// 		'text'=>'Sorry, You must fill E-Mail'
// 	));
// 	die($output);
// }
if ( empty($first_name) ||
     empty($last_name) ||
     empty($affiliation) ||
     empty($institution) ||
     empty($email) ||
     empty($phone) ||
     empty($nation) ||
     empty($title) ||
     empty($topic) ) {
	$output = json_encode(array(
		'type'=>'error',
		'text'=>'Sorry, You must fill Form'
	));
	die($output);
}

// mail body = message + attachment
// message
$message  = '<html><body><ul>';
$message .= '<li>First Name: ' . $first_name . '</li>';
$message .= '<li>Last Name: ' . $last_name . '</li>';
$message .= '<li>Affiliation: ' . $affiliation . '</li>';
$message .= '<li>Institution: ' . $institution . '</li>';
$message .= '<li>E-Mail: ' . $email . '</li>';
$message .= '<li>Phone: ' . $phone . '</li>';
$message .= '<li>Nation: ' . $nation . '</li>';
$message .= '<li>Title: ' . $title . '</li>';
$message .= '<li>Topoic: ' . $topic . '</li>';
$message .= '</ul></body></html>';


// check if upload file
$file_attached = false;
if (isset($_FILES["file"])) {
	$file_name     = $_FILES["file"]["name"];
	$file_type     = $_FILES["file"]["type"];
	$file_tmp_name = $_FILES["file"]["tmp_name"];
	$file_error    = $_FILES["file"]["error"];
	$file_size     = $_FILES["file"]["size"];

	// check if file_error
	if ($file_error > 0) {
		$file_error_msg = array(
			1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini.",
			2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
			3=>"The uploaded file was only partially uploaded.",
			4=>"No file was uploaded.",
			6=>"Missing a temporary folder. Introduced in PHP 5.0.3.",
			7=>"Failed to write file to disk. Introduced in PHP 5.1.0.",
			8=>"A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help. Introduced in PHP 5.2.0."
		);
		$output = json_encode(array(
			'type'=>'error',
			'text'=>$file_error_msg[$file_error]
		));
		die($output);
	}

	//// read file
	//// 1. deal with file
	// $handle = fopen($file_tmp_name, "r");
	// $file_content = fread($handle, $file_size);
	// fclose($handle);

	//// 2. deal with file
	$file_content = file_get_contents($file_tmp_name);
	$file_content = chunk_split(base64_encode($file_content));

	$file_attached = true;
}


$separator = md5(uniqid(time())); // Random Hash
$eol = "\r\n"; // RFC
$replyto = $from_mail = $email; // Set Email

if ($file_attached) {
	// Main Header
	$header  = "From: $first_name $last_name <$from_mail>" . $eol;
	$header .= "Reply-To: $replyto" . $eol;
	// $header .= "MIME-Version: 1.0" . $eol;
	$header .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"\r\n\r\n"; // $eol*2
	// $header .= "Content-Transfer-Encoding: 8bit" . $eol;

	// Message
	$body  = "--" . $separator . $eol;
	$body .= "Content-Type: text/html; charset=utf-8" . $eol;
	$body .= "Content-Transfer-Encoding: 8bit\r\n\r\n"; // $eol*2
	$body .= $message . "\r\n\r\n"; // $eol*2

	// Attachment
	$body .= "--" . $separator . $eol;
	// $body .= "Content-Type: $file_type; name=\"" . $file_name . "\"" . $eol; // $name -> $file_name
	$body .= "Content-Type: application/octet-stream; name=\"" . $file_name . "\"" . $eol; // $name -> $file_name
	$body .= "Content-Transfer-Encoding: base64" . $eol;
	$body .= "Content-Disposition: attachment; filename=\"" . $file_name ."\"\r\n\r\n"; // $eol*2
	$body .= $file_content . "\r\n\r\n"; // $eol*2
	$body .= "--" . $separator . "--";
} else {
	// Main Header
	$header  = "From: $first_name $last_name <$from_mail>" . $eol;
	$header .= "Reply-To: $replyto" . $eol;
	$header .= "MIME-Version: 1.0" . $eol;
	$header .= "Content-Type: text/html; charset=utf-8" . $eol;

	$body .= $message . $eol;
}

try {
	mail($to_email, $subject, $body, $header);
	echo json_encode(array(
		'type'=>'success',
		'text'=>'ONLINE Submission success.'
	));
} catch (Exception $e) {
	echo json_encode(array(
		'type'=>'error',
		'text'=>'Error: ' . $e->getMessage()
	));
}


?>