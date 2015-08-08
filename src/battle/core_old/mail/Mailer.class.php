<?php
require_once 'lib/PHPMailer-master/class.phpmailer.php';
require_once 'lib/PHPMailer-master/class.smtp.php';
/**
 * Mailer 
 * @author touchypunchy
 */
class Mailer{	
	public static function send($recipient, $subject, $html, $text = ""){
		$mail = new PHPMailer();

		// OVH
		$mail->IsQMAIL();
		
		// GMAIL
		//$mail->IsSMTP(); // telling the class to use SMTP
		//$mail->SMTPAuth   = true; // enable SMTP authentication
		//$mail->SMTPSecure = "ssl"; // sets the prefix to the server
		
		$mail->Host       = Configuration::MAIL_HOST; // "smtp.labelcarrote.com"; // SMTP server
		$mail->Port       = Configuration::MAIL_PORT; // set the SMTP port for the GMAIL server
		$mail->Username   = Configuration::MAIL_USERNAME; //"postmaster@labelcarrote.com";  // username
		$mail->Password   = Configuration::MAIL_PASS; // password
		$mail->SMTPDebug  = Configuration::MAIL_SMTPDEBUG; // enables SMTP debug information (for testing) // 1 = errors and messages // 2 = messages only

		$mail->SetFrom(Configuration::MAIL_USERNAME, 'FlipApart');

		$mail->AddAddress($recipient, $recipient);//"P2B");
		$mail->addBCC('julien.vigneron@gmail.com');
		$mail->Subject = $subject;
		$mail->MsgHTML($html);
		$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
			
		if(!$mail->Send())
		  return "Mailer Error: " . $mail->ErrorInfo;
		else
		  return "Message sent!";
	}
}
