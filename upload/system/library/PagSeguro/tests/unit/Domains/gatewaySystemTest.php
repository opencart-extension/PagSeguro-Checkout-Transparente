<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\GatewaySystem;

class GatewaySystemTest extends TestCase
{
    /**
     * @test
     */
    public function newInstance()
    {
        $instance = new GatewaySystem();
        $this->assertInstanceOf(GatewaySystem::class, $instance);
    }

    /**
     * @test
     */
    public function populateFromXml()
    {
        $xmlOriginal = '
        <gatewaySystem>
            <type>cielo</type>
            <rawCode xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
            <rawMessage xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
            <normalizedCode xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
            <normalizedMessage xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
            <authorizationCode>0</authorizationCode>
            <nsu>0</nsu>
            <tid>0</tid>
            <establishmentCode>1056784170</establishmentCode>
            <acquirerName>CIELO</acquirerName>
        </gatewaySystem>
        ';

        $instance = GatewaySystem::fromXml($xmlOriginal);

        $this->assertEquals('cielo', $instance->getType());
        $this->assertEquals('0', $instance->getAuthorizationCode());
        $this->assertEquals('0', $instance->getNsu());
        $this->assertEquals('0', $instance->getTid());
        $this->assertEquals('1056784170', $instance->getEstablishmentCode());
        $this->assertEquals('CIELO', $instance->getAcquirerName());
    }

    /**
     * @test
     */
    public function populateToXml()
    {
        $xmlOriginal = '
        <gatewaySystem>
            <type>cielo</type>
            <rawCode xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
            <rawMessage xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
            <normalizedCode xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
            <normalizedMessage xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
            <authorizationCode>0</authorizationCode>
            <nsu>0</nsu>
            <tid>0</tid>
            <establishmentCode>1056784170</establishmentCode>
            <acquirerName>CIELO</acquirerName>
        </gatewaySystem>
        ';

        $xmlExpected = '
        <GatewaySystem>
            <type>cielo</type>
            <authorizationCode>0</authorizationCode>
            <nsu>0</nsu>
            <tid>0</tid>
            <establishmentCode>1056784170</establishmentCode>
            <acquirerName>CIELO</acquirerName>
        </GatewaySystem>
        ';

        $instance = GatewaySystem::fromXml($xmlOriginal);

        $this->assertXmlStringEqualsXmlString($xmlExpected, $instance->toXml());
    }
}
