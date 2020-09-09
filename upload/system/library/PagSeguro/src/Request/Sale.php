<?php

namespace ValdeirPsr\PagSeguro\Request;

use DOMDocument;
use ValdeirPsr\PagSeguro\Domains\Environment;
use ValdeirPsr\PagSeguro\Domains\Payment;
use ValdeirPsr\PagSeguro\Domains\Transaction;
use ValdeirPsr\PagSeguro\Domains\Logger\Logger;
use ValdeirPsr\PagSeguro\Exception\Auth as AuthException;
use ValdeirPsr\PagSeguro\Exception\PagSeguroRequest as PagSeguroRequestException;

class Sale
{
    private $env;
    private $logId;

    public function __construct(Environment $env)
    {
        $this->env = $env;
        $this->logId = uniqid();
    }

    /**
     * Envia as configurações de pagamento para o PagSeguro
     * No caso de boleto ou transferência, é retornar um link
     * para impressão ou autenticação, respectivamente.
     *
     * @param Payment $payment
     *
     * @throws AuthException Caso as credenciais sejam inválidas
     * @throws PagSeguroRequestException Caso alguma informação seja enviada incorretamente.
     *
     * @return Transaction
     */
    public function create(Payment $payment)
    {
        Logger::info('Creating a new order', [
            'Log ID' => $this->logId,
            'Request' => $payment->toXml()
        ]);

        return Transaction::fromXml($this->request($payment));
    }

    /**
     * Captura informações de uma transação
     *
     * @param string $paymentId
     *
     * @return Transaction
     */
    public function info(string $paymentId)
    {
        Logger::info('Capturing info order', [
            'Log ID' => $this->logId,
            'Payment ID' => $paymentId
        ]);

        return Transaction::fromXml($this->request(null, "/{$paymentId}", 'GET'));
    }

    /**
     * Cancela pagamento
     *
     * @param string $paymentId
     *
     * @return bool
     */
    public function void($paymentId): bool
    {
        Logger::info('Canceling an order', [
            'Log ID' => $this->logId,
            'Payment ID' => $paymentId
        ]);

        $response = $this->request([
            'transactionCode' => $paymentId
        ], '/cancels');

        $dom = new DOMDocument();
        $dom->loadXML($response);

        $result = $dom->getElementsByTagName('result');

        Logger::info('Order canceled', [
            'Log ID' => $this->logId,
            'Payment ID' => $paymentId,
            'Response' => $response
        ]);

        return ($result->count() > 0)
            ? strtolower(trim($result->item(0)->textContent)) === 'ok'
            : false;
    }

    /**
     * Reembolsa pagamento
     *
     * @param string $paymentId
     *
     * @return bool
     */
    public function refund($paymentId): bool
    {
        Logger::info('Refunding an order', [
            'Log ID' => $this->logId,
            'Payment ID' => $paymentId
        ]);

        $response = $this->request([
            'transactionCode' => $paymentId
        ], '/cancels');

        $dom = new DOMDocument();
        $dom->loadXML($response);

        Logger::info('Refunded order', [
            'Log ID' => $this->logId,
            'Payment ID' => $paymentId,
            'Response' => $response
        ]);

        $result = $dom->getElementsByTagName('result');

        return ($result->count() > 0)
            ? strtolower(trim($result->item(0)->textContent)) === 'ok'
            : false;
    }

    /**
     * {@inheritDoc}
     */
    protected function buildUrl(string $path = ''): string
    {
        return Factory::url($this->env, "v2/transactions{$path}", [
            'email' => $this->env->getEmail(),
            'token' => $this->env->getToken()
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function request($data, string $path = '', string $method = 'POST')
    {
        $url = $this->buildUrl($path);

        $request = Factory::request($this->env);

        if ($method === 'POST') {
            if ($data instanceof Payment) {
                $request->setHeader('Content-Type', 'application/xml; charset=ISO-8859-1');
                $data = $data->toXml();
            } else {
                $request->setHeader('Content-Type', 'application/x-www-form-urlencoded');
            }
            $request->post($url, $data);
        } elseif ($method === 'GET') {
            $request->get($url);
        }

        $request->close();

        if ($request->isSuccess()) {
            return $request->getResponse();
        } elseif ($request->getHttpStatus() === 401) {
            throw new AuthException($this->env, 'Check your credentials', 1000);
        } else {
            throw new PagSeguroRequestException($request, $data);
        }
    }
}
