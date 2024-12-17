<?php
namespace Dongarbanzo\Newsletter;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Newsletter {
    private $config;
    private $mail;

    public function __construct(array $config) {
        $this->config = $config;
        $this->initializeMailer();
    }

    private function initializeMailer() {
        $this->mail = new PHPMailer(true);
        
        try {
            $this->mail->isSMTP();
            $this->mail->Host = $this->config['smtp_host'];
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $this->config['smtp_username'];
            $this->mail->Password = $this->config['smtp_password'];
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = $this->config['smtp_port'];
        } catch (Exception $e) {
            error_log("Error configurando mailer: " . $e->getMessage());
        }
    }

    public function subscribe(array $data) {
        try {
            $this->mail->setFrom($this->config['smtp_from_email'], $this->config['smtp_from_name']);
            $this->mail->addAddress($this->config['recipient_email']);
            
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Nueva Suscripción Newsletter Don Garbanzo';
            
            $mailBody = $this->buildEmailBody($data);
            $this->mail->Body = $mailBody;

            $this->mail->send();
            return ['success' => true, 'message' => 'Suscripción exitosa'];
        } catch (Exception $e) {
            error_log("Error enviando email: " . $this->mail->ErrorInfo);
            return ['success' => false, 'message' => 'Error al suscribirse'];
        }
    }

    private function buildEmailBody(array $data): string {
        return sprintf(
            "<h2>Nueva Suscripción</h2>
            <p><strong>Nombre:</strong> %s</p>
            <p><strong>Correo:</strong> %s</p>
            <p><strong>Mensaje:</strong> %s</p>",
            htmlspecialchars($data['name'] ?? 'No proporcionado'),
            htmlspecialchars($data['email']),
            htmlspecialchars($data['message'] ?? 'Sin mensaje')
        );
    }
}