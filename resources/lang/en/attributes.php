<?php

return [
    'guards' => [
        'admin' => 'Admin',
        'user' => 'User',
    ],
    'blockchains' => [
        'BTC' => 'Bitcoin',
        'ETH' => 'Ethereum',
        'TRX' => 'Tron'
    ],
    'exchange' => [
        'orders' => [
            'status' => [
                'NEW' => 'New',
                'PARTIALLY_FILLED' => 'Partially filled',
                'PENDING' => 'Pending',
                'FILLED' => 'Filled',
                'CANCELED' => 'Canceled',
                'PENDING_CANCELED' => 'Pending canceled',
                'REJECTED' => 'Rejected',
                'EXPIRED' => 'Expired'
            ],
            'sides' => [
                'BUY' => 'Buy',
                'SELL' => 'Sell',
            ],
            'types' => [
                'LIMIT' => 'Limit',
                'STOPLOSSLIMIT' => 'Stop limit',
                'MARKET' => 'Market',
            ],
        ],
        'charts' => [
            'trading-view' => 'Technical chart',
            'market-depth' => 'Market depth chart',
        ],
        'attributes' => [
            'order-book' => 'Order book',
            'assets' => 'Assets',
            'charts' => 'Charts'
        ],
        'queries' => [
            'open-orders' => 'Open orders',
            'recent-orders' => 'Recent orders',
            'history' => 'History',
            'market-orders' => 'Market orders',
            'my-orders' => 'My orders',
            'filled-orders' => 'Trade history',
            'all-orders' => 'All orders',
        ],
        '24HrTicker' => [
            'change' => 'Price change',
            'high' => 'Highest price',
            'low' => 'Lowest price',
            'volume' => 'Volume'
        ],
    ],
    'logs' => [
        'events' => [
            0 => 'Logout',
            1 => 'Successful login',
            2 => 'Change password'
        ],
        'devices' => [
            'desktop' => 'Desktop',
            'mobile' => 'Mobile',
            'tablet' => 'Tablet'
        ],
    ],
    'transactions' => [
        'type' => [
            1 => 'Referral reward',
            2 => 'Trade wage'
        ],
        'side' => [
            1 => 'Addition',
            2 => 'Subtraction',
        ]
    ],
    'tickets' => [
        'status' => [
            0 => 'Closed',
            1 => 'Waiting for admin',
            2 => 'Waiting for user'
        ],
    ],
    'wallets' => [
        0 => 'Virtual',
        1 => 'Available',
        2 => 'Frozen'
    ],
    'withdraws' => [
        0 => 'Rejected',
        1 => 'Pending',
        2 => 'Completed',
        3 => 'Canceled',
    ],
    'documents' => [
        'status' => [
            0 => 'Rejected',
            1 => 'Pending',
            2 => 'Accepted',
        ],
    ],
    'copy' => 'Copy',
    'default' => [
        'yes' => 'Yes',
        'no' => 'No',
    ],
    'notification' => [
        'yes' => 'Yes',
        'no' => 'No',
    ],
    'departments' => [
        'finance' => 'Finance',
        'technical' => 'Technical',
        'support' => 'Support',
    ],
    '2fa' => [
        'types' => [
            'google' => 'Google',
            'mobile' => 'Mobile',
            'email' => 'Email',
        ],
    ],
    'bankAccounts' => [
        'types' => [
            0 => 'Bank account',
            1 => 'Crypto account',
            2 => 'Bank card'
        ]
    ],
    'settings' => [
        'seo-setting' => 'Seo settings',
        'appearance-setting' => 'Appearance settings',
        'api-setting' => 'APIs settings',
        'engine' => 'Trade engine settings',
        'deposits' => 'Deposits settings',
        'withdraws' => 'Withdrawals settings',
        'financial-setting' => 'Financial settings',
        'order-notification' => 'Order notifications setting',
        'bank-account' => 'Bank account settings',
        'orders' => 'Order settings',
        'social-setting' => 'Social media settings',
        'IRT_deposit' => 'IRT deposit settings',
        'IRT_withdraw' => 'IRT withdraw settings',
        'dark_theme' => 'Dark theme settings',
        'light_theme' => 'Light theme settings',
        'otc' => 'OTC settings',
    ],
    'manualDeposits' => [
        'status' => [
            0 => 'Rejected',
            1 => 'Waiting for review',
            2 => 'Approved',
        ],
    ],
    'wallet_addresses' => [
        'status' => [
            0 => 'Disable',
            1 => 'Enable',
        ],
        'availability' => [
            0 => 'Unavailable',
            1 => 'Available',
        ],
    ],
    'dropify' => [
        'messages' => [
            'default' => 'Drag and drop a file here or click',
            'replace' => 'Drag and drop a file or click to replace',
            'remove' => 'Remove',
            'error' => 'Oops, something wrong happended'
        ]
    ],
    'crypto_deposit_method' => [
        1 => 'Automatic',
        2 => 'Manual',
    ],
    'theme' => [
        'primary' => 'Primary',
        'secondary' => 'Secondary',
        'success' => 'Success',
        'warning' => 'Warning',
        'danger' => 'Danger',
        'card' => 'Card',
        'modal' => 'Modal',
        'body' => 'Body',
        'layout' => 'Layout',
        'typography_main' => 'Typography main',
        'typography_heading' => 'Typography heading',
        'typography_success' => 'Typography success',
        'typography_danger' => 'Typography danger',
        'orderbook_success' => 'Orderbook success',
        'orderbook_danger' => 'Orderbook danger',
    ],
    'otc' => [
        'symbols' => [
            'type' => [
                1 => 'base asset',
                2 => 'quote asset',
            ],
        ],
    ],
];
