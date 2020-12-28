# Aba Situação do Pedido

Nesta aba, você configurará uma situação para cada mudança no *status* de pagamento

![Aba situação do pedido](/PagSeguro-Checkout-Transparente/assets/tab-order-status.png#zoom)

## Campos
| Campo | Descrição |
| ----- | --------- |
| **Aguardando pagamento** | Ocorre quando o pagamento não foi reconhecido pelo PagSeguro (ao imprimir o boleto, por exemplo) |
| **Em análise** | Quando o PagSeguro estiver analisando o pagamento |
| **Pago** | Pagamento completo |
| **Disponível** | Pagamento liberado para saque no PagSeguro |
| **Disputado** | O comprador solicitou reembolso através do PagSeguro |
| **Reembolsado** | Cliente reembolssado |
| **Cancelado** | Pagamento cancelado (o cliente não pagou o boleto, por exemplo) |

::: tip
O _status Disponível_ não é enviado para o cliente
:::