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
use ValdeirPsr\PagSeguro\Domains\PaymentMethod\Boleto;
use ValdeirPsr\PagSeguro\Domains\Logger\Logger;
use ValdeirPsr\PagSeguro\Request\Sale;

class ControllerExtensionPaymentPagSeguroBoleto extends Controller
{
    const EXTENSION_PREFIX = 'payment_pagseguro_';

    /**
     * Exibe formulário com o campo CPF e botões para confirmar o pedido,
     * imprimir ou baixar o boleto
     */
    public function index()
    {
        Logger::getInstance([
            'enabled' => $this->config->get(self::EXTENSION_PREFIX . 'debug')
        ]);

        $data = $this->language->load('extension/payment/pagseguro_boleto');

        $this->load->model('extension/payment/pagseguro');

        $environment_name = $this->config->get(self::EXTENSION_PREFIX . 'sandbox') == 1 ? 'debug' : 'production';
        $custom_field_cpf_id = $this->config->get(self::EXTENSION_PREFIX . 'custom_fields_cpf');

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

        $custom_fields = $this->model_extension_payment_pagseguro->getCustomFieldValues($order_id);

        $data['cpf'] = $custom_fields[$custom_field_cpf_id] ?? '';

        $data['action_create_sale'] = $this->url->link('extension/payment/pagseguro_boleto/transaction', 'order_id=' . $this->session->data['order_id'], true);
        $data['action_download_boleto'] = $this->url->link('extension/payment/pagseguro_boleto/download', 'url=', true);
        $data['confirm'] = $this->url->link('extension/payment/pagseguro_boleto/confirm', 'code=', true);

        return $this->load->view('extension/payment/pagseguro_boleto', $data);
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

        $this->load->language('extension/payment/pagseguro_boleto');

        $cpf = $this->request->post['cpf'] ?? null;
        $hash = $this->request->post['hash'] ?? '';
        $order_id = $this->session->data['order_id'] ?? 0;
        $custom_field_number_id = $this->config->get(self::EXTENSION_PREFIX . 'custom_fields_number') ?? 0;

        $this->load->model('checkout/order');
        $this->load->model('extension/payment/pagseguro');

        $order_info = $this->model_checkout_order->getOrder($order_id);

        if (!$order_info) {
            $this->response->setOutput(json_encode([
                'error' => $this->language->get('error_order_not_found')
            ]));

            return;
        }

        try {
            $customer_document = Document::cpf(preg_replace('/\D/', '', $cpf));

            $sender = FactoryUser::sender(
                sprintf('%s %s', $order_info['firstname'], $order_info['lastname']),
                $order_info['email'],
                $order_info['telephone'],
                $customer_document,
                $hash
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
            $payment->setPayment(new Boleto());

            if ($this->cart->hasShipping()) {
                $address = new Address(
                    $order_info['shipping_address_1'],
                    $order_info['shipping_custom_field'][$custom_field_number_id],
                    $order_info['shipping_address_2'],
                    $order_info['shipping_city'],
                    $order_info['shipping_zone_code'],
                    preg_replace('/\D/', '', $order_info['shipping_postcode']),
                    $order_info['shipping_company']
                );

                $shipping_cost = $this->model_extension_payment_pagseguro->getShippingCost($order_totals);

                $shipping = new Shipping();
                $shipping->setType(ShippingTypes::UNKNOWN);
                $shipping->setCost(number_format($shipping_cost, 2, '.', ''));
                $shipping->setAddressRequired(true);
                $shipping->setAddress($address);

                $payment->setShipping($shipping);
            }

            $env = $this->model_extension_payment_pagseguro->factoryEnvironment();
            $environment_name = $this->config->get(self::EXTENSION_PREFIX . 'sandbox') == 1 ? 'debug' : 'production';
            $result = [];

            $sale = new Sale($env);
            $response = $sale->create($payment);

            $this->model_extension_payment_pagseguro->addOrder($order_id, $response);

            $this->setOutputJson([
                'payment_link' => $response->getPayment()->getPaymentLink(),
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
     * Realiza o download de um boleto
     */
    public function download()
    {
        $order_id = $this->session->data['order_id'] ?? $this->request->get['order_id'] ?? null;

        if ($order_id) {
            if (isset($this->request->get['url'])) {
                $url = base64_decode($this->request->get['url']);
                if (substr($url, 0,4)!=='http') $url = $this->request->get['url'];
            } elseif (isset($this->request->get['transaction_id'])) {
                $this->load->model('extension/payment/pagseguro');
                $env = $this->model_extension_payment_pagseguro->factoryEnvironment();
                $sale = new Sale($env);
                $transaction = $sale->info($this->request->get['transaction_id']);
                $url = $transaction->getPayment()->getPaymentLink();
            } else {
                die('PSR');
            }

            $url = str_replace('print.jhtml', 'print_pdf.jhtml', $url);

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=psr_pedido_' . $order_id . '.pdf');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            ob_clean();
            flush();
            echo file_get_contents($url);
            exit;
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
            $statusP = $payment->getStatus();

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

            header('location: ' . $this->url->link('checkout/success'));
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
