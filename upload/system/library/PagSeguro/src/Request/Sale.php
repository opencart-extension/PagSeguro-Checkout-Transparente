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

    public function __construct(Environment $env)
    {
        $this->env = $env;
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
        return Transaction::fromXml($this->request($payment));
    }

    /**
     * Captura as informações de uma transação
     *
     * @param string $paymentId
     *
     * @return Transaction
     */
    public function info(string $paymentId)
    {
        return Transaction::fromXml($this->request(null, "/{$paymentId}", 'GET', 'v3'));
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
        $response = $this->request([
            'transactionCode' => $paymentId
        ], '/cancels');

        $dom = new DOMDocument();
        $dom->loadXML($response);

        $result = $dom->getElementsByTagName('result');

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
        $response = $this->request([
            'transactionCode' => $paymentId
        ], '/refunds');

        $dom = new DOMDocument();
        $dom->loadXML($response);

        $result = $dom->getElementsByTagName('result');

        return ($result->count() > 0)
            ? strtolower(trim($result->item(0)->textContent)) === 'ok'
            : false;
    }

    /**
     * {@inheritDoc}
     */
    protected function buildUrl(string $path = '', $apiVersion = 'v2'): string
    {
        return Factory::url($this->env, "{$apiVersion}/transactions{$path}", [
            'email' => $this->env->getEmail(),
            'token' => $this->env->getToken()
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function request($data, string $path = '', string $method = 'POST', $apiVersion = 'v2')
    {
        $url = $this->buildUrl($path, $apiVersion);
        $uid = time() . ':' . uniqid();

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
            $data = $path;
        }

        $request->close();

        Logger::info('Realiza uma requisição de pagamento', [
            'uid' => $uid,
            'e-mail' => $this->env->getEmail(),
            'token' => $this->env->getToken(),
            'type' => get_called_class(),
            'request' => $data
        ]);

        if ($request->isSuccess()) {
            $response = $request->getResponse();

            Logger::info('Realiza uma requisição de pagamento', [
                'uid' => $uid,
                'e-mail' => $this->env->getEmail(),
                'token' => $this->env->getToken(),
                'type' => get_called_class(),
                'response' => $response
            ]);

            return $response;
        } elseif ($request->getHttpStatus() === 401) {
            throw new AuthException($this->env, 'Check your credentials', 1000);
        } else {
            throw new PagSeguroRequestException($this->env, $request, $data);
        }
    }
}
