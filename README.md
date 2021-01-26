![Create Releasea by Tag](https://github.com/opencart-extension/PagSeguro-Checkout-Transparente/workflows/Create%20Releasea%20by%20Tag/badge.svg)
![Generate Doc](https://github.com/opencart-extension/PagSeguro-Checkout-Transparente/workflows/Generate%20Doc/badge.svg)
![Test SDK with PHPUnit](https://github.com/opencart-extension/PagSeguro-Checkout-Transparente/workflows/Test%20SDK%20with%20PHPUnit/badge.svg)

# :rocket: PagSeguro Checkout Transparente
 > Módulo PagSeguro Checkout Transparente para OpenCart

O projeto "PagSeguro Transparente" foi criado para facilitar a compra para o usuário final. Ele, quando instalado, tem a função de tornar a compra mais fácil, pois o famoso 'redirecionamento' é 'eliminado'.

# :dollar: Vantagens

### Pagamento feito totalmente em seu e-commerce ou site
O cliente fica no ambiente do seu e-commerce ou site durante todo o processo de compra, sem necessidade de cadastro ou páginas intermediárias de pagamento.

### Aumento de conversão de suas vendas
Você pode ter um aumento de até 30% na conversão de suas vendas, uma vez que, o número de etapas do seu checkout será reduzido e seus clientes não serão direcionados para páginas externas ao seu e-commerce ou site.

### Segurança de dados feita pelo PagSeguro
Os dados de pagamento dos seus clientes são direcionados diretamente do navegador para o PagSeguro. Sem passar por seus servidores, assim não precisa se preocupar com a segurança destas informações.

# :hammer: Requisitos

| Ferramenta | Versão |
| ---------- | ------ |
| PHP        | >= 7.3 |
| cURL       | >= 7   |
| ext-json   | -      |
| ext-iconv  | -      |
| ext-xml    | -      |
| ext-curl   | -      |
| SSL        | -      |

# :computer: Instalar via Git/GitHub

Para realizar o download via GitHub, é necessário possuir o [composer](https://getcomposer.org/) instalados em seu pc/notebook/etc.

1. Para baixar, acesse seu terminal e execute o comando abaixo
```bash
git clone https://github.com/opencart-extension/PagSeguro-Checkout-Transparente.git PagSeguro
```

2. Caso não possua o *Git*, realize o download através da url [https://github.com/opencart-extension/PagSeguro-Checkout-Transparente/archive/develop.zip](https://github.com/opencart-extension/PagSeguro-Checkout-Transparente/archive/develop.zip "Download do repositório") e realize a extração do arquivo.

3. Acesse a pasta/diretório criada

4. Dentro da pasta/diretório, execute o *composer* para instalar as dependências necessárias para o funcionamento.
```bash
composer install
```

5. Após a instalação feita, é possível enviar os arquivos para a raiz de sua no servidor remoto ou local.

# :new: Novidades

**[2.0.1]**

* o botão de 2 via aparecia somente no tema padrão ([823a70c](https://github.com/opencart-extension/PagSeguro-Checkout-Transparente/commit/823a70c44823d5dbaa1b463a87722da67af1acaf))
* **ocmod:** limita correção do bug do twig na versão OC 3.0.3.6 ([6472ba0](https://github.com/opencart-extension/PagSeguro-Checkout-Transparente/commit/6472ba0e590d0e6e6c1d4a85dd819629963b03d1))
* **view:** corrige falha na exibiçao dos detalhes de pagamento ([cf61a38](https://github.com/opencart-extension/PagSeguro-Checkout-Transparente/commit/cf61a38eb4d6ec2b2bd599792933e3d0f7d90cd5)), closes [#38](https://github.com/opencart-extension/PagSeguro-Checkout-Transparente/issues/38)
* verifica se a url no download veio mesmo base64 encodada ([3864a74](https://github.com/opencart-extension/PagSeguro-Checkout-Transparente/commit/3864a74c26c05c6686add2c4e83777c2f3e50958))
* Remove trailing comma para maior suporte de versões do PHP ([6d76945](https://github.com/opencart-extension/PagSeguro-Checkout-Transparente/commit/6d76945f601864e74b1cd07e442524aefbcbc864))

**[2.0.0]**

 - Nova versão

# :books: Documentação
https://opencart-extension.github.io/PagSeguro-Checkout-Transparente/

# :interrobang: Suporte
https://valdeirpsr.atlassian.net/servicedesk/customer/portal/3
