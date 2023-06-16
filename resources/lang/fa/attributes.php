<?php

return [
    'guards' => [
        'admin' => 'مدیر',
        'user' => 'کاربر',
    ],
    'blockchains' => [
        'BTC' => 'بیت کوین',
        'ETH' => 'اتریوم',
        'TRX' => 'ترون'
    ],
    'exchange' => [
        'orders' => [
            'status' => [
                'NEW' => 'سفارش جدید',
                'PARTIALLY_FILLED' => 'در حال تکمیل',
                'PENDING' => 'در انتظار بررسی',
                'FILLED' => 'تکمیل شده',
                'CANCELED' => 'منصرف شده',
                'PENDING_CANCELED' => 'در حال انصراف',
                'REJECTED' => 'رد شده توسط سیستم',
                'EXPIRED' => 'منقضی شده'
            ],
            'sides' => [
                'BUY' => 'خرید',
                'SELL' => 'فروش',
            ],
            'types' => [
                'LIMIT' => 'لیمیت',
                'STOPLOSSLIMIT' => 'استاپ لیمیت',
                'MARKET' => 'مارکت',
            ],
        ],
        'charts' => [
            'trading-view' => 'نمودار تکنیکال',
            'market-depth' => 'نمودار عمق بازار',
        ],
        'attributes' => [
            'order-book' => 'لیست سفارش ها',
            'assets' => 'دارایی ها',
            'charts' => 'نمودار',
        ],
        'queries' => [
            'open-orders' => 'سفارش های باز',
            'recent-orders' => 'سفارش های اخیر',
            'history' => 'تاریخچه',
            'market-orders' => 'معاملات بازار',
            'my-orders' => 'معاملات من',
            'filled-orders' => 'معاملات اخیر',
            'all-orders' => 'سفارش ها',
        ],
        '24HrTicker' => [
            'change' => 'تغییر قیمت',
            'high' => 'بالاترین قیمت',
            'low' => 'پایین ترین قیمت',
            'volume' => 'حجم معاملات'
        ],
    ],
    'logs' => [
        'events' => [
            0 => 'خروج',
            1 => 'ورود موفق',
            2 => 'تغییر گذرواژه'
        ],
        'devices' => [
            'desktop' => 'کامپیوتر رومیزی',
            'mobile' => 'تلفن همراه',
            'tablet' => 'تبلت'
        ],
    ],
    'transactions' => [
        'type' => [
            1 => 'جایزه معرفی دوستان',
            2 => 'کارمزد معامله',
        ],
        'side' => [
            1 => 'واریز',
            2 => 'برداشت',
        ]
    ],
    'tickets' => [
        'status' => [
            0 => 'بسته شده',
            1 => 'در انتظار پاسخ مدیر',
            2 => 'در انتظار پاسخ کاربر'
        ],
    ],
    'wallets' => [
        1 => 'آزاد',
        2 => 'بلوکه'
    ],
    'withdraws' => [
        0 => 'رد شده',
        1 => 'در انتظار بررسی',
        2 => 'تکمیل شده',
        3 => 'انصراف',
    ],
    'documents' => [
        'status' => [
            0 => 'رد شده',
            1 => 'در انتظار بررسی',
            2 => 'تایید شده',
        ],
    ],
    'copy' => 'کپی',
    'default' => [
        'yes' => 'بله',
        'no' => 'خیر',
    ],
    'notification' => [
        'yes' => 'بله',
        'no' => 'نه',
    ],
    'departments' => [
        'finance' => 'مالی',
        'technical' => 'فنی',
        'support' => 'پشتیبانی',
    ],
    '2fa' => [
        'types' => [
            'google' => 'گوگل',
            'mobile' => 'موبایل',
            'email' => 'ایمیل',
        ],
    ],
    'bankAccounts' => [
        'types' => [
            0 => 'حساب بانکی',
            1 => 'حساب کریپتو',
            2 => 'کارت بانکی'
        ]
    ],
    'settings' => [
        'seo-setting' => 'تنظیمات سئو',
        'appearance-setting' => 'تنظیمات ظاهری',
        'api-setting' => 'تنظیمات API ها',
        'engine' => 'تنظیمات هسته معاملات',
        'deposits' => 'تنظیمات واریز ها',
        'withdraws' => 'تنظیمات برداشت ها',
        'financial-setting' => 'تنظیمات مالی',
        'order-notification' => 'تنظیمات اعلان سفارش ها',
        'orders' => 'تنظیمات سفارش ها',
        'bank-account' => 'تنظیمات حساب بانکی',
        'social-setting' => 'تنظیمات شبکه های اجتماعی',
        'IRT_deposit' => 'تنظیمات واریز ریالی',
        'IRT_withdraw' => 'تنظیمات برداشت ریالی',
        'dark_theme' => 'تنظیمات تم تیره',
        'light_theme' => 'تنظیمات تم روشن',
        'otc' => 'تنظیمات OTC',
    ],
    'manualDeposits' => [
        'status' => [
            0 => 'رد شده',
            1 => 'در انتظار بررسی',
            2 => 'تایید شده',
        ],
    ],
    'wallet_addresses' => [
        'status' => [
            0 => 'غیرفعال',
            1 => 'فعال',
        ],
        'availability' => [
            0 => 'در حال استفاده',
            1 => 'آزاد',
        ],
    ],
    'dropify' => [
        'messages' => [
            'default' => 'برای آپلود فایل این جا کلیک کنید',
            'replace' => 'بارگذاری',
            'remove' => 'حذف',
            'error' => 'خطا در بارگذاری'
        ]
    ],
    'crypto_deposit_method' => [
        1 => 'خودکار',
        2 => 'دستی',
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
                1 => 'مبدا',
                2 => 'مقصد',
            ],
        ],
    ],
];
