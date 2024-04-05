<?php
require_once 'ConexaoMysql.php';
class antecipadas extends ConexaoMysql
{
    public $id;
    public $fornecedor;
    public $data;
    public $valororiginal;
    public $descontojuros;
    public $descontotaxas;
    public $valor;
    public $status;
    public $tipo;
    public $confirmada;
    public function getId()
    {
        return $this->id;
    }
    public function getFornecedor()
    {
        return $this->fornecedor;
    }
    public function getData()
    {
        return $this->data;
    }
    public function getValororiginal()
    {
        return $this->valororiginal;
    }
    public function getDescontojuros()
    {
        return $this->descontojuros;
    }
    public function getDescontotaxas()
    {
        return $this->descontotaxas;
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
    public function setFornecedor($fornecedor)
    {
        $this->fornecedor = $fornecedor;
    }
    public function setData($data)
    {
        $this->data = $data;
    }
    public function setValororiginal($valororiginal)
    {
        $this->valororiginal = $valororiginal;
    }
    public function setDescontojuros($descontojuros)
    {
        $this->descontojuros = $descontojuros;
    }
    public function setDescontotaxas($descontotaxas)
    {
        $this->descontotaxas = $descontotaxas;
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
        $antecipadas = $this->pdo->prepare("INSERT INTO antecipadas (
            id,
            fornecedor,
            data,
            valorOriginal,
            descontoJuros,
            descontoTaxas,
            valor,
            status,
            tipo,
            confirmada,
        ) VALUES (
            :id,
            :fornecedor,
            :data,
            :valororiginal,
            :descontojuros,
            :descontotaxas,
            :valor,
            :status,
            :tipo,
            :confirmada
        )");
        $antecipadas->bindValue(':id', $this->getId());
        $antecipadas->bindValue(':fornecedor', $this->getFornecedor());
        $antecipadas->bindValue(':data', $this->getData());
        $antecipadas->bindValue(':valororiginal', $this->getValororiginal());
        $antecipadas->bindValue(':descontojuros', $this->getDescontojuros());
        $antecipadas->bindValue(':descontotaxas', $this->getDescontotaxas());
        $antecipadas->bindValue(':valor', $this->getValor());
        $antecipadas->bindValue(':status', $this->getStatus());
        $antecipadas->bindValue(':tipo', $this->getTipo());
        $antecipadas->bindValue(':confirmada', $this->getConfirmada());
        try {
            return $antecipadas->execute();
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function Update()
    {
        $antecipadas = $this->pdo->prepare("UPDATE antecipadas SET
            data = :data,
            valorOriginal = :valororiginal,
            descontoJuros = :descontojuros,
            descontoTaxas = :descontotaxas,
            valor = :valor,
            status = :status,
            tipo = :tipo,
            confirmada = :confirmada
        WHERE  id = :id
        ");
        $antecipadas->bindValue(':id', $this->getId());
        $antecipadas->bindValue(':fornecedor', $this->getFornecedor());
        $antecipadas->bindValue(':data', $this->getData());
        $antecipadas->bindValue(':valororiginal', $this->getValororiginal());
        $antecipadas->bindValue(':descontojuros', $this->getDescontojuros());
        $antecipadas->bindValue(':descontotaxas', $this->getDescontotaxas());
        $antecipadas->bindValue(':valor', $this->getValor());
        $antecipadas->bindValue(':status', $this->getStatus());
        $antecipadas->bindValue(':tipo', $this->getTipo());
        $antecipadas->bindValue(':confirmada', $this->getConfirmada());
        try {
            return $antecipadas->execute();
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function Delete()
    {
        $antecipadas = $this->pdo->prepare("DELETE FROM antecipadas
            WHERE  id = :id
        ");
        $antecipadas->bindValue(':id', $this->getId());
        try {
            return $antecipadas->execute();
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function Select()
    {
        $antecipadas = $this->pdo->prepare("SELECT * FROM antecipadas
            WHERE  id = :id
        ");
        $antecipadas->bindValue(':id', $this->getId());
        try {
            $antecipadas->execute();
            return $antecipadas->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }

    public function Total_Antecipado()
    {
        $antecipadas = $this->pdo->prepare("select distinct sum(antecipadas.descontoJuros) as juros, sum(antecipadas.descontoTaxas) as taxas from antecipadas
         where antecipadas.id in (select antecipadasDetalhes.antecipada from antecipadasDetalhes where antecipadasDetalhes.operacao in (select id from operacoes where status = 5))
         group by antecipadas.id");
        try {
            $antecipadas->execute();
            return $antecipadas->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function Total_AntecipadoAncora()
    {
        $antecipadas = $this->pdo->prepare("select distinct sum(antecipadasDetalhes.valorOriginal) as valor from antecipadas
        left join antecipadasDetalhes on antecipadasDetalhes.antecipada = antecipadas.id
        inner join operacoes on operacoes.id = antecipadasDetalhes.operacao
          and operacoes.status = 5");
        try {
            $antecipadas->execute();
            return $antecipadas->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function Total_Operado()
    {
        $antecipadas = $this->pdo->prepare("SELECT antecipadas.descontoJuros as juros FROM antecipadas 
        left join antecipadasDetalhes on antecipadasDetalhes.antecipada = antecipadas.id
        inner join operacoes on operacoes.id = antecipadasDetalhes.operacao and operacoes.status = 5 group by antecipadas.id");
        try {
            $antecipadas->execute();
            return $antecipadas->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function Total_Taxas()
    {
        $antecipadas = $this->pdo->prepare("SELECT descontoTaxas as taxas FROM antecipadas
        left join antecipadasDetalhes on antecipadasDetalhes.antecipada = antecipadas.id
        inner join operacoes on operacoes.id = antecipadasDetalhes.operacao
        and operacoes.status = 5
        group by antecipadas.id");
        try {
            $antecipadas->execute();
            return $antecipadas->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    //Fluxo Disponível
    public function Total_OperadoFluxoDisponivel()
    {
        $antecipadas = $this->pdo->prepare("SELECT antecipadas.valor as valor FROM antecipadas 
         inner join antecipadasDetalhes on antecipadasDetalhes.antecipada = antecipadas.id 
         inner join operacoes on antecipadasDetalhes.operacao = operacoes.id and operacoes.confirmada = 1 and operacoes.status = 5
         inner join boletos on boletos.operacao = antecipadasDetalhes.operacao
         group by antecipadas.id
         ");
        try {
            $antecipadas->execute();
            return $antecipadas->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function Total_OperadoMes()
    {
        $antecipadas = $this->pdo->prepare("SELECT SUM(antecipadasDetalhes.valorOriginal) as valor,MAX(antecipadasDetalhes.antecipada) as antecipada FROM antecipadas
        left join antecipadasDetalhes on antecipadasDetalhes.antecipada = antecipadas.id
        inner join operacoes on operacoes.id = antecipadasDetalhes.operacao
        and operacoes.status =5 and  operacoes.confirmada = 1");
        try {
            $antecipadas->execute();
            return $antecipadas->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function Total_OperadoMesAllrow()
    {
        $antecipadas = $this->pdo->prepare("SELECT * FROM antecipadas
        left join antecipadasDetalhes on antecipadasDetalhes.antecipada = antecipadas.id
        inner join operacoes on operacoes.id = antecipadasDetalhes.operacao
        and operacoes.status =5 and  operacoes.confirmada = 1");
        try {
            $antecipadas->execute();
            return $antecipadas->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function Count()
    {
        $antecipadas = $this->pdo->prepare("SELECT count(*) as antecipadas FROM antecipadas");
        try {
            $antecipadas->execute();
            return $antecipadas->fetch();
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function CountConfirmadas()
    {
        $antecipadas = $this->pdo->prepare("SELECT count(*) as antecipadas FROM antecipadas
        left join antecipadasDetalhes on antecipadasDetalhes.antecipada = antecipadas.id
        inner join operacoes on operacoes.id = antecipadasDetalhes.operacao
        and operacoes.status =5 and  operacoes.confirmada = 1");
        try {
            $antecipadas->execute();
            return $antecipadas->fetch();
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function TotalAntecipadasFornecedor()
    {
        $antecipadas = $this->pdo->prepare("SELECT SUM(valor) as valor FROM antecipadas WHERE fornecedor = :fornecedor");
        $antecipadas->bindValue(':fornecedor', $this->getFornecedor());

        try {
            $antecipadas->execute();
            return $antecipadas->fetch();
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }

    public function AntecipadasFornecedor()
    {
        $antecipadas = $this->pdo->prepare("SELECT * FROM antecipadas WHERE fornecedor = :fornecedor ORDER BY data DESC");
        $antecipadas->bindValue(':fornecedor', $this->getFornecedor());

        try {
            $antecipadas->execute();
            return $antecipadas->fetchAll();
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function Antecipadas()
    {
        $antecipadas = $this->pdo->prepare("SELECT antecipadas.*
        FROM antecipadas 
        LEFT JOIN antecipadasDetalhes ON antecipadasDetalhes.antecipada = antecipadas.id
        INNER JOIN operacoes ON operacoes.id = antecipadasDetalhes.operacao AND operacoes.confirmada = 1
        GROUP BY antecipadas.id, antecipadas.fornecedor, antecipadas.data, antecipadas.valorOriginal,antecipadas.descontoJuros,antecipadas.descontoTaxas,antecipadas.valor,antecipadas.status,antecipadas.tipo,antecipadas.confirmada
        ORDER BY antecipadas.valor DESC 
        LIMIT 4;");

        try {
            $antecipadas->execute();
            return $antecipadas->fetchAll();
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function SumValor_Total_Antecipadas()
    {
        $antecipadas = $this->pdo->prepare("SELECT SUM(valor) as valor FROM antecipadas ");
        try {
            $antecipadas->execute();
            return $antecipadas->fetch();
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function SumJuros_Total_Antecipadas()
    {
        $antecipadas = $this->pdo->prepare("SELECT SUM(descontoJuros) as valor FROM antecipadas");
        try {
            $antecipadas->execute();
            return $antecipadas->fetch();
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function SumTaxas_Total_Antecipadas()
    {
        $antecipadas = $this->pdo->prepare("SELECT SUM(descontoTaxas) as valor FROM antecipadas");
        try {
            $antecipadas->execute();
            return $antecipadas->fetch();
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function SumValorOriginal_Total_Antecipadas()
    {
        $antecipadas = $this->pdo->prepare("SELECT SUM(valorOriginal) as valor FROM antecipadas WHERE fornecedor = :fornecedor");
        $antecipadas->bindValue(':fornecedor', $this->getFornecedor());

        try {
            $antecipadas->execute();
            return $antecipadas->fetch();
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }

    //modo cliente
    public function totaldupliByFornecedor()
    {
        $antecipadas = $this->pdo->prepare("SELECT count(*) as totaldupli FROM antecipadas as a, antecipadasDetalhes as ad WHERE a.fornecedor = :fornecedor AND ad.antecipada = a.id");
        $antecipadas->bindValue(':fornecedor', $this->getFornecedor());

        try {
            $antecipadas->execute();
            return $antecipadas->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }

    public function Sum_descontoTaxasFornecedor()
    {
        $antecipadas = $this->pdo->prepare("SELECT SUM(descontoTaxas) as valor FROM antecipadas WHERE fornecedor = :fornecedor");
        $antecipadas->bindValue(':fornecedor', $this->getFornecedor());

        try {
            $antecipadas->execute();
            return $antecipadas->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }

    public function Sum_jurosTaxasFornecedor()
    {
        $antecipadas = $this->pdo->prepare("SELECT SUM(descontoJuros) as valor FROM antecipadas WHERE fornecedor = :fornecedor");
        $antecipadas->bindValue(':fornecedor', $this->getFornecedor());

        try {
            $antecipadas->execute();
            return $antecipadas->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }
    public function totaldupli()
    {
        $antecipadas = $this->pdo->prepare("SELECT count(*) as totaldupli FROM antecipadas as a, antecipadasDetalhes as ad WHERE a.fornecedor = :fornecedor AND ad.antecipada = :id");
        $antecipadas->bindValue(':fornecedor', $this->getFornecedor());
        $antecipadas->bindValue(':id', $this->getId());

        try {
            $antecipadas->execute();
            return $antecipadas->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }

    public function antecipadasDetalhes()
    {
        $antecipadas = $this->pdo->prepare("SELECT operacoes.status as stat FROM antecipadas as a, antecipadasDetalhes as ad left join operacoes  on operacoes.id = ad.operacao WHERE a.fornecedor = :fornecedor AND ad.antecipada = :id");
        $antecipadas->bindValue(':fornecedor', $this->getFornecedor());
        $antecipadas->bindValue(':id', $this->getId());

        try {
            $antecipadas->execute();
            return $antecipadas->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $retorno) {
            return $retorno->getMessage();
        }
    }

    public function antecipadas_Boleto_Data($page = 1, $perPage = 10)
    {
        // Calcula o offset com base no número da página e no número de resultados por página
        $offset = ($page - 1) * $perPage;
    
        // Prepara a consulta SQL com cláusula LIMIT e OFFSET para a paginação
        $antecipadas = $this->pdo->prepare("
            SELECT 
                a.`id`, 
                a.`fornecedor`, 
                a.`data`, 
                a.`valorOriginal`, 
                a.`descontoJuros`, 
                a.`descontoTaxas`, 
                a.`valor`, 
                a.`status`, 
                a.`tipo`, 
                a.`confirmada`, 
                ad.`operacao`, 
                ad.`data` AS 'data_detalhe', 
                ad.`valorOriginal` AS 'valorOriginal_detalhe', 
                ad.`descontoJuros` AS 'descontoJuros_detalhe', 
                ad.`valor` AS 'valor_detalhe',
                b.`id` AS 'id_boleto',
                b.`cnpj`,
                b.`vencimento`,
                b.`valor` AS 'valor_boleto',
                b.`operacao` AS 'operacao_boleto',
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
                f.`limite`,
                f.`juros`,
                f.`status` AS 'fornecedor_status',
                f.`clicksign_key`,
                f.`banco`,
                f.`conta`,
                f.`agencia`,
                f.`updated`,
                o.`nota`
            FROM 
                `antecipadas` AS a 
            JOIN 
                `antecipadasDetalhes` AS ad ON a.`id` = ad.`antecipada`
            JOIN
                `boletos` AS b ON ad.`operacao` = b.`operacao`
            LEFT JOIN
                `fornecedores` AS f ON a.`fornecedor` = f.`id`
            LEFT JOIN
                `operacoes` AS o ON b.`operacao` = o.`id`
            WHERE 
                a.data = :data AND o.confirmada = 1
            LIMIT :limit OFFSET :offset
        ");
        
        $antecipadas->bindValue(':data', $this->getData());
        $antecipadas->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $antecipadas->bindValue(':offset', $offset, PDO::PARAM_INT);
    
        try {
            $antecipadas->execute();
            return $antecipadas->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    
}
