<?php
  session_start();
  if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] !== 'cliente') {
    header("Location: ../pagina_de_acesso_nao_autorizado.php");
    exit();
}
  setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
  date_default_timezone_set('America/Sao_Paulo');
  require_once('../Class/Config.php');
  $con = new mysqli(DB_HOUST, DB_USER,DB_PASS, DB_NAME);
	mysqli_set_charset($con, 'utf8');

  $cnpj = $_SESSION['cnpj']; // cnpj
  $dados = json_decode(file_get_contents('php://input'), true);
  $id = $dados['id']; //id antecipacao

	function prepared_query($mysqli, $sql, $params, $types = "") {    
    $stmt = $mysqli->prepare($sql);
		if (sizeof($params) > 0) {
			$types = $types ?: str_repeat("s", count($params));
			$stmt->bind_param($types, ...$params);
		}    
    $stmt->execute();
    return $stmt;
	}


  function formataCpfCnpj($str) {
    $ret = '';
    if (strlen($str) == 14) {
      $ret = substr($str, 0, 2).'.'.substr($str, 2, 3).'.'.substr($str, 5, 3).'/'.substr($str, 8, 4).'-'.substr($str, -2);
    } else {
      // if(substr($str, 8, 1) == )
      $ret = substr($str, 0, 3).'.'.substr($str, 3, 3).'.'.substr($str, 6, 3).'-'.substr($str, -2);
    }
    return $ret;
  }


function formataFone(?string $str): string {
  if ($str === null) {
      return ''; // ou outra lógica de tratamento para string nula
  }

  $localStr = str_replace(["(", ")", " "], '', $str);
  return '('.substr($localStr, 0, 2).') '.substr($localStr, 2, 5).'-'.substr($localStr, -4);
}

