<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Domains\Environment;
use ValdeirPsr\PagSeguro\Request\Notification;

class NotificationTest extends TestCase
{
    /**
     * @test
     * @group notification
     */
    public function newInstance()
    {
        $env = Environment::sandbox('pagseguro@valdeir.dev', '1234567890');
        $instance = new Notification($env);

        $this->assertInstanceOf(Notification::class, $instance);
    }

    /**
     * @test
     * @group notification
     */
    public function checkNotificationWithValidArguments()
    {
        $env = Environment::sandbox('pagseguro@valdeir.dev', '1234567890');

        $stub = $this->getMockBuilder(Notification::class)
            ->setConstructorArgs([$env])
            ->setMethods(['buildUrl'])
            ->getMock();

        $stub->expects($this->once())
            ->method('buildUrl')
            ->willReturn(getenv('SERVER_URL') . 'v3/transactions/notifications-valid-request');

        $notificationId = 'abc123';

        $transaction = $stub->capture($notificationId);

        $this->assertEquals('5D4A027F-C28E-4799-8154-A154B6F7DD7B', $transaction->getCode());
        $this->assertEquals(2.07, $transaction->getCreditorFees()->getInstallmentFeeAmount());
        $this->assertEquals(0.40, $transaction->getCreditorFees()->getIntermediationRateAmount());
        $this->assertEquals(2.17, $transaction->getCreditorFees()->getIntermediationFeeAmount());
        $this->assertEquals('INTERNAL', $transaction->getCancellationSource());
        $this->assertEquals('2020-09-06 00:59:05', $transaction->getEscrowEndDate()->format('Y-m-d H:i:s'));
    }
}
