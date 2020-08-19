<?php

namespace ValdeirPsr\PagSeguro\Domains;

/**
 * Somente leitura
 * Somente quando o pagamento for via cartão de crédito
 */
class GatewaySystem
{
    /** @var string */
    private $type;
    
    /** @var string */
    private $authorizationCode;
    
    /** @var string */
    private $nsu;
    
    /** @var string */
    private $tid;
    
    /** @var string */
    private $establishmentCode;
    
    /** @var string */
    private $acquirerName;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getAuthorizationCode(): string
    {
        return $this->authorizationCode;
    }

    /**
     * @return string
     */
    public function getNsu(): string
    {
        return $this->nsu;
    }

    /**
     * @return string
     */
    public function getTid(): string
    {
        return $this->tid;
    }

    /**
     * @return string
     */
    public function getEstablishmentCode(): string
    {
        return $this->establishmentCode;
    }

    /**
     * @return string
     */
    public function getAcquirerName(): string
    {
        return $this->acquirerName;
    }
}
