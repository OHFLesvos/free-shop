<?php

return [
    'welcome' => [
        'type' => 'markdown',
        'purpose' => 'Text displayed on the shop welcome page',
    ],
    'about' => [
        'type' => 'markdown',
        'purpose' => 'Text displayed on the about page',
    ],    
    'privacy-policy' => [
        'type' => 'markdown',
        'purpose' => 'Text of the privacy policy page',
    ],
    'post-checkout' => [
        'type' => 'markdown',
        'purpose' => 'Text shown after the customer completes his order',
    ],
    'message-order-registered' => [
        'type' => 'plain',
        'purpose' => 'Text sent to the customer\'s phone after completing an order',
        'required' => true,
        'default_content' => 'Hello :customer_name (ID :customer_id), we have received your order with ID #:id and will get back to you soon.',
        'help' => 'You can use the following placeholders: <code>:customer_name</code> (the customer\'s name), <code>:customer_id</code> (the customer\'s ID number), <code>:id</code> (the order ID)',
    ],
    'message-order-cancelled' => [
        'type' => 'plain',
        'purpose' => 'Text sent to the customer\'s phone if his/her order gets cancelled',
        'required' => true,
        'default_content' => 'Hello :customer_name (ID :customer_id). Your order with ID #:id has been cancelled.',
        'help' => 'You can use the following placeholders: <code>:customer_name</code> (the customer\'s name), <code>:customer_id</code> (the customer\'s ID number), <code>:id</code> (the order ID)',
    ],
    'message-order-ready' => [
        'type' => 'plain',
        'purpose' => 'Text sent to the customer\'s phone when his/her order is ready',
        'required' => true,
        'default_content' => 'Hello :customer_name (ID :customer_id). Your order with ID #:id is ready.',
        'help' => 'You can use the following placeholders: <code>:customer_name</code> (the customer\'s name), <code>:customer_id</code> (the customer\'s ID number), <code>:id</code> (the order ID)',
    ],
];
