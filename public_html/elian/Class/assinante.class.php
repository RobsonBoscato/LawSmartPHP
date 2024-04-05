<?php 
require_once 'ConexaoMysql.php';
class assinante extends ConexaoMysql {
    public $clicksign_key;
    public $secret_signerkey;
    public function getClicksign_key(){
        return $this->clicksign_key;
    }
    public function getSecret_signerkey(){
        return $this->secret_signerkey;
    }
    public function setClicksign_key($clicksign_key){
        $this->clicksign_key = $clicksign_key;
    }
    public function setSecret_signerkey($secret_signerkey){
        $this->secret_signerkey = $secret_signerkey;
    }
    public function Insert() {
        $assinante = $this->pdo->prepare("INSERT INTO assinante (
            clicksign_key,
            secret_signerkey,
        ) VALUES (
            :clicksign_key,
            :secret_signerkey
        )");
        $assinante->bindValue(':clicksign_key' ,$this->getClicksign_key());
        $assinante->bindValue(':secret_signerkey' ,$this->getSecret_signerkey());
        try {
            return $assinante->execute();
        }
catch (Exception $retorno) {
    return $retorno->getMessage();
        
        }
    }
    public function Update() {
        $assinante = $this->pdo->prepare("UPDATE assinante SET
            clicksign_key = :clicksign_key,
            secret_signerkey = :secret_signerkey
        ");
        $assinante->bindValue(':clicksign_key' ,$this->getClicksign_key());
        $assinante->bindValue(':secret_signerkey' ,$this->getSecret_signerkey());
        try {
            return $assinante->execute();
        }
catch (Exception $retorno) {
    return $retorno->getMessage();
        
        }
    }
    public function Delete() {
        $assinante = $this->pdo->prepare("DELETE FROM assinante
        ");
        try {
            return $assinante->execute();
        }
catch (Exception $retorno) {
    return $retorno->getMessage();
        
        }
    }
    public function Select() {
        $assinante = $this->pdo->prepare("SELECT * FROM assinante
        ");
        try {
            $assinante->execute();
            return $assinante->fetchAll();
        }
catch (Exception $retorno) {
    return $retorno->getMessage();
        
        }
    }
}
