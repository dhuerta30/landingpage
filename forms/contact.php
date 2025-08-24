<?php
// Configuración
$receiving_email_address = 'tu_correo@dominio.com';
$recaptcha_secret = "TU_SECRET_KEY"; // Clave secreta de reCAPTCHA

// Validar que venga todo
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Verificar si existe reCAPTCHA
  if (!isset($_POST['g-recaptcha-response'])) {
    die("Por favor confirma que no eres un robot.");
  }

  // Verificar con Google
  $captcha = $_POST['g-recaptcha-response'];
  $verifyResponse = file_get_contents(
    "https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptcha_secret . "&response=" . $captcha
  );
  $responseData = json_decode($verifyResponse);

  if (!$responseData->success) {
    die("Verificación de reCAPTCHA fallida. Intenta de nuevo.");
  }

  // Incluir librería PHP Email Form
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

  // Configura SMTP si quieres (más seguro que mail())
  /*
  $contact->smtp = array(
    'host' => 'smtp.tuservidor.com',
    'username' => 'usuario',
    'password' => 'contraseña',
    'port' => '587'
  );
  */

  $contact->add_message($_POST['name'], 'De');
  $contact->add_message($_POST['email'], 'Email');
  $contact->add_message($_POST['message'], 'Mensaje', 10);

  echo $contact->send();
} else {
  echo "Método no permitido.";
}

