<?php

require_once DIR_SYSTEM . 'library/PagSeguro/vendor/autoload.php';

use ValdeirPsr\PagSeguro\Exception\Auth as AuthException;
use ValdeirPsr\PagSeguro\Exception\PagSeguroRequest as PagSeguroRequestException;
use ValdeirPsr\PagSeguro\Constants\Shipping\Type as ShippingTypes;
use ValdeirPsr\PagSeguro\Domains\Payment;
use ValdeirPsr\PagSeguro\Domains\User\Factory as FactoryUser;
use ValdeirPsr\PagSeguro\Domains\CartItem;
use ValdeirPsr\PagSeguro\Domains\Shipping;
use ValdeirPsr\PagSeguro\Domains\Address;
use ValdeirPsr\PagSeguro\Domains\Document;
use ValdeirPsr\PagSeguro\Domains\PaymentMethod\CreditCard;
use ValdeirPsr\PagSeguro\Domains\Logger\Logger;
use ValdeirPsr\PagSeguro\Request\Sale;

class ControllerExtensionPaymentPagSeguroCredit extends Controller
{
    const EXTENSION_PREFIX = 'payment_pagseguro_';

    /**
     * Exibe formulário para o cliente informar os dados do cartão
     */
    public function index()
    {
        Logger::getInstance([
            'enabled' => $this->config->get(self::EXTENSION_PREFIX . 'debug')
        ]);

        $data = $this->language->load('extension/payment/pagseguro_credit');

        $this->load->model('extension/payment/pagseguro');
        $this->load->model('checkout/order');

        $environment_name = $this->config->get(self::EXTENSION_PREFIX . 'sandbox') == 1 ? 'debug' : 'production';
        $custom_field_cpf_id = $this->config->get(self::EXTENSION_PREFIX . 'custom_fields_cpf');
        $custom_field_birthday_id = $this->config->get(self::EXTENSION_PREFIX . 'custom_fields_birthday');

        try {
            $data['session'] = $this->model_extension_payment_pagseguro->generateSession();
        } catch (AuthException $e) {
            $data['warning'] = $this->language->get(sprintf('error_%s_auth', $environment_name));
        } catch (Exception $e) {
            $data['warning'] = $this->language->get(sprintf('error_%s_unknown', $environment_name));
        }

        if ($environment_name === 'debug') {
            $data['javascript_src'] = 'https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js';
        } else {
            $data['javascript_src'] = 'https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js';
        }

        $order_id = $this->session->data['order_id'];

        $order_info = $this->model_checkout_order->getOrder($order_id);

        $data['cpf'] = $order_info['custom_field'][$custom_field_cpf_id] ?? '';
        $data['maxInstallmentNoInterest'] = $this->config->get(self::EXTENSION_PREFIX . 'installment_free');
        $data['amount'] = $order_info['total'];

        if ($custom_field_birthday_id) {
            $data['birthday'] = $order_info['custom_field'][$custom_field_birthday_id] ?? '';
        } else {
            $data['birthday'] = null;
        }

        if ($order_info['telephone']) {
            $data['phone'] = $order_info['telephone'];
        } else {
            $data['phone'] = null;
        }

        $data['validate'] = [
            'month' => range(1, 12),
            'year' => range(date('Y'), date('Y') + 30)
        ];

        $data['installment_minimum_value'] = $this->config->get(self::EXTENSION_PREFIX . 'installment_minimum_value');

        $data['action_create_sale'] = $this->url->link('extension/payment/pagseguro_credit/transaction', 'order_id=' . $order_id, true);
        $data['confirm'] = $this->url->link('extension/payment/pagseguro_credit/confirm', 'order_id=' . $order_id . '&code=', true);

        return $this->load->view('extension/payment/pagseguro_credit', $data);
    }