function formataCep(?string $str): string {
  if ($str === null) {
      return ''; // ou outra lógica de tratamento para string nula
  }

  return substr($str, 0, 2).'.'.substr($str, 2, 3).'-'.substr($str, -3);
}


  class Extenso {
    public static function removerFormatacaoNumero($strNumero) {
      $strNumero = trim(str_replace("R$", '', $strNumero));
      $vetVirgula = explode( ",", $strNumero );
        if ( count( $vetVirgula ) == 1 ) {
            $acentos = array(".");
            $resultado = str_replace( $acentos, "", $strNumero );
            return $resultado;
        } else if ( count( $vetVirgula ) != 2 ) {
            return $strNumero;
        }
        $strNumero = $vetVirgula[0];
        $strDecimal = mb_substr( $vetVirgula[1], 0, 2 );
        $acentos = array(".");
        $resultado = str_replace( $acentos, "", $strNumero );
        $resultado = $resultado . "." . $strDecimal;
        return $resultado;
    }

    public static function converte($valor = 0, $bolExibirMoeda = true, $bolPalavraFeminina = false) {
        $valor = self::removerFormatacaoNumero( $valor );
        $singular = null;
        $plural = null;
        if ( $bolExibirMoeda ) {
            $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
            $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
        } else {
            $singular = array("", "", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
            $plural = array("", "", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
        }
        $c = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
        $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
        $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezessete", "dezoito", "dezenove");
        $u = array("", "um", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
        if ( $bolPalavraFeminina ) {
            if ($valor == 1)
                $u = array("", "uma", "duas", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
            else
                $u = array("", "um", "duas", "três", "quatro", "cinco", "seis","sete", "oito", "nove");

            $c = array("", "cem", "duzentas", "trezentas", "quatrocentas","quinhentas", "seiscentas", "setecentas", "oitocentas", "novecentas");
        }
        $z = 0;
        $valor = number_format( $valor, 2, ".", "." );
        $inteiro = explode( ".", $valor );
        for ( $i = 0; $i < count( $inteiro ); $i++ )
            for ( $ii = mb_strlen( $inteiro[$i] ); $ii < 3; $ii++ )
                $inteiro[$i] = "0" . $inteiro[$i];

        // $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
        $rt = null;
        $fim = count( $inteiro ) - ($inteiro[count( $inteiro ) - 1] > 0 ? 1 : 2);
        for ( $i = 0; $i < count( $inteiro ); $i++ ) {
            $valor = $inteiro[$i];
            $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
            $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
            $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

            $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
            $t = count( $inteiro ) - 1 - $i;
            $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
            if ( $valor == "000")
                $z++;
            elseif ( $z > 0 )
                $z--;

            if ( ($t == 1) && ($z > 0) && ($inteiro[0] > 0) )
                $r .= ( ($z > 1) ? " de " : "") . $plural[$t];

            if ( $r )
                $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
        }
        $rt = mb_substr( $rt, 1 );
        return($rt ? trim( $rt ) : "zero");
    }
  }

  $sql = "SELECT * FROM `fornecedores` where cnpj=?";
  $stmt = prepared_query($con, $sql, [$cnpj], 's');
  $empresa = $stmt->get_result()->fetch_all(MYSQLI_ASSOC)[0];
  $stmt->close();

  $prepared_id = $con->real_escape_string($id);
  // echo "RPREPARED IS SELECT * FROM operacoes left join boletos on boletos.operacao = operacoes.id WHERE operacoes.id IN ($prepared_id)";
  $operacao = $con->query("SELECT *, operacoes.id as id_oper FROM operacoes left join boletos on boletos.operacao = operacoes.id WHERE operacoes.id IN ($prepared_id)");
  // echo "OPERACAO RESULT IS".var_dump($operacao[0]);
  // $stmt->close(); 
    

  function fator_vencimento($data) {
    $data = explode("-",$data);
    $ano = $data[0];
    $mes = $data[1];
    $dia = $data[2];
      return(abs((_dateToDays("1997","10","07")) - (_dateToDays($ano, $mes, $dia))));
  }
  
  function _dateToDays($year,$month,$day) {
      $century = substr($year, 0, 2);
      $year = substr($year, 2, 2);
      if ($month > 2) {
          $month -= 3;
      } else {
          $month += 9;
          if ($year) {
              $year--;
          } else {
              $year = 99;
              $century --;
          }
      }
      return ( floor((  146097 * $century)    /  4 ) +
              floor(( 1461 * $year)        /  4 ) +
              floor(( 153 * $month +  2) /  5 ) +
                  $day +  1721119);
  }

  $trs = '';
  $trs_completo = '';
  $vlrtot = 0;
  // 677777 "ALL OPERACOES".var_dump($operacao);
  while ($op = $operacao->fetch_assoc()) {
    // echo "ANTECIP IS".var_dump($op);
    $doc = $op['nota'];
    // echo "SELECT * FROM operacoes left join boletos on boletos.operacao = operacoes.id WHERE operacoes.nota = $doc and operacoes.id != {$op['id_oper']}";
    $operacao_reference_query = $con->query("SELECT * FROM operacoes WHERE operacoes.nota like '{$doc}' and operacoes.id != {$op['id_oper']}");
    $operacao_reference = $operacao_reference_query->fetch_assoc();
    // echo "REFERENCE IS".var_dump($operacao_reference);
    $trs .= '
      <tr>
        <td class="center">'.$op['nota'].'</td>
        <td class="left">'.$empresa['razao'].'</td>
        <td class="center">'.formataCpfCnpj($empresa['cnpj']).'</td>
        <td class="center">'.date_format(date_create($operacao_reference['vencimento']), 'd-m-Y').'</td>
        <td class="right">R$ '.number_format($operacao_reference['valor'], 2, ',', '.').'</td>
        
      </tr>
    ';
    
    $vlrtot += $operacao_reference['valor'];

    $trs_completo .= '
      <tr>
        <td class="center">'.$op['nota'].'</td>
        <td class="left">'.$empresa['razao'].'</td>
        <td class="center">'.formataCpfCnpj($empresa['cnpj']).'</td>
        <td class="right">R$ '.number_format($operacao_reference['valor'], 2, ',', '.').'</td>
        <td class="center">'.date_format(date_create($operacao_reference['vencimento']), 'd-m-Y').'</td>
        <td class="right">R$ '.number_format($op['valor'], 2, ',', '.').'</td>
        <td class="center">'.date_format(date_create($op['vencimento']), 'd-m-Y').'</td>
      </tr>
    ';
  }
  // echo "VLR TOT IS".$vlrtot;
  $table = '
  <table>
    <thead>
      <tr>
        <td>DOCUMENTO</td>
        <td>DEVEDOR-SACADO</td>
        <td>CNPJ/CPF</td>
        <td>VENCIMENTO</td>
        <td>VALOR DE FACE</td>
      </tr>
    </thead>
    <tbody>
    '.$trs.'
    </tbody>
  </table>
  ';


  $table_completo = '
  <table>
    <thead>
      <tr>
        <td>DOCUMENTO</td>
        <td>DEVEDOR-SACADO</td>
        <td>CNPJ/CPF</td>
        <td>VALOR DE FACE</td>
        <td>VENCIMENTO ORIGINAL</td>
        <td>VALOR PRORROGADO</td>
        <td>VENCIMENTO PRORROGADO</td>
      </tr>
    </thead>
    <tbody>
    '.$trs_completo.'
    </tbody>
  </table>
  ';

  $html = '
  <h3>INSTRUMENTO PARTICULAR DE CESSÃO DE DIREITOS CREDITÓRIOS EM RAZÃO DE PAGAMENTO POR SUB-ROGAÇÃO CONVENCIONAL E OUTRAS AVENÇAS</h3>
  <p>
    <span class="under">CEDENTE SUB-ROGANTE:</span> LAWSEC S/A., pessoa jurídica de direito privado, devidamente inscrita perante o CNPJ/MF sob o nº 32.527.198/0001-51, com sede e foro na Rua Jorge Czerniewicz, nº 99, CEP 89.255-072, Jaraguá do Sul – Estado de Santa Catarina.
    <br><span class="bold">Representante Legal:</span> Gilberto Eichenberg
    <br><span class="bold">RG</span> nº 4.153.267/SESP/SC
    <br><span class="bold">CPF/MF</span> nº 051.603.129-51
    <br><span class="bold">Estado Civil:</span> Solteiro
    <br><span class="bold">Nacionalidade:</span> Brasileiro
    <br><span class="bold">Profissão:</span> Empresário
    <br><span class="bold">Endereço:</span> Rua José Pomianowski, nº 163, Bairro Chico de Paulo, CEP 89.254-810, Jaraguá do Sul – Estado de Santa Catarina.
    <br><span class="bold">Telefone:</span> (47) 98445-2133
    <br><span class="bold">e-mail:</span> gilberto@lawsecsa.com.br
    <br>doravante simplesmente denominado de CEDENTE SUB-ROGANTE. 
  </p>
  <p>
    <span class="under">CESSIONÁRIO SUB-ROGADO:</span> LAWSEC S/A., pessoa jurídica de direito privado, devidamente inscrita perante o CNPJ/MF sob o nº 32.527.198/0001-51, com sede e foro na Rua Jorge Czerniewicz, nº 99, CEP 89.255-072, Jaraguá do Sul – Estado de Santa Catarina.
    <br><span class="bold">Representante Legal:</span> Gilberto Eichenberg
    <br><span class="bold">RG</span> nº 4.153.267/SESP/SC
    <br><span class="bold">CPF/MF</span> nº 051.603.129-51
    <br><span class="bold">Estado Civil:</span> Solteiro
    <br><span class="bold">Nacionalidade:</span> Brasileiro
    <br><span class="bold">Profissão:</span> Empresário
    <br><span class="bold">Endereço:</span> Rua José Pomianowski, nº 163, Bairro Chico de Paulo, CEP 89.254-810, Jaraguá do Sul – Estado de Santa Catarina.
    <br><span class="bold">Telefone:</span> (47) 98445-2133
    <br><span class="bold">e-mail:</span> gilberto@lawsecsa.com.br
    <br>doravante simplesmente denominado de CESSIONÁRIO SUB-ROGADO. 
  </p>
  <p>
    <span class="under">DEVEDOR ANUENTE:</span> '.$empresa["razao"].', pessoa jurídica de direito privado, inscrita no CNPJ/MF sob o nº '.formataCpfCnpj($empresa["cnpj"]).', com sede '.$empresa["rua"].', '.$empresa["numero"].'- '.$empresa["estado"].', CEP '.formataCep($empresa["cep"]).'
    <br><span class="bold">Representante Legal:</span> '.$empresa["representante"].'
    <br><span class="bold">CPF/MF</span> nº '.formataCpfCnpj($empresa["cpf"]).'
    <br><span class="bold">Estado Civil:</span>  '.$empresa["rua"].', '.$empresa["numero"].'- '.$empresa["estado"].', CEP '.formataCep($empresa["cep"]).'
    <br><span class="bold">Nacionalidade:</span> '.formataFone($empresa["telefone"]).'
    <br><span class="bold">Profissão:</span> '.$empresa["email"].'
    <br><span class="bold">Endereço:</span>  '.$empresa["rua"].', '.$empresa["numero"].'- '.$empresa["estado"].', CEP '.formataCep($empresa["cep"]).'
    <br><span class="bold">Telefone:</span> '.formataFone($empresa["telefone"]).'
    <br><span class="bold">e-mail:</span> '.$empresa["email"].'
    <br>doravante simplesmente denominado de DEVEDOR ANUENTE. 
  </p>
  <p>
    <span class="under">INTERVENIENTE RESPONSÁVEL(IS) SOLIDÁRIO(S):</span>
    <br><span class="bold">Nome:</span> '.$empresa["representante"].'
    <br><span class="bold">CPF/MF</span> nº '.formataCpfCnpj($empresa["cpf"]).'
    <br><span class="bold">Endereço:</span> '.$empresa["rua"].', '.$empresa["numero"].'- '.$empresa["estado"].', CEP '.formataCep($empresa["cep"]).'
    <br><span class="bold">Telefone:</span> '.formataFone($empresa["telefone"]).'
    <br><span class="bold">e-mail:</span> '.$empresa["email"].'
    <br>doravante simplesmente denominado de INTERVENIENTE RESPONSÁVEL(IS) SOLIDÁRIO(S) ou apenas RESPONSÁVEL(IS) SOLIDÁRIO(S) 
  </p>
  <p>
    <span class="under">INTERVENIENTE FIEL DEPOSITÁRIO:</span>
    <br><span class="bold">Nome:</span> '.$empresa["representante"].'
    <br><span class="bold">CPF/MF</span> nº '.formataCpfCnpj($empresa["cpf"]).'
    <br><span class="bold">Endereço:</span> '.$empresa["rua"].', '.$empresa["numero"].'- '.$empresa["estado"].', CEP '.formataCep($empresa["cep"]).'
    <br><span class="bold">Telefone:</span> '.formataFone($empresa["telefone"]).'
    <br><span class="bold">e-mail:</span> '.$empresa["email"].'
    <br>doravante simplesmente denominado de INTERVENIENTE FIEL DEPOSITÁRIO ou apenas FIEL DEPOSITÁRIO.
  </p>

  <p>
    Considerando que o CEDENTE SUB-ROGANTE, é único, exclusivo e legítimo titular dos créditos, identificados e descritos no quadro constante da cláusula 1.1, bem como de todos os direitos acessórios aos créditos, incluindo multa(s), juros remuneratórios, encargos moratórios, correção monetária, e toda e qualquer garantia, real ou pessoal ou fiduciária, ainda existentes, que garanta, total ou parcialmente, o seu pagamento; 
  </p>
  <p>
    Considerando que a DEVEDORA ANUENTE deseja repactuar o prazo de vencimento dos títulos de crédito devidos ao CEDENTE SUB-ROGANTE;
  </p>
  <p>
    Considerando que a CEDENTE SUB-ROGANTE não possui condições de flexibilizar novo prazo de vencimento para os títulos de crédito devidos pela DEVEDORA ANUENTE;
  </p>
  <p>
    Considerando que a DEVEDORA ANUENTE declara e expressa que recebeu integralmente todos os produtos descritos nas respectivas notas fiscais que deram origem aos títulos de crédito identificados e descritos no quadro constante da cláusula 1.1,  razão pela qual reconhece para todos os efeitos jurídicos, ser devedora da CEDENTE SUB-ROGANTE na importância financeira expressa nestes títulos; 
  </p>
  <p>
  Considerando que a DEVEDORA ANUENTE anui e concorda com o pagamento por sub-rogação convencional que está sendo realizado pelo CESSIONÁRIO SUB-ROGADO, declarando-se obrigada ao pagamento dos títulos de crédito identificados e descritos no quadro constante da cláusula 1.1, somente em favor do CESSIONÁRIO SUB-ROGADO;
  </p>
  <p>
  Considerando que o CESSIONÁRIO SUB-ROGADO realiza o pagamento dos títulos de crédito identificados e descritos no quadro constante da cláusula 1.1 em favor do CEDENTE SUB-ROGANTE na forma art. 347, inciso I do Código Civil, sub-rogando-se no valor pago, acrescido de todos os direitos pactuados no presente instrumento e eventuais termos aditivos;
  </p>
  <p>
    <span class="under">1. DO PAGAMENTO POR SUB-ROGAÇÃO CONVENCIONAL</span>
    <br><br>
    1.1 O presente instrumento particular tem como objeto a formalização da transferência ao CESSIONÁRIO SUB-ROGADO, de todos os direitos de crédito e de cobrança dos títulos identificados e descritos no quadro constante desta cláusula 1.1, que o CEDENTE SUB-ROGANTE possuiu em face da DEVEDORA ANUENTE, sacado pelo CEDENTE SUB-ROGANTE em face do DEVEDOR ANUENTE, transferência está ocorrida na forma do art. 347, inciso I do Código Civil por ocasião do pagamento realizado pelo CESSIONÁRIO SUB-ROGADO em favor do CEDENTE SUB-ROGANTE.
    <br><br>
    '.$table.'
    <br>
  </p>
  <p>
    1.2 Por ocasião do presente instrumento particular, a CEDENTE SUB-ROGANTE declarada o recebimento da importância de R$ '.number_format($vlrtot, 2, ',', '.').' ('.Extenso::converte(str_replace('.', ',', $vlrtot), true, false).'), outorgando ao CESSIONÁRIO SUB-ROGADO, por sua vez, a mais ampla, irrevogável e total quitação, para nada mais reclamar a que tempo for.
  </p>
  <p>
    1.2.1 Todas as eventuais despesas de registro deste Instrumento Particular de Pagamento por Sub-rogação, que eventualmente necessitem ser realizadas, serão suportadas exclusivamente pela da DEVEDORA ANUENTE.
  </p>
  <p>
    1.2.2 Na eventualidade do CESSIONÁRIO SUB-ROGADO suportar as despesas descritas no caput, este poderá exigir o ressarcimento das despesas juntamente com o valor da dívida objeto da presente sub-rogação.
  </p>
  <p>
    1.3 Declara a CEDENTE SUB-ROGANTE que os créditos representados pelos títulos identificados e descritos no quadro constante da cláusula 1.1, estão livres de quaisquer ônus ou gravames de qualquer natureza, responsabilizando-se civil e criminalmente pela existência, legalidade, legitimidade e veracidade dos créditos, declarando-se, ainda, FIEL DEPOSITÁRIO ser responsável pela guarda dos mesmos e apresentá-los quando requisitados por escrito pela CESSIONÁRIO SUB-ROGADO, no prazo de 48 (quarenta e oito) horas contados da solicitação – sob pena de incorrer nas penalidades legalmente cabíveis, observando, sempre, o disposto no artigo 638 do Código Civil, o artigo 168 do Código Penal, e o art. 5.º, LXII, da Constituição Federal.
  </p>
  <p>
    <span class="bold under">2. DA CESSÃO DO(S) DIREITO(S)_ CREDITÓRIO(S) SUB-ROGADO(S)</span>
    <br><br>
    2.1 Face o pagamento descrito na cláusula 1.2, o CEDENTE SUB-ROGANTE, por meio do presente instrumento, cede e transfere ao CESSIONÁRIO SUB-ROGADO, enquanto vigente e nos limites deste contrato, os Títulos de Crédito a seguir listados, incluindo seus acessórios, bem como todos os instrumentos que os representam, inclusive notas fiscais eletrônicas de venda de mercadoria e/ou prestação dos serviços originários dos créditos e os respectivos comprovantes da entrega da mercadoria e/ou prestação de serviços, bem assim, como os eventuais anexos e garantias constituídas, como de fato tem cedido e transferido, sub-rogando todos os seus direitos, inalterados, ao CESSIONÁRIO-SUB-ROGADO.
  </p>
  <p>
    2.2 A DEVEDORA ANUENTE, por sua vez, outorga ao presente instrumento sua expressa anuência, concordância e principalmente o reconhecimento da dívida, obrigando-se a proceder com o pagamento devido somente e diretamente ao CESSIONÁRIO SUB-ROGADO.
  </p>
  <p>
    2.3 Os créditos mencionados e listados no item 1.1 acima, estão sendo endossados pela CEDENTE SUB-ROGANTE em favor da CESSIONÁRIO SUB-ROGADO, mediante endosso pleno.
  </p>
  <p>
    2.4 Declara, ainda, a CEDENTE SUB-ROGANTE, com relação aos créditos cedidos nos termos deste contrato e que são objeto de securitização, que:
    <br><br>
    <span class="bold">(i)</span> Os títulos de créditos ora cedidos não foram objeto de qualquer outra alienação, compromisso de alienação, cessão ou mesmo oneração, inexistindo qualquer direito do devedor-sacado, ora DEVEDORA ANUENTE, contra a CEDENTE SUB-ROGANTE ou qualquer acordo, transação e/ou novação entre o CEDENTE SUB-ROGANTE e o DEVEDOR ANUENTE-sacado (ou terceiros) que possa ensejar qualquer arguição de compensação e/ou outra forma de extinção, redução ou modificação das condições de pagamento e valor dos créditos cedidos ao CESSIONÁRIO SUB-ROGADO.
    <br><br>
    <span class="bold">(ii)</span> Os títulos negociados também poderão ser emitidos, endossados e avalizados eletronicamente, independentemente de serem ou não produzidos com a utilização de processo de certificação disponibilizado pela ICP-Brasil (Infra-Estrutura de Chaves Públicas) na forma do § 2º, art. 10, da MP 2.200-2 , assim como a nota fiscal poderá ser enviada em arquivo XML, independentemente de serem ou não produzidos com a utilização de processo de certificação disponibilizado pela ICP-Brasil.
  </p>
  <p>
    2.5 O(s) INTERVENIENTE(S) RESPONSÁVEL(EIS) SOLIDÁRIO(S) responsabilizam-se perante o CESSIONÁRIO SUB-ROGADO, pelos riscos e prejuízos que possam advir dos créditos e/ou títulos negociados, inclusive pela solvência do devedor-sacado, ora DEVEDOR ANUENTE e pela boa liquidação e pagamento do crédito, caso ele não seja efetuado na data de seu vencimento, bem como na hipótese de serem opostas quaisquer exceções quanto à legitimidade, legalidade e veracidade do crédito.
  </p>
  <p>
    2.6 O(s) INTERVENIENTE(S) RESPONSÁVEL(EIS) SOLIDÁRIO(S) também respondem integralmente junto ao CESSIONÁRIO SUB-ROGADO pelos créditos negociados, e pelas obrigações decorrentes do endosso realizado, nas seguintes situações:
    <br><br>
    <span class="bold">(i)</span> Se os créditos representados pelos títulos forem objeto de outra cessão, alienação, ajuste ou oneração, sem o consentimento prévio e expresso do CESSIONÁRIO SUB-ROGADO;
    <br><br>
    <span class="bold">(ii)</span> Se os créditos adquiridos pelo CESSIONÁRIO SUB-ROGADO forem objeto de eventual acordo entre a CEDENTE-SUB-ROGANTE e o DEVEDOR ANUENTE, que possa ensejar arguição ou compensação e/ou qualquer outra forma de redução, extinção ou modificação de qualquer das condições que interfiram ou prejudiquem um dos direitos decorrentes dos títulos negociados;
    <br><br>
    <span class="bold">(iii)</span> Se o DEVEDOR ANUENTE promover qualquer alteração nos seus atos constitutivos (do contrato social, estatuto) ou mudança de endereço sem conhecimento prévio do CESSIONÁRIO SUB-ROGADO;
    <br><br>
    <span class="bold">(iv)</span> Se o DEVEDOR ANUENTE for judicialmente reconhecido como insolvente, ou falido;
    <br><br>
    <span class="bold">(iv)</span> Se o DEVEDOR ANUENTE realizar o pagamento à terceiro, no todo ou em parte, de valores relativos aos créditos e/ou títulos objeto do presente instrumento. 
    <br><br>
    <span class="bold">(vi)</span> Se for oposta qualquer exceção, oposição, defesa ou justificativa pelo DEVEDOR ANUENTE baseado em fato de responsabilidade ou contrária aos termos deste contrato;
    <br><br>
    <span class="bold">(vii)</span> Se houver contraprotesto do DEVEDOR ANUENTE e/ou qualquer reclamação judicial deste contra a CEDENTE SUB-ROGANTE; ou, ainda;
    <br><br>
    <span class="bold">(viii)</span> Em caso de inadimplemento baseado em alegação de caso fortuito ou força maior.
  </p>
  <p>
    2.7 Sobrevindo a constatação de não pagamento pelo DEVEDOR ANUENTE no vencimento ou de quaisquer vícios ou exceções na origem dos créditos e/ou títulos que os representam os títulos negociados entre as partes, obrigam-se o(s) INTERVENIENTE(S) RESPONSÁVEL(EIS) SOLIDÁRIO(S), realizar o pagamento em favor do CESSIONÁRIO SUB-ROGADO, no prazo de 48 (quarenta e oito) horas, contados da comunicação do evento pelo CESSIONÁRIO,SUB-ROGADO pelo valor de face do título negociado, acrescido da multa de 0,33% (zero vírgula trinta e três por cento), limitada a 10% (dez por cento), juros de mora de 1% (um por cento) ao mês, nos termos do art. 406 do Código Civil, bem como da devida atualização monetária segundo índices oficiais – INPC (Índice Nacional de Preço ao Consumidor) regularmente estabelecidos, das perdas e danos e honorários de advogado na ordem de 20% do saldo devedor, tudo conforme autorizam os artigos 389 ao 392 e 394 ao 396 do Código Civil.
  </p>
  <p>
    2.8 O não pagamento dos créditos e/ou títulos no prazo previsto no item 2.8 acima, acarretará negativação, apontamento dos títulos para protesto e a imediata exigibilidade dos créditos, ensejando a cobrança judicial contra o DEVEDOR ANUENTE e INTERVENIENTE(S)RESPONSÁVEL(EIS) SOLIDÁRIO(S) dos créditos e/ou títulos não pago(s).
  </p>
  <p>
    2.9 A tolerância do CESSIONÁRIO SUB-ROGADO quanto ao disposto no item 2.8, constituirá ato de mera liberalidade, não implicando, tácita ou implicitamente, em renúncia ou novação quanto às obrigações previstas.
  </p>
  <p>
    2.10 No caso do CESSIONÁRIO SUB-ROGADO acionar judicialmente o DEVEDOR ANUENTE em decorrência da inadimplência, assim como nos casos previstos no item 2.8, obrigam-se os INTERVENIENTE(S) RESPONSÁVEL(EIS) SOLIDÁRIO(S),  a reembolsar na integralidade, com todos os acréscimos legais, o valor desembolsado pelo CESSIONÁRIO SUB-ROGADO, incluindo despesas com advogados na ordem de 20% (vinte por cento) do saldo devedor e custas processuais. O não pagamento dos créditos e/ou títulos no prazo previsto no item 2.8 acima, acarretará negativação, apontamento dos títulos para protesto e a imediata exigibilidade dos créditos, ensejando a cobrança judicial contra o DEVEDOR ANUENTE e INTERVENIENTE(S)RESPONSÁVEL(EIS) SOLIDÁRIO(S) dos créditos e/ou títulos não pago(s).
  </p>
  <p>
    2.11 O simples pagamento das multas previstas neste contrato não exime a parte infratora do cumprimento das demais obrigações resultantes deste contrato.
  </p>
  <p>
    2.12 As penalidades porventura aplicadas em conformidade com o disposto neste contrato serão consideradas dívida líquida e certa, servindo para tanto o presente contrato como título executivo extrajudicial.
  </p>
  <p>
    2.13 Realizado o pagamento dos créditos e/ou títulos que os representem, e constada a má-fé da CEDENTE SUB-ROGANTE e/ou DEVEDOR ANUENTE na existência de vícios na origem do crédito, seja quanto à sua existência, seja quanto à sua legalidade e legitimidade, os INTERVENIENTE(S) RESPONSÁVEL(EIS) SOLIDÁRIO(S) responderá(ão) pela pena de multa fixada no valor correspondente ao valor total de face do(s) crédito(s) e/ou título(s) negociado(s), independentemente das demais penalidades previstas no presente contrato.
  </p>
  <p>
    2.14 A não aplicação da multa prevista no item 2.14 pelo CESSIONÁRIO SUB-ROGANTE, constituirá ato de mera liberalidade, não implicando, tácita ou implicitamente, em renúncia a direito ou novação de obrigações.
  </p>
  <p>
    <span class="bold under">3. DA PRORROGAÇÃO DO VENCIMENTO</span>
    <br><br>
    3.1 O CEDENTE SUB-ROGADO, atendendo a solicitação da DEVEDORA ANUENTE, prorroga o vencimento dos créditos representados pelos títulos identificados e descritos no quadro constante da cláusula 1.1, passando o(s) mesmo a vencer conforme as datas descritas no quadro abaixo:
    <br><br>
    '.$table_completo.'
    <br>
  </p>
  <p>
    <span class="bold under">4. DA OUTORGA DE GARANTIAS – RESPONSABILIDADE SOLIDÁRIA</span>
    <br><br>
    4.1 Expressamente, na forma dos artigos 264, 265 e seguintes do Código Civil, o(s) INTERVENIENTE(S) RESPONSÁVEL(EIS) SOLIDÁRIO(S)s, já qualificados anteriormente, assinam o presente contrato como corresponsáveis solidários e principais pagadores com o DEVEDOR ANUENTE por todas as obrigações aqui estabelecidas, cuja responsabilidade perdurará até o total e definitivo cumprimento das obrigações avençadas e abrangidas por este contrato, substituindo sua responsabilidade para todos os títulos cedidos, na vigência deste contrato.
  </p>
  <p>
    4.2 A substituição do(s) INTERVENIENTE(s) RESPONSÁVEL(EIS) SOLIDÁRIO(S) dependerá de anuência prévia e expressa aprovação do CESSIONÁRIO SUB-ROGADO.
  </p>
  <p>
    4.3 Para garantir o pagamento do crédito relacionado a este Contrato, o DEVEDOR ANUENTE e o(s) INTERVENIENTE(s) RESPONSÁVEL(EIS) SOLIDÁRIO(S), emitem neste ato, em favor do CESSIONÁRIO SUB-ROGADO, Nota Promissória com prazo de apresentação para a data do vencimento prorrogado, com pacto adjeto estabelecendo que após o vencimento serão devidos a multa diária de 0,33%, limitada a 10% (dez por cento), juros de 1% ao mês e correção monetária à base do INPC-IBGE, a qual passa a fazer parte integrante e inseparável deste contrato.
  </p>
  <p>
    <span class="bold under">5. DAS COMUNICAÇÕES,  NOTIFICAÇÕES e ASSINATURAS:</span>
    <br><br>
    5.1 Elegem as partes que qualquer comunicação e/ou notificação entre as partes deverão ocorrer exclusivamente observando os dados constantes do preâmbulo deste Instrumento, ou sejam, através de seus endereços eletrônicos (e-mail) e/ou através do número de telefonia móvel e o uso de plataformas de comunicação instantânea, exemplificativamente, mas não se limitando, a whatsapp e telegram. Todas as notificações decorrentes deste Contrato deverão ser feitas por escrito e serão consideradas eficazes: a) quando da transmissão por plataforma de comunicação instantânea, b) quando por envio para o e-mail declarado ou c) quando postado para o endereço eletrônico das partes, independentemente de certificação digital, nos termos do § 2º, art. 10, da MP 2.200-2. Para efeito de qualquer notificação, observar-se-ão os dados constantes do preâmbulo deste Instrumento, que somente poderão ser alterados por notificação enviada por uma Parte à outra, comunicando expressamente as alterações dos dados para contato, em especial os endereços físicos, de telefonia móvel e eletrônicos, sob pena de serem consideradas válidas e recebidas as comunicações realizadas, assim destinadas:
  </p>
  <p>
    (i) Para a CEDENTE SUB-ROGANTE– '.$empresa["razao"].' - CNPJ/MF sob o nº '.formataCpfCnpj($empresa["cnpj"]).', na pessoa de seu representante legal, Sr. '.$empresa["representante"].'
    <br>a.1) e-mail: '.$empresa["email"].'
    <br>a.2) fone móvel: '.formataFone($empresa["telefone"]).'
  </p>
  <p>
    (ii) Para o CESSIONÁRIO SUB-ROGADO - LAWSEC S/A. - CNPJ/MF sob o nº 32.527.198/0001-51, na pessoa de seu representante legal, Sr. Gilberto Eichenberg 
    <br>b.1) e-mail: gilberto@lawsec.com.br 
    <br>b.2) fone móvel: 47-98445-2133
  </p>
  <p>
    (iii) Para o DEVEDOR ANUENTE, Sr(a). '.$empresa["representante"].'
    <br>c.1) e-mail: '.$empresa["email"].'
    <br>c.2) fone móvel: '.formataFone($empresa["telefone"]).'
  </p>
  <p>
    (iii) Para o INTERVENIENTE RESPONSÁVEL SOLIDÁRIO, Sr(a). '.$empresa["representante"].'
    <br>c.1) e-mail: '.$empresa["email"].'
    <br>c.2) fone móvel: '.formataFone($empresa["telefone"]).'
  </p>
  <p>
    (iv) Para a INTERVENIENTE FIEL DEPOSITÁRIO, Sr(a). ). '.$empresa["representante"].'
    <br>d.1) e-mail: '.$empresa["email"].'
    <br>d.2) fone móvel: '.formataFone($empresa["telefone"]).'
  </p>
  <p>
    5.2 Declaram as partes que averiguaram os endereços eletrônicos e números de telefones móveis acima descritos e por atestarem serem detentores e usuários dos mesmos, declaram sua concordância na utilização dos mesmos para qualquer comunicação ou notificação, obrigando-se, em caso de desuso ou alteração, comunicar as demais partes em até 15 (quinze) dias, por escrito, bem com firmarem termo aditivo.
  </p>
  <p>
    5.3 Reconhecem as partes, nos termos do § 2º, art. 10, da MP 2.200-2, que as assinaturas digitais e/ou eletrônicas apostas neste instrumento como em qualquer título de crédito com sua origem vinculada ao presente instrumento de compromisso, independentemente de serem ou não produzidos com a utilização de processo de certificação disponibilizado pela ICP-Brasil, é admitido como válido, gerando por via de consequência todos seus efeitos legais perante as partes e quaisquer terceiros.
  </p>
  <p>
    <span class="bold under">6. DA CUSTÓDIA DE INFORMAÇÕES NA FORMA DA LEI 13.709/2018</span>
		<br><br>
		6.1 As partes comprometem-se a cumprir os requisitos estabelecidos neste instrumento e na legislação de proteção de dados aplicável no Brasil, incluindo, mas não se limitando à Lei nº 13.709 de agosto de 2018 (“Lei Geral de Proteção de Dados Pessoais” ou “LGPD”).
  </p>
	<p>
		6.2 A CEDENTE SUB-ROGANTE, o DEVEDOR ANUENTE, o(s) RESPONSÁVEL(IS) SOLIDÁRIO(S) e o FIEL DEPOSITÁRIO autorizam a coleta de dados pessoais imprescindíveis a execução do presente contrato, nos termos da Lei nº 13.709 de agosto de 2018, tais como (i) dados relacionados à sua identificação pessoal, a fim de que se garanta a fiel contratação pelo respectivo titular do contrato e; (ii) dados relacionados ao endereço, haja vista a necessidade de identificar o local em que esta encontra-se sediada.
	</p>
	<p>
		6.2.1 A CEDENTE SUB-ROGANTE, o DEVEDOR ANUENTE, o(s) RESPONSÁVEL(IS) SOLIDÁRIO(S)e o FIEL DEPOSITÁRIO reconhecem que todos os dados pessoais solicitados e coletados são os estritamente necessários para os fins almejados neste contrato.
	</p>
	<p>
		6.2.2 A CEDENTE SUB-ROGANTE, o DEVEDOR ANUENTE, o(s) RESPONSÁVEL(IS) SOLIDÁRIO(S)e o FIEL DEPOSITÁRIO autorizam o compartilhamento de seus dados, para os fins descritos neste item, com terceiros legalmente legítimos para defender os interesses da CESSIONÁRIA, bem como da(s) CEDENTE(S).
	</p>
	<p>
		6.3 A CEDENTE SUB-ROGANTE, o DEVEDOR ANUENTE, o(s) RESPONSÁVEL(IS) SOLIDÁRIO(S)e o FIEL DEPOSITÁRIO possuem tempo determinado de 03 (três) anos para acesso aos próprios dados armazenados, podendo também solicitar a exclusão dos referidos dados que foram previamente coletados com o seu consentimento, nos termos da Lei nº 13.709 de agosto de 2018.
	</p>
	<p>
		6.3.1 Caso a CEDENTE SUB-ROGANTE, o DEVEDOR ANUENTE, o(s) RESPONSÁVEL(IS) SOLIDÁRIO(S) e o FIEL DEPOSITÁRIO pretendam realizar a exclusão de algum dado coletado, deverão preencher uma declaração neste sentido, ciente que a revogação de determinados dados poderá importar em eventuais prejuízos na prestação de serviços.
	</p>
	<p>
		6.4 As partes comprometem-se, neste ato, a não utilizar os Dados para outros fins que não aos oriundos do presente Contrato de Prestação de Serviços.
	</p>
	<p>
		6.5 Ficarão armazenados os dados pessoais coletados, pelo prazo descrito no item 6.3, em caso de rescisão contratual, comprometendo-se a CESSIONÁRIA a descartá-los de forma adequada.
	</p>
	<p>
		<span class="bold under">7. DISPOSIÇÕES FINAIS</span>
		<br><br>
		7.1 Este Contrato tornar-se-á eficaz na data de sua assinatura e permanecerá em vigor até a total liquidação/pagamento dos Créditos por parte dos respectivos Devedores. 
	</p>
	<p>
		7.2 Os direitos de cada Parte previstos neste Contrato (i) são cumulativos com outros direitos previstos em lei, a menos que expressamente os excluam; e (ii) só admitem renúncia por escrito e específica. O não exercício, total ou parcial, de qualquer direito decorrente do presente Contrato, ou de seus Aditamentos, não implicará novação da obrigação ou renúncia ao respectivo direito por seu titular.
	</p>
	<p>
		7.3 Se qualquer disposição deste Contrato ou de seus Aditamentos for considerada inválida e/ou ineficaz, as Partes deverão envidar seus melhores esforços para substituí-la por outra de conteúdo similar e com os mesmos efeitos. A eventual invalidade e/ou ineficácia de uma ou mais cláusulas não afetará as demais disposições do presente Contrato ou de seus Aditamentos.
	</p>
	<p>
		7.4 As Partes se comprometem a empregar seus melhores esforços para resolver através de negociações qualquer disputa ou controvérsia relacionada a este Contrato ou aos Aditamentos.
	</p>
	<p>
		7.5 O inadimplemento de qualquer das obrigações previstas neste Contrato e seus aditamentos, por qualquer das partes, ensejará o direito de a parte lesada promover a execução específica para o cumprimento destas obrigações revestindo-se, para tal fim, o presente contrato, das características de título executivo extrajudicial, na forma do art. 784, II do Código de Processo Civil. Para tanto, reputa-se líquido e certo, para todos os fins de direito, o valor da soma de todos os créditos e/ou títulos que os representem (abrangendo principal e acessórios) objeto das operações formalizadas através deste contrato e dos respectivos Aditamentos celebrados entre as Partes.
	</p>
	<p>
		7.6 Para que o presente contrato e eventuais aditamentos operem plenamente seus efeitos jurídicos perante terceiros, poderão a qualquer momento ser levados a registro no Cartório de Registro Público de Títulos e Documentos. As despesas relativas ao registro do contrato correrão por conta exclusiva do DEVEDOR ANUENTE.
	</p>
	<p>
		7.7 O presente contrato é firmado em caráter irrevogável e irretratável, obrigando-se as partes, seus herdeiros e sucessores, não podendo ser transferido ou cedido por qualquer das Partes, no todo ou em Parte, sem a prévia aprovação por escrita da outra Parte.
	</p>
	<p>
		7.8 Quaisquer alterações do presente contrato somente serão válidas quando feitas por escrito e assinadas pelas Partes, mediante a celebração do competente Aditamento.
	</p>
	<p>
		7.9 A nomenclatura utilizada como título das seções do presente Contrato tem apenas fins de referência, não definindo, limitando ou restringindo quaisquer de seus termos ou condições.
	</p>
	<p>
		7.10 O contrato reflete as manifestações de vontade das partes, declarando que a decretação de estado de calamidade pública pela União Federal, Estados ou Municípios, qualquer que seja a razão incluindo-se pandemias, não modificará as obrigações e disposições contidas neste instrumento, renunciando, expressamente, a todo e qualquer prazo de natureza material e processual que impeçam ou obstem a pretensão executiva do objeto do contrato, em especial os contidos em legislações transitórias promulgadas ou publicadas durante e/ou após o estado de calamidade pública, inclusive normas que afastem a incidência dos juros, correção monetária e multas, na hipótese de inadimplemento ou descumprimento contratual.
	</p>
	<p>
  As Partes neste ato elegem o Foro da Comarca de Jaraguá do Sul, Estado de Santa Catarina, com expressa exclusão de qualquer outro, ainda que privilegiado, como competente para dirimir quaisquer dúvidas e/ou questões oriundas deste Contrato ou de eventuais aditamentos.
	</p>
	<p>
  E, por estarem assim justas e contratadas, as Partes assinam o presente contrato em uma única via, na forma digital, na presença de duas testemunhas.
	</p>
	<p>
		Jaraguá do Sul, '.date("d").'/'.date("m").'/'.date("Y").'.
	</p>
	<p><br><br><br><br>
		<span class="bold">CEDENTE SUB-ROGANTE
		<br>'.$empresa["razao"].'
		<br>CNPJ/MF sob o nº '.formataCpfCnpj($empresa["cnpj"]).'
    </span>
	</p>
	<p><br><br><br><br>
		<span class="bold">CESSIONÁRIO SUB-ROGADO
		<br>LAWSEC S/A.
		<br>CNPJ/MF sob o nº 32.527.198/0001-51
    </span>
	</p>
  <p><br><br><br>
  <span class="bold">DEVEDOR ANUENTE
  <br>'.$empresa["representante"].'			
  <br>CPF/MF nº '.formataCpfCnpj($empresa["cpf"]).'			
  </span>
