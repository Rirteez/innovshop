<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Psr\Log\LoggerInterface;

class MailerService
{
    private MailerInterface $mailer;
    private Environment $twig;
    private LoggerInterface $logger;

    public function __construct(MailerInterface $mailer, Environment $twig, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
    }

    public function sendOrderStatusUpdate(string $to, string $subject, string $template, array $context): void
    {
        try {
            $htmlContent = $this->twig->render($template, $context);

            $email = (new Email())
                ->from('erwannglath@gmail.com')
                ->to($to)
                ->subject($subject)
                ->html($htmlContent);

            $this->mailer->send($email);
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
        }
    }
}
