<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Constants\PaymentMethod\Methods as PaymentMethods;
use ValdeirPsr\PagSeguro\Domains\Environment;
use ValdeirPsr\PagSeguro\Domains\Payment;
use ValdeirPsr\PagSeguro\Request\Factory;
use ValdeirPsr\PagSeguro\Request\Sale;
use ValdeirPsr\PagSeguro\Exception\Auth as AuthException;

class SaleTest extends TestCase
{
    /**
     * @test
     */
    public function newInstance()
    {
        $env = Environment::sandbox('pagseguro@valdeir.dev', '1234567890');
        $instance = new Sale($env);

        $this->assertInstanceOf(Sale::class, $instance);
    }

    /**
     * @test
     */
    public function createSaleWithBoletoAndValidArguments()
    {
        $env = Environment::sandbox('pagseguro@valdeir.dev', '1234567890');

        $xml = file_get_contents('tests/data/sale/valid-boleto.xml');

        $payment = Payment::fromXml($xml);

        $stub = $this->getMockBuilder(Sale::class)
            ->setConstructorArgs([$env])
            ->setMethods(['buildUrl'])
            ->getMock();

        $stub->expects($this->any())
            ->method('buildUrl')
            ->willReturn('https://f3528d51-6219-4b80-8bd3-3ab112b8094f.mock.pstmn.io/v2/transactions/boleto-valid');

        $newPayment = $stub->create($payment);

        $this->assertEquals('Florbela Espanca', $newPayment->getSender()->getName());
        $this->assertEquals('Antologia poetica de Florbela Espanca', $newPayment->getItems()[0]->getDescription());
        $this->assertEquals('Poesia de Florbela Espanca', $newPayment->getItems()[1]->getDescription());
        $this->assertStringStartsWith('https://sandbox.pagseguro.uol.com.br/checkout/payment/booklet/print.jhtml', $newPayment->getPayment()->getPaymentLink());
        $this->assertEquals(PaymentMethods::BOLETO, $newPayment->getPayment()->getType());
    }

    /**
     * @test
     */
    public function createSaleWithEftAndValidArguments()
    {
        $env = Environment::sandbox('pagseguro@valdeir.dev', '1234567890');

        $xml = file_get_contents('tests/data/sale/valid-eft.xml');

        $payment = Payment::fromXml($xml);

        $stub = $this->getMockBuilder(Sale::class)
            ->setConstructorArgs([$env])
            ->setMethods(['buildUrl'])
            ->getMock();

        $stub->expects($this->any())
            ->method('buildUrl')
            ->willReturn('https://f3528d51-6219-4b80-8bd3-3ab112b8094f.mock.pstmn.io/v2/transactions/eft-valid');

        $newPayment = $stub->create($payment);

        $this->assertEquals('Florbela Espanca', $newPayment->getSender()->getName());
        $this->assertEquals('Antologia poetica de Florbela Espanca', $newPayment->getItems()[0]->getDescription());
        $this->assertEquals('Poesia de Florbela Espanca', $newPayment->getItems()[1]->getDescription());
        $this->assertStringStartsWith('https://sandbox.pagseguro.uol.com.br/checkout/payment/eft/print.jhtml', $newPayment->getPayment()->getPaymentLink());
        $this->assertNotNull($newPayment->getStatus());
        $this->assertEquals(PaymentMethods::ELETRONIC_TRANSFER, $newPayment->getPayment()->getType());
    }

    /**
     * @test
     */
    public function createSaleWithCreditCardAndValidArguments()
    {
        $env = Environment::sandbox('pagseguro@valdeir.dev', '1234567890');

        $xml = file_get_contents('tests/data/sale/valid-creditcard.xml');

        $payment = Payment::fromXml($xml);

        $stub = $this->getMockBuilder(Sale::class)
            ->setConstructorArgs([$env])
            ->setMethods(['buildUrl'])
            ->getMock();

        $stub->expects($this->any())
            ->method('buildUrl')
            ->willReturn('https://f3528d51-6219-4b80-8bd3-3ab112b8094f.mock.pstmn.io/v2/transactions/creditcard-valid');

        $newPayment = $stub->create($payment);

        $this->assertEquals('Florbela Espanca', $newPayment->getSender()->getName());
        $this->assertEquals('Antologia poetica de Florbela Espanca', $newPayment->getItems()[0]->getDescription());
        $this->assertEquals('Poesia de Florbela Espanca', $newPayment->getItems()[1]->getDescription());
        $this->assertNotNull($newPayment->getStatus());
        $this->assertEquals(PaymentMethods::CREDITCARD, $newPayment->getPayment()->getType());
        $this->assertEquals('cielo', $newPayment->getGatewaySystem()->getType());
        $this->assertEquals('0', $newPayment->getGatewaySystem()->getNsu());
        $this->assertEquals('0', $newPayment->getGatewaySystem()->getTid());
        $this->assertEquals('1056784170', $newPayment->getGatewaySystem()->getEstablishmentCode());
        $this->assertEquals('CIELO', $newPayment->getGatewaySystem()->getAcquirerName());
    }
}