</p>
	<p><br><br><br>
		<span class="bold">INTERVENIENTE RESPONSÁVEL SOLIDÁRIO	   	
		<br>'.$empresa["representante"].'			
		<br>CPF/MF nº '.formataCpfCnpj($empresa["cpf"]).'			
    </span>
	</p>
	<p><br><br><br>
		<span class="bold">INTERVENIENTE FIEL DEPOSITÁRIO
		<br>'.$empresa["representante"].'
		<br>CPF/MF nº '.formataCpfCnpj($empresa["cpf"]).'
    </span>
	</p>
	';
/*
	<p>
		Testemunhas:
		<br><br><br><br>
    <div>
      <div class="fleft">
        __________________________________
        <br>Nome:
        <br>CPF/MF nº 
      </div>
      <div class="fright">
        __________________________________
        <br>Nome:
        <br>CPF/MF nº 
      </div>
    </div>
	</p>
  gilberto@lawsecsa.com.br
  Lawsmart@2022
*/
include('../mpdf/autoload.php');
  $contrato = 'contrato_'.$empresa['cnpj'].'_'.str_replace(',', '_', $id).'.pdf';
  $mpdf = new \Mpdf\Mpdf();
  $css = file_get_contents('contrato.css');
  $mpdf->WriteHTML($css, 1);	
	$mpdf->setHTMLFooter('<p class="footer">Página <b>{PAGENO}</b> de <b>{nbpg}</b></p>');
	$mpdf->AddPage('P', 'A4', '', '', '', 13, 13, 13, 13, 5, 5);
	$mpdf->WriteHTML($html);
	$mpdf->Output('../contratos/'.$contrato);

  $json = '';
  if (file_exists('../contratos/'.$contrato)) {
    $json = array('criado' => true, 'emailEmpresa' => $empresa['email'], 'nome' => 'contratos/'.$contrato, 'id_operacao' => $id);
  } else {
    $json = array('criado' => false, 'emailEmpresa' => '', 'nome' => '');
  }

  echo json_encode($json);

?>