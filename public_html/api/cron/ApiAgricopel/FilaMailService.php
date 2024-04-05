<?php
date_default_timezone_set('America/Sao_Paulo');

function my_autoload($pClassName)
{
    include('../../../agricopel/Class' . "/" . $pClassName . ".class.php");
}

spl_autoload_register("my_autoload"); // Carrega as classes 


$email = new emails();
$email->setStatus('pendente');
$list = $email->SelectStatus();
foreach ($list as  $value) {
    $destinatario = 'gilberto@lawfinancas.com.br';//$value['recipient_email'];
    $msg = $value['body'];
    $assunto = $value['subject'];
    $EmailSender = new EmailSender();
    $send =  $EmailSender->enviarEmailSimples($destinatario, $msg, $assunto);
    if($send == true){
        $e = new emails();
        $e->setId($value['id']);
        $e->setStatus('enviado');
        $e->setSent_at(date('Y-m-d H:i:s'));
        $e->UpdateSend();
    }else{
        $e = new emails();
        $e->setId($value['id']);
        $e->setStatus('Erro');
        $e->setSent_at(date('Y-m-d H:i:s'));
        $e->UpdateSend();
    }
    
}
