<?php

if (!class_exists('Midtrans')) {
    require_once dirname(__FILE__) . '/midtrans-php/Midtrans.php';
}

class LP_Gateway_Midtrans extends LP_Gateway_Abstract
{

    public function __construct()
    {
        $this->id = 'midtrans';
        $this->method_title = __('Midtrans', 'learnpress-midtrans');
        $this->method_description = __('Pay with Midtrans', 'learnpress-midtrans');

        // Settings
        $this->settings = LP()->settings;

        $this->merchant_id = $this->settings->get('midtrans_merchant_id');
        $this->server_key = $this->settings->get('midtrans_server_key');
        $this->client_key = $this->settings->get('midtrans_client_key');

        \Midtrans\Config::$serverKey = $this->server_key;
        \Midtrans\Config::$isProduction = false; // Set to true for production mode
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Add hooks for payment process
        add_action('init', array($this, 'register_midtrans_payment'));
    }

    public function register_midtrans_payment()
    {
        // Midtrans payment process logic
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['midtrans-notify'])) {
            $this->handle_midtrans_notification();
        }
    }

    public function payment_form()
    {
        // Payment form for Midtrans
        echo '<div id="midtrans-payment-form"></div>';
    }

    public function process_payment($order_id)
    {
        // Process payment with Midtrans API
        $order = learn_press_get_order($order_id);

        $params = array(
            'transaction_details' => array(
                'order_id' => $order_id,
                'gross_amount' => $order->total,
            ),
            'customer_details' => array(
                'first_name' => $order->billing_first_name,
                'last_name' => $order->billing_last_name,
                'email' => $order->billing_email,
                'phone' => $order->billing_phone,
            ),
        );

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            return array(
                'result'   => 'success',
                'redirect' => $this->get_return_url($order) . '&snap_token=' . $snapToken,
            );
        } catch (Exception $e) {
            return array(
                'result'   => 'fail',
                'message'  => $e->getMessage(),
            );
        }
    }

    private function handle_midtrans_notification()
    {
        $json_result = file_get_contents('php://input');
        $result = json_decode($json_result);

        if ($result) {
            $order_id = $result->order_id;
            $transaction_status = $result->transaction_status;
            $order = learn_press_get_order($order_id);

            switch ($transaction_status) {
                case 'capture':
                case 'settlement':
                    $order->update_status('completed');
                    break;
                case 'pending':
                    $order->update_status('on-hold');
                    break;
                case 'deny':
                case 'expire':
                case 'cancel':
                    $order->update_status('failed');
                    break;
            }
        }
    }
}
