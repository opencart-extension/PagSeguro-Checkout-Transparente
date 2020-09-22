<?php

namespace ValdeirPsr\PagSeguro\Exception;

use DOMDocument;
use DOMXPath;
use Throwable;
use ValdeirPsr\PagSeguro\Domains\Environment;
use ValdeirPsr\PagSeguro\Domains\Error;
use ValdeirPsr\PagSeguro\Domains\Logger\Logger;
use Curl\Curl;

class PagSeguroRequest extends \Exception
{
    private $request;
    private $requestBody;
    private $errors = [];

    public function __construct(
        Environment $env,
        Curl $curl,
        $requestBody,
        string $message = null,
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->request = $curl;
        $this->requestBody = $requestBody;

        Logger::warning('Erro ao realizar pagamento', [
            'e-mail' => $env->getEmail(),
            'token' => $env->getToken(),
            'request' => $requestBody,
            'response' => $this->getResponse()
        ]);

        $this->checkErrors();
    }

    /**
     * @return Curl
     */
    public function getRequest(): Curl
    {
        return $request;
    }

    /**
     * @return mixed Retorna a resposta do servidor
     */
    public function getResponse()
    {
        return $this->request->getResponse();
    }

    /**
     * @return int Retorna o Status Code da resposta
     */
    public function getHttpStatus(): int
    {
        return $this->request->getHttpstatus();
    }

    /**
     * Define o corpo da mensagem enviada
     *
     * @param mixed $value
     *
     * @return self
     */
    public function setRequestBody($value)
    {
        $this->requestBody = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRequestBody()
    {
        return $this->requestBody;
    }

    /**
     * @return Error[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Verifica os erros recebidos pela API
     *
     * @return void
     */
    private function checkErrors()
    {
        $response = $this->getResponse();

        if ($response) {
            $responseDom = new DOMDocument();
            $status = $responseDom->loadXML($response, LIBXML_NOERROR);

            if ($status) {
                $errors = $responseDom->getElementsByTagName('error');

                foreach ($errors as $error) {
                    $code = $error->getElementsByTagName('code');
                    $msg = $error->getElementsByTagName('message');

                    if ($code->count() > 0) {
                        $code = intval($code->item(0)->textContent);
                    } else {
                        $code = 0;
                    }

                    if ($msg->count() > 0) {
                        $msg = trim($msg->item(0)->textContent);
                    } else {
                        $msg = $response;
                    }

                    $this->errors[] = new Error($msg, $code);
                }
            } else {
                $this->errors[] = new Error($response);
            }
        } else {
            $this->errors[] = new Error('PagSeguroRequest :: Response empty');
        }
    }
}
