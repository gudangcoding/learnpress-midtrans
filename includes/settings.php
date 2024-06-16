<?php

add_filter('learn_press_payment_method_settings', 'midtrans_payment_settings');

function midtrans_payment_settings($settings)
{
    $midtrans_settings = array(
        array(
            'title'   => __('Midtrans Settings', 'learnpress-midtrans'),
            'id'      => 'midtrans_settings',
            'type'    => 'title',
        ),
        array(
            'title'   => __('Merchant ID', 'learnpress-midtrans'),
            'id'      => 'midtrans_merchant_id',
            'type'    => 'text',
            'default' => '',
        ),
        array(
            'title'   => __('Server Key', 'learnpress-midtrans'),
            'id'      => 'midtrans_server_key',
            'type'    => 'text',
            'default' => '',
        ),
        array(
            'title'   => __('Client Key', 'learnpress-midtrans'),
            'id'      => 'midtrans_client_key',
            'type'    => 'text',
            'default' => '',
        ),
        array(
            'type' => 'sectionend',
            'id'   => 'midtrans_settings',
        ),
    );

    return array_merge($settings, $midtrans_settings);
}
