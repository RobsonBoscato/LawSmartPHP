# API de Integração - Sistema de Operações Financeiras

Esta API é responsável por integrar informações dos clientes e fornecedores ao nosso sistema, alimentando o banco de dados e gerenciando operações financeiras.

## Funcionalidades Principais

1. **Autenticação de Usuários**:

   - Os usuários (clientes e fornecedores) devem realizar o login com senha e preencher informações obrigatórias do cadastro.

2. **Cadastro Completo**:

   - Preenchimento obrigatório de todas as informações necessárias para completar o cadastro.

3. **Fluxo de Operações**:

   - Após o cadastro, os usuários podem realizar diversas operações de acordo com os limites estabelecidos.

4. **Registro e Validação de Operações**:

   - Todas as operações são verificadas, gravadas e validadas no sistema.

5. **Notificações Automáticas**:

   - Cada operação gera notificações para os envolvidos.

6. **Geração de Duplicatas**:

   - Cada operação gera novas duplicatas, registradas no banco por arquivo/retorno.

7. **Notificação de Pagamento**:
   - Após a confirmação da operação, o fundo correspondente é notificado para realizar o pagamento.

## Detalhes Técnicos

- **Tecnologia**: API REST utilizando JSON e cURL para comunicação.

## Recursos Específicos

### Fornecedores

- `fornecedorRazao`
- `fornecedorCNPJ`
- `fornecedorEmail`
- `fornecedorTelefone`
- `fornecedorTaxaJuros`
- `fornecedorLimite`
- `fornecedorCustoBoleto`\*
- `fornecedorTAC`\*
- `fornecedorTED`_
  (_ Caso os campos acima venham zerados, serão utilizados os padrões do sistema.)

### Duplicatas

- `duplicataFornecedorCNPJ`
- `duplicataNota`
- `duplicataVencimento`

## Fluxo de Operações

1. **Tipos de Operação**

   - Descrição dos diferentes tipos de operação disponíveis.

2. **Status das Operações**

   - Lista dos status possíveis das operações e seus significados.

3. **Gráficos e Visualizações**

   - Explicação dos gráficos e dados apresentados na interface.

4. **Níveis de Acesso**

   - Hierarquia de níveis de acesso no sistema (Ancora, Fornecedor e Cliente).

5. **Arquivo de Retorno**
   - Estrutura e uso dos arquivos de retorno gerados pelo sistema.

## Status das Operações (LAWSMART)

- 0: Duplicata normal de entrada
- 1: Duplicata antecipada
- 2: Duplicata antecipada cancelada
- 3: Duplicata postergada
- 4: Duplicata postergada cancelada
- 5: Duplicata Cancelada
- 6: Duplicata de Ancora
- 7: Duplicata de ancora antecipada
- 8: Duplicata de ancora antecipada cancelada
- 9: Duplicada postergada livre para antecipação

## Hierarquia de Níveis

O sistema é dividido entre Ancora, Fornecedor e Cliente, cada um com diferentes níveis de acesso e responsabilidades.

## Funcionamento Geral

Ao utilizar as APIs disponíveis, as informações são filtradas e distribuídas na base de dados. Após validação, as operações ficam disponíveis para os responsáveis seguir o fluxo do sistema.

**Nota**: Todas as ações realizadas no sistema são irreversíveis.

## Estrutura de Arquivos

Os arquivos do sistema são distribuídos de acordo com a hierarquia de níveis de acesso, sendo cada um responsável por funções específicas do sistema, com comentários sobre seu funcionamento.
