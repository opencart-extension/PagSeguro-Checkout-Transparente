<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Constants\PaymentMethod\Methods as PaymentMethods;
use ValdeirPsr\PagSeguro\Domains\Environment;
use ValdeirPsr\PagSeguro\Domains\Payment;
use ValdeirPsr\PagSeguro\Domains\Error;
use \ValdeirPsr\PagSeguro\Domains\Document;
use \ValdeirPsr\PagSeguro\Domains\Address;
use ValdeirPsr\PagSeguro\Domains\PaymentMethod\CreditCard;
use ValdeirPsr\PagSeguro\Domains\User\Holder;
use ValdeirPsr\PagSeguro\Request\Factory;
use ValdeirPsr\PagSeguro\Request\Sale;
use ValdeirPsr\PagSeguro\Exception\Auth as AuthException;
use ValdeirPsr\PagSeguro\Exception\PagSeguroRequest as PagSeguroRequestException;

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

        $payment = new Payment();

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

    /**
     * @test
     */
    public function createSaleWithCreditCardAndInvalidArgumentsShouldGiveError()
    {
        try {
            $env = Environment::sandbox('pagseguro@valdeir.dev', '1234567890');

            $payment = Payment::fromXml(file_get_contents('tests/data/sale/invalid-creditcard.xml'));

            $creditCard = new CreditCard();
            $creditCard->setToken('e79fc9be6fd14b3c8b6164f21ba3c464');
            $creditCard->setInstallmentQuantity(3);
            $creditCard->setInstallmentValue(14.46);

            $holder = new Holder();
            $holder->setName('Nome impresso no cartao');
            $holder->setBirthDate(DateTime::createFromFormat('d/m/Y', '20/10/2021'));
            $holder->setDocument(Document::cpf('25136624078'));
            $holder->setPhone('11', '999991111');
            $creditCard->setHolder($holder);

            $address = new Address();
            $address->setStreet('Av. Brigadeiro Faria Lima');
            $address->setComplement('1 andar');
            $address->setDistrict('Jardim Paulistano');
            $address->setCity('Sao Paulo');
            $address->setState('SP');
            $address->setPostalCode('01452002');
            $creditCard->setBillingAddress($address);

            $payment->setPayment($creditCard);

            $stub = $this->getMockBuilder(Sale::class)
                ->setConstructorArgs([$env])
                ->setMethods(['buildUrl'])
                ->getMock();

            $stub->expects($this->any())
                ->method('buildUrl')
                ->willReturn('https://f3528d51-6219-4b80-8bd3-3ab112b8094f.mock.pstmn.io/v2/transactions/creditcard-invalid');

            $newPayment = $stub->create($payment);
        } catch (PagSeguroRequestException $request) {
            $errors = $request->getErrors();

            $this->assertNotEmpty($errors);
            $this->assertContainsOnlyInstancesOf(Error::class, $errors);
            $this->assertXmlStringEqualsXmlFile('tests/data/sale/invalid-creditcard.xml', $request->getRequestBody());
        }
    }

    /**
     * @test
     */
    public function createSaleWithCreditCardAndValidArgumentsAndInvalidCredentialsShouldGiveError()
    {
        $this->expectException(AuthException::class);
        $env = Environment::sandbox('pagseguro@valdeir.dev', '1234567890');

        $payment = Payment::fromXml(file_get_contents('tests/data/sale/valid-creditcard.xml'));

        $creditCard = new CreditCard();
        $creditCard->setToken('e79fc9be6fd14b3c8b6164f21ba3c464');
        $creditCard->setInstallmentQuantity(3);
        $creditCard->setInstallmentValue(14.46);

        $holder = new Holder();
        $holder->setName('Nome impresso no cartao');
        $holder->setBirthDate(DateTime::createFromFormat('d/m/Y', '20/10/2021'));
        $holder->setDocument(Document::cpf('25136624078'));
        $holder->setPhone('11', '999991111');
        $creditCard->setHolder($holder);

        $address = new Address();
        $address->setStreet('Av. Brigadeiro Faria Lima');
        $address->setComplement('1 andar');
        $address->setDistrict('Jardim Paulistano');
        $address->setCity('Sao Paulo');
        $address->setState('SP');
        $address->setPostalCode('01452002');
        $creditCard->setBillingAddress($address);

        $payment->setPayment($creditCard);

        $stub = $this->getMockBuilder(Sale::class)
            ->setConstructorArgs([$env])
            ->setMethods(['buildUrl'])
            ->getMock();

        $stub->expects($this->any())
            ->method('buildUrl')
            ->willReturn('https://f3528d51-6219-4b80-8bd3-3ab112b8094f.mock.pstmn.io/v2/transactions/creditcard-valid-invalid-session');

        $newPayment = $stub->create($payment);
    }

    /**
     * @test
     */
    public function cancelPaymentWithValidArguments()
    {
        $env = Environment::sandbox('pagseguro@valdeir.dev', '1234567890');

        $stub = $this->getMockBuilder(Sale::class)
            ->setConstructorArgs([$env])
            ->setMethods(['buildUrl'])
            ->getMock();

        $stub->expects($this->once())
            ->method('buildUrl')
            ->willReturn('https://f3528d51-6219-4b80-8bd3-3ab112b8094f.mock.pstmn.io/v2/transactions/cancel-sale-valid-data');

        $result = $stub->void('abc123');
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function refundPaymentWithValidArguments()
    {
        $env = Environment::sandbox('pagseguro@valdeir.dev', '1234567890');

        $stub = $this->getMockBuilder(Sale::class)
            ->setConstructorArgs([$env])
            ->setMethods(['buildUrl'])
            ->getMock();

        $stub->expects($this->once())
            ->method('buildUrl')
            ->willReturn('https://f3528d51-6219-4b80-8bd3-3ab112b8094f.mock.pstmn.io/v2/transactions/refund-sale-valid-data');

        $result = $stub->refund('abc123');
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function captureTransactioInfoWihoutError()
    {
        $env = Environment::sandbox('pagseguro@valdeir.dev', '0123456789');

        $stub = $stub = $this->getMockBuilder(Sale::class)
            ->setConstructorArgs([$env])
            ->setMethods(['buildUrl'])
            ->getMock();

        $stub->expects($this->once())
            ->method('buildUrl')
            ->willReturn('https://f3528d51-6219-4b80-8bd3-3ab112b8094f.mock.pstmn.io/v2/transactions/info-sale-valid-data');

        $transaction = $stub->info('C9133EB990AE44E5963F027E6B908B41');

        $this->assertEquals('C9133EB9-90AE-44E5-963F-027E6B908B41', $transaction->getCode());
        $this->assertEquals(43.4, $transaction->getGrossAmount());
        $this->assertEquals(
            'https://sandbox.pagseguro.uol.com.br/checkout/payment/booklet/print.jhtml?c=ac680d9c3cc7c02e267b6f3c5ab2436f6b605ef9124307f20b9296c57cb90e319b74953b59fee8d',
            $transaction->getPayment()->getPaymentLink()
        );
    }
}
