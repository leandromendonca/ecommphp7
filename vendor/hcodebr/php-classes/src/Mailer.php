<?php

namespace Hcode;

use Rain\Tpl;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    private $config;
    private $mail;

    public function __construct($toAddress, $toName, $subject, $tplName, $data = array())
    {
        $this->config = new Config('mailer');

        $tplConfig = array(
            "tpl_dir"   => $_SERVER["DOCUMENT_ROOT"]."/views/email/",
            "cache_dir" => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
            "debug"     => false
        );

        Tpl::configure($tplConfig);

        $tpl = new Tpl;

        var_dump($data);

        foreach ($data as $key => $value)
        {
            $tpl->assign($key, $value);
        }

        $html = $tpl->draw($tplName, true);

        $this->mail = new PHPMailer;

        //Tell PHPMailer to use SMTP
        $this->mail->isSMTP();

        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $this->mail->SMTPDebug = 0;

        //Set the hostname of the mail server
        $this->mail->Host = $this->config->getmailer_host();
        // use
        // $mail->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6

        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $this->mail->Port = $this->config->getmailer_port();

        //Set the encryption system to use - ssl (deprecated) or tls
        $this->mail->SMTPSecure = $this->config->getmailer_secure();

        //Whether to use SMTP authentication
        $this->mail->SMTPAuth = true;

        //Username to use for SMTP authentication - use full email address for gmail
        $this->mail->Username = $this->config->getmailer_user();

        //Password to use for SMTP authentication
        $this->mail->Password = $this->config->getmailer_pass();

        //$this->mail->XMailer = 'MyMailer';

        //Set who the message is to be sent from
        $this->mail->setFrom($this->config->getmailer_from_address(), utf8_decode($this->config->getmailer_from_name()));

        //Set an alternative reply-to address
        $this->mail->addReplyTo($this->config->getmailer_from_address(), utf8_decode($this->config->getmailer_from_name()));

        //Set who the message is to be sent to
        $this->mail->addAddress($toAddress, $toName);

        //Set the subject line
        $this->mail->Subject = $subject;

        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $this->mail->msgHTML(utf8_decode($html));

        //Replace the plain text body with one created manually
        $this->mail->AltBody = 'This is a plain-text message body';

        //Attach an image file
        //$this->mail->addAttachment('images/phpmailer_mini.png');
    }

    public function send()
    {
        $returns = $this->mail->send();

        if(!$returns)
        {
            throw new \Exception('Mensagem não pode ser enviada. Erro: ' . $mail->ErrorInfo);
        }

        return $returns;
    }

}

?>