    /**
     * Cria a transação no PagSeguro
     */
    public function transaction()
    {
        Logger::getInstance([
            'enabled' => $this->config->get(self::EXTENSION_PREFIX . 'debug')
        ]);

        $this->response->addHeader('Content-Type: application/json');

        $this->load->language('extension/payment/pagseguro_credit');

        $senderHash = $this->request->post['sender_hash'] ?? null;
        $cardToken = $this->request->post['card_token'] ?? null;
        $cardHolderName = $this->request->post['card_holder_name'] ?? null;
        $cardHolderCpf = $this->request->post['card_holder_cpf'] ?? null;
        $cardHolderBirthdate = $this->request->post['card_holder_birthdate'] ?? null;
        $installmentQuantity = $this->request->post['installment_quantity'] ?? null;
        $installmentValue = $this->request->post['installment_value'] ?? null;

        $this->load->model('checkout/order');
        $this->load->model('extension/payment/pagseguro');

        $order_id = $this->session->data['order_id'] ?? 0;

        $order_info = $this->model_checkout_order->getOrder($order_id);

        if (!$order_info) {
            $this->setOutputJson([
                'errors' => ['error' => 'Order ID invalid']
            ], 400, 'Bad Request');

            return;
        }

        $result = [];

        try {
            $customer_document = Document::cpf(preg_replace('/\D/', '', $cardHolderCpf));

            $sender = FactoryUser::sender(
                sprintf('%s %s', $order_info['firstname'], $order_info['lastname']),
                $order_info['email'],
                $order_info['telephone'],
                $customer_document,
                $senderHash
            );

            $products = $this->model_checkout_order->getOrderProducts($order_id);

            $items = [];

            foreach ($products as $key => $product) {
                $item = new CartItem();
                $item->setId(sprintf('ID%d_K%d', $product['product_id'], $key));
                $item->setDescription($product['name'] . '  ::  ' . $product['model']);
                $item->setQuantity(intval($product['quantity']));
                $item->setAmount(number_format($product['price'], 2, '.', ''));
                $items[] = $item;
            }

            $vouchers = $this->session->data['vouchers'] ?? [];

            foreach ($vouchers as $key => $voucher) {
                $item = new CartItem();
                $item->setId(sprintf('Voucher_%s', $key));
                $item->setDescription($voucher['description']);
                $item->setQuantity(1);
                $item->setAmount(number_format($voucher['amount'], 2, '.', ''));
                $items[] = $item;
            }

            $order_totals = $this->model_checkout_order->getOrderTotals($order_id);

            $token = $this->config->get(self::EXTENSION_PREFIX . 'callback_token');
            $extra_amount = $this->model_extension_payment_pagseguro->getExtraAmount($order_totals);

            $payment = new Payment();
            $payment->setMode('default');
            $payment->setSender($sender);
            $payment->setCurrency('BRL');
            $payment->setNotificationUrl($this->url->link('extension/payment/pagseguro/callback', 'order_id=' . $this->session->data['order_id'] . '&token=' . $token, true));
            $payment->setCartItems($items);
            $payment->setExtraAmount($extra_amount);
            $payment->setReference($order_info['comment']);

            $custom_field_number_id = $this->config->get(self::EXTENSION_PREFIX . 'custom_fields_number');

            $address_number = $order_info['payment_custom_field'][$custom_field_number_id] ?? null;

            $billingAddress = new Address();
            $billingAddress->setStreet($order_info['payment_address_1']);
            $billingAddress->setNumber($address_number);
            $billingAddress->setDistrict($order_info['payment_address_2']);
            $billingAddress->setCity($order_info['payment_city']);
            $billingAddress->setState($order_info['payment_zone_code']);
            $billingAddress->setPostalCode($order_info['payment_postcode']);
            $billingAddress->setComplement($order_info['payment_company']);

            $custom_field_birthdate_id = $this->config->get(self::EXTENSION_PREFIX . 'custom_fields_birthday');

            $holder = FactoryUser::holder(
                $cardHolderName,
                $order_info['email'],
                $order_info['telephone'],
                Document::cpf($cardHolderCpf)
            );

            if ($cardHolderBirthdate) {
                $holder->setBirthdate(DateTime::createFromFormat('Y-m-d', $cardHolderBirthdate));
            }

            $installmentFree = $this->config->get(self::EXTENSION_PREFIX . 'installment_free');

            $creditCard = new CreditCard();
            $creditCard->setToken($cardToken);
            $creditCard->setInstallmentQuantity($installmentQuantity);
            $creditCard->setInstallmentValue(number_format($installmentValue, 2, '.', ''));
            $creditCard->setBillingAddress($billingAddress);
            $creditCard->setHolder($holder);

            if ($installmentFree) {
                $creditCard->setNoInterestInstallmentQuantity(intval($installmentFree));
            }

            $payment->setPayment($creditCard);

            $shipping = new Shipping();
            $shipping->setAddressRequired($this->cart->hasShipping());

            if ($this->cart->hasShipping()) {
                $address = new Address(
                    $order_info['shipping_address_1'],
                    $order_info['shipping_custom_field'][$custom_field_number_id] ?? null,
                    $order_info['shipping_address_2'],
                    $order_info['shipping_city'],
                    $order_info['shipping_zone_code'],
                    preg_replace('/\D/', '', $order_info['shipping_postcode']),
                    $order_info['shipping_company']
                );

                $shipping_cost = $this->model_extension_payment_pagseguro->getShippingCost($order_totals);

                $shipping->setType(ShippingTypes::UNKNOWN);
                $shipping->setCost(number_format($shipping_cost, 2, '.', ''));
                $shipping->setAddress($address);

            }

            $payment->setShipping($shipping);

            $env = $this->model_extension_payment_pagseguro->factoryEnvironment();
            $environment_name = $this->config->get(self::EXTENSION_PREFIX . 'sandbox') == 1 ? 'debug' : 'production';

            $sale = new Sale($env);
            $response = $sale->create($payment);

            $this->model_extension_payment_pagseguro->addOrder($order_id, $response);

            $this->setOutputJson([
                'success' => true,
                'code' => $response->getCode()
            ]);
        } catch (AuthException $e) {
            $this->setOutputJson(['errors' => [
                'error' => $this->language->get(sprintf('error_%s_auth', $environment_name))
            ]]);
        } catch (PagSeguroRequestException $e) {
            $this->setOutputJson(['errors' => array_map(function ($error) {
                return $error->getMessage();
            }, $e->getErrors())]);
        } catch (InvalidArgumentException $e) {
            $this->setOutputJson(['errors' => [
                'error' => $e->getMessage()
            ]]);
        } catch (Exception $e) {
            $this->setOutputJson(['errors' => [
                'error' => $e->getMessage()
            ]]);
        }
    }

