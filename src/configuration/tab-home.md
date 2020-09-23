# Aba Home

Configure os dados básicos como credenciais e modo de desenvolvimento.

![Tab Home](/PagSeguro-Checkout-Transparente/assets/tab-home.png#zoom)

| Campo | Descrição | Obrigatório |
| ----- | --------- | :---------: |
| **Situação** | Habilita ou desabilita as formas de pagamento da extensão | Sim |
| **E-mail** | Informe o e-mail da sua credencial | Sim |
| **Token** | Informe seu *token* de acesso | Sim |
| **Modo de desenvolvimento** | Ao habilitar, sua loja estará em modo *sandbox*, ou seja, apenas para testes. Os pedidos feitos com este campo habilitado não terão efeitos. | Sim |
| **Modo Debug** | Ao habilitar, sua loja salvará os *logs* das requisições. Recomendado para modo de teste. | Sim |
| **Notificar clientes** | Informe sim caso queira enviar uma notificação para o usuário a cada atualização da situação do pedido. | Sim |
| **Token de segurança** | Chave utilizada como parâmetro de URL para evitar *DDoS* ou *brute force*. | Sim |
| **Telemetria** | Com esta opção ativada, o módulo enviará informações básicas para serem analisadas. Isso poderá nos ajudar a melhorar nossos módulos. Saiba mais acessando a página [Telemetria](/PagSeguro-Checkout-Transparente/telemetry/) | Sim |
| **Informe seu e-mail para receber atualizações** | Caso queira receber atualizações do módulo, informe um e-mail para contato | Não |

::: tip Dica
Obtenha as credenciais com o PagSeguro.
Para obter as credenciais de *sandbox* (teste), acesse https://sandbox.pagseguro.uol.com.br/
:::