<?php
/*
 * This file is part of the Bibliometric Snowballing project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace BS\Helper;


use BS\Model\App;
use PHPMailer\PHPMailer\PHPMailer;

class MailHelper
{
    /**
     * @var MailHelper|null instance
     */
    protected static $instance = null;

    /**
     * @var PHPMailer $mailer Session
     */
    protected $mailer = null;

    /**
     * MailHelper constructor.
     *
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host = App::instance()->getConfig('smtp/hostname');
        $this->mailer->SMTPAuth = App::instance()->getConfig('smtp/auth');
        $this->mailer->Username = App::instance()->getConfig('smtp/username');
        $this->mailer->Password = App::instance()->getConfig('smtp/password');
        $this->mailer->SMTPSecure = App::instance()->getConfig('smtp/tls') == true ? 'tls' : '';
        $this->mailer->Port = App::instance()->getConfig('smtp/port');
        $this->mailer->isHTML(false);
        $this->mailer->setFrom(App::instance()->getConfig('mail'), 'Bibliometric Snowballing');
    }

    /**
     * Returns the singleton.
     *
     * @return MailHelper instance
     */
    public static function instance()
    {
        if (!isset(MailHelper::$instance)) {
            MailHelper::$instance = new MailHelper();
        }

        return MailHelper::$instance;
    }

    /**
     * Validates an email address using PHPMailer class.
     *
     * @param string $emailAddress email address to validate
     * @return bool true, if email address is validated
     */
    public function validateEmailAddress($emailAddress)
    {
        return PHPMailer::validateAddress($emailAddress);
    }

    /**
     * Sends an email to the admin configured in config.json (key: mail).
     *
     * @param string $subject subject
     * @param string $message message
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function sendToAdmin($subject, $message)
    {
        $this->sendToAddress($subject, $message, App::instance()->getConfig('mail'));
    }

    /**
     * Sends an email to the admin configured in config.json (key: mail).
     *
     * @param string $subject subject
     * @param string $message message
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function sendToAddress($subject, $message, $address)
    {
        $this->mailer->clearAllRecipients();
        $this->mailer->addAddress($address);
        $this->mailer->Subject = $subject;
        $this->mailer->Body = $message;
        $this->mailer->send();
    }
}
