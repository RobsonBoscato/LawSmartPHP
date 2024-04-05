<?php
require_once 'ConexaoMysqlAncora.php';
class token_users extends ConexaoMysqlAncora
{

    public $id;
    public $client_id;
    public $secret_id;
    public $ambiente;
    public function getId()
    {
        return $this->id;
    }
    public function getClientId()
    {
        return $this->client_id;
    }
    public function getSecretId()
    {
        return $this->secret_id;
    }
    public function getAmbiente()
    {
        return $this->ambiente;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }
    public function setClientId($client_id)
    {
        $this->client_id = $client_id;
    }
    public function setSecretId($secret_id)
    {
        $this->secret_id = $secret_id;
    }
    public function setAmbiente($ambiente)
    {
        $this->ambiente = $ambiente;
    }
 
    // public function Insert()
    // {
    //     $ancora = $this->pdo->prepare("INSERT INTO ancora (
    //         razao,
    //         email,
    //         cnpj,
    //         senha
    //     ) VALUES (
    //         :id,
    //         :razao,
    //         :email,
    //         :cnpj,
    //         :senha
    //     )");
    //     $ancora->bindValue(':razao', $this->getRazao());
    //     $ancora->bindValue(':email', $this->getEmail());
    //     $ancora->bindValue(':cnpj', $this->getCnpj());
    //     $ancora->bindValue(':senha', $this->getSenha());

    //     try {
    //         return $ancora->execute();
    //     } catch (Exception $retorno) {
    //         return $retorno->getMessage();
    //     }
    // }
    // public function Update()
    // {
    //     $ancora = $this->pdo->prepare("UPDATE ancora SET
    //         razao = :razao,
    //         email = :email,
    //         cnpj = :cnpj
    //     WHERE  id = :id
    //     ");
    //     $ancora->bindValue(':id', $this->getId());
    //     $ancora->bindValue(':razao', $this->getrazao());
    //     $ancora->bindValue(':email', $this->getemail());
    //     $ancora->bindValue(':cnpj', $this->getcnpj());
    //     try {
    //         return $ancora->execute();
    //     } catch (Exception $retorno) {
    //         return $retorno->getMessage();
    //     }
    // }
    // public function Delete()
    // {
    //     $ancora = $this->pdo->prepare("DELETE FROM ancora
    //         WHERE  id = :id
    //     ");
    //     $ancora->bindValue(':id', $this->getId());
    //     try {
    //         return $ancora->execute();
    //     } catch (Exception $retorno) {
    //         return $retorno->getMessage();
    //     }
    // }
    // public function Select()
    // {
    //     $ancora = $this->pdo->prepare("SELECT * FROM ancora
    //         WHERE  id = :id
    //     ");
    //     $ancora->bindValue(':id', $this->getId());
    //     try {
    //         $ancora->execute();
    //         return $ancora->fetchAll();
    //     } catch (Exception $retorno) {
    //         return $retorno->getMessage();
    //     }
    // }
    // public function SelectAll()
    // {
    //     $ancora = $this->pdo->prepare("SELECT * FROM ancora ");
    //     try {
    //         $ancora->execute();
    //         return $ancora->fetchAll(PDO::FETCH_ASSOC);
    //     } catch (Exception $retorno) {
    //         return $retorno->getMessage();
    //     }
    // }


    public function CheckToken()
    {
        $ancora = $this->pdo->prepare("SELECT * FROM token_users
            WHERE  client_id = :client_id and secret_id =:secret_id
        ");
        $ancora->bindValue(':client_id', $this->getClientId());
        $ancora->bindValue(':secret_id', $this->getSecretId());
        
        try {
            $ancora->execute();
            return $ancora->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
}
