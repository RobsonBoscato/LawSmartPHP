<?php
require_once 'ConexaoMysql.php';
class postergacoes extends ConexaoMysql
{
    public $id;
    public $data;
    public $valororiginal;
    public $juros;
    public $taxas;
    public $valor;
    public $status;
    public $tipo;
    public $confirmada;
    public function getId()
    {
        return $this->id;
    }
    public function getData()
    {
        return $this->data;
    }
    public function getValororiginal()
    {
        return $this->valororiginal;
    }
    public function getJuros()
    {
        return $this->juros;
    }
    public function getTaxas()
    {
        return $this->taxas;
    }
    public function getValor()
    {
        return $this->valor;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function getTipo()
    {
        return $this->tipo;
    }
    public function getConfirmada()
    {
        return $this->confirmada;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function setData($data)
    {
        $this->data = $data;
    }
    public function setValororiginal($valororiginal)
    {
        $this->valororiginal = $valororiginal;
    }
    public function setJuros($juros)
    {
        $this->juros = $juros;
    }
    public function setTaxas($taxas)
    {
        $this->taxas = $taxas;
    }
    public function setValor($valor)
    {
        $this->valor = $valor;
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }
    public function setConfirmada($confirmada)
    {
        $this->confirmada = $confirmada;
    }
    public function Insert()
    {
        $postergacoes = $this->pdo->prepare("INSERT INTO postergacoes (
            id,
            data,
            valorOriginal,
            juros,
            taxas,
            valor,
            status,
            tipo,
            confirmada,
        ) VALUES (
            :id,
            :data,
            :valororiginal,
            :juros,
            :taxas,
            :valor,
            :status,
            :tipo,
            :confirmada
        )");
        $postergacoes->bindValue(':id', $this->getId());
        $postergacoes->bindValue(':data', $this->getData());
        $postergacoes->bindValue(':valororiginal', $this->getValororiginal());
        $postergacoes->bindValue(':juros', $this->getJuros());
        $postergacoes->bindValue(':taxas', $this->getTaxas());
        $postergacoes->bindValue(':valor', $this->getValor());
        $postergacoes->bindValue(':status', $this->getStatus());
        $postergacoes->bindValue(':tipo', $this->getTipo());
        $postergacoes->bindValue(':confirmada', $this->getConfirmada());
        try {
            return $postergacoes->execute();
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function Update()
    {
        $postergacoes = $this->pdo->prepare("UPDATE postergacoes SET
            data = :data,
            valorOriginal = :valororiginal,
            juros = :juros,
            taxas = :taxas,
            valor = :valor,
            status = :status,
            tipo = :tipo,
            confirmada = :confirmada
        WHERE  id = :id
        ");
        $postergacoes->bindValue(':id', $this->getId());
        $postergacoes->bindValue(':data', $this->getData());
        $postergacoes->bindValue(':valororiginal', $this->getValororiginal());
        $postergacoes->bindValue(':juros', $this->getJuros());
        $postergacoes->bindValue(':taxas', $this->getTaxas());
        $postergacoes->bindValue(':valor', $this->getValor());
        $postergacoes->bindValue(':status', $this->getStatus());
        $postergacoes->bindValue(':tipo', $this->getTipo());
        $postergacoes->bindValue(':confirmada', $this->getConfirmada());
        try {
            return $postergacoes->execute();
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function Delete()
    {
        $postergacoes = $this->pdo->prepare("DELETE FROM postergacoes
            WHERE  id = :id
        ");
        $postergacoes->bindValue(':id', $this->getId());
        try {
            return $postergacoes->execute();
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function Select()
    {
        $postergacoes = $this->pdo->prepare("SELECT * FROM postergacoes
            WHERE  id = :id
        ");
        $postergacoes->bindValue(':id', $this->getId());
        try {
            $postergacoes->execute();
            return $postergacoes->fetchAll();
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function SelectId()
    {
        $postergacoes = $this->pdo->prepare("SELECT * FROM postergacoes
            WHERE  id = :id
        ");
        $postergacoes->bindValue(':id', $this->getId());
        try {
            $postergacoes->execute();
            return $postergacoes->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }

    public function postergadas_Boleto_Data()
    {
        $postergacoes = $this->pdo->prepare("SELECT 
        p.`id`, 
        p.`data`, 
        p.`valorOriginal`, 
        p.`juros`, 
        p.`taxas`, 
        p.`valor`, 
        p.`status`, 
        o.`tipo`, 
        o.`confirmada`, 
        pd.`id` AS 'id_detalhe', 
        pd.`id_postergacao` AS 'id_postergacao_detalhe', 
        pd.`id_operacao` AS 'id_operacao_detalhe', 
        pd.`valorOriginal` AS 'valorOriginal_detalhe', 
        pd.`juros` AS 'juros_detalhe', 
        pd.`taxas` AS 'taxas_detalhe', 
        pd.`valor` AS 'valor_detalhe',
        o.`nota`,
        b.`id` AS 'id_boleto',
        b.`cnpj`,
        b.`vencimento`,
        b.`valor` AS 'valor_boleto',
        b.`operacao` AS 'operacao_boleto',
        b.`documento`,
        b.`nosso_numero`,
        b.`nosso_numero_banco`,
        b.`nosso_numero_dac`,
        b.`emissao`,
        b.`registro`,
        b.`ocorrencia`,
        b.`data_ocorrencia`,
        b.`data_registro`,
        b.`remessa`,
        b.`retorno`,
        b.`tarifa`,
        b.`iof`,
        b.`abatimento`,
        b.`descontos`,
        b.`mora_multa`,
        b.`creditado`,
        b.`outros_creditos`,
        b.`cod_liquidacao`,
        b.`mensagem`,
        b.`status` AS 'status_boleto',
        f.`id` AS 'fornecedor_id',
        f.`razao`,
        f.`tipo` AS 'fornecedor_tipo',
        f.`cnpj` AS 'fornecedor_cnpj',
        f.`email`,
        f.`telefone`,
        f.`representante`,
        f.`cpf`,
        f.`rua`,
        f.`numero`,
        f.`bairro`,
        f.`cidade`,
        f.`estado`,
        f.`cep`,
        f.`boleto`,
        f.`ted`,
        f.`tac`,
        f.`limite` AS limiteCliente,
        COALESCE(p1.total_postergacoes, 0) AS total_postergacoes,
        (f.`limite` - COALESCE(p1.total_postergacoes, 0)) AS limite_utilizado,
        f.`juros`,
        f.`status` AS 'fornecedor_status',
        f.`clicksign_key`,
        f.`banco`,
        f.`conta`,
        f.`agencia`,
        f.`updated`
    FROM 
        `postergacoes` AS p
    JOIN 
        `postergacoesDetalhes` AS pd ON p.`id` = pd.`id_postergacao`
    JOIN
        `boletos` AS b ON pd.`id_operacao` = b.`operacao`
    LEFT JOIN
        `operacoes` AS o ON b.`operacao` = o.`id`
    LEFT JOIN
        `fornecedores` AS f ON o.`cnpj` = f.`cnpj`
    LEFT JOIN (
        SELECT 
            o.`cnpj`, 
            SUM(p.`valor`) AS total_postergacoes
        FROM 
            `postergacoes` AS p 
        JOIN 
            `postergacoesDetalhes` AS pd ON p.`id` = pd.`id_postergacao` 
        JOIN 
            `boletos` AS b ON pd.`id_operacao` = b.`operacao` 
        LEFT JOIN 
            `operacoes` AS o ON b.`operacao` = o.`id` 
        WHERE 
            p.`data` = :data 
            AND o.`confirmada` = 1 
        GROUP BY 
            o.`cnpj`
    ) AS p1 ON f.`cnpj` = p1.`cnpj`
    WHERE 
        p.`data` = :data 
        AND o.`confirmada` = 1;
    ");
        $postergacoes->bindValue(':data', $this->getData());
        try {
            $postergacoes->execute();
            return $postergacoes->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
}