    /**
     * Realiz a confirmação do pedido
     */
    public function confirm() {
        $confirm = $this->request->get['code'] ?? null;

        if ($confirm) {
            $this->load->model('checkout/order');
            $this->load->model('extension/payment/pagseguro');

            $env = $this->model_extension_payment_pagseguro->factoryEnvironment();

            $status = 0;

            $sale = new Sale($env);
            $payment = $sale->info($confirm);
            $status = $payment->getStatus();

            switch ($status) {
                case 1:
                    $status = $this->config->get(self::EXTENSION_PREFIX . 'order_status_pending');
                    break;
                case 2:
                    $status = $this->config->get(self::EXTENSION_PREFIX . 'order_status_analysing');
                    break;
                case 3:
                    $status = $this->config->get(self::EXTENSION_PREFIX . 'order_status_paid');
                    break;
                case 4:
                    $status = $this->config->get(self::EXTENSION_PREFIX . 'order_status_available');
                    break;
                case 5:
                    $status = $this->config->get(self::EXTENSION_PREFIX . 'order_status_disputed');
                    break;
                case 6:
                    $status = $this->config->get(self::EXTENSION_PREFIX . 'order_status_returned');
                    break;
                case 7:
                    $status = $this->config->get(self::EXTENSION_PREFIX . 'order_status_cancelled');
                    break;
                default:
                    $status = $this->config->get(self::EXTENSION_PREFIX . 'order_status_pending');
                    break;
            }

            if (isset($this->session->data['order_id'])) {
                $order_id = $this->session->data['order_id'];
            } else {
                $order_id = $this->request->get["order_id"];
            }

            $customer_notify = $this->config->get(self::EXTENSION_PREFIX . 'customer_notify');

            $this->model_checkout_order->addOrderHistory($order_id, $status, '', $customer_notify);

            if (isset($this->session->data['order_id'])) {
                $this->cart->clear();
                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
                unset($this->session->data['payment_method']);
                unset($this->session->data['payment_methods']);
                unset($this->session->data['comment']);
                unset($this->session->data['coupon']);
            }

            $this->response->redirect($this->url->link('checkout/success'));
        }
    }

    /**
     * Retorna um JSON válido
     *
     * @param array $response
     */
    private function setOutputJson(array $response = []) {
        $statusCode = isset($result['errors']) ? 400 : 200;
        $statusName = $statusCode === 400 ? 'Bad Request' : 'OK';

        header("HTTP/1.0 $statusCode $statusName");
        echo json_encode($response);
        die();
    }
}
