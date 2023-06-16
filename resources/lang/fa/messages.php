<?php

return [
    'cache-clear' => [
        'successful' => 'کش نرم افزار با موفقیت حذف شذ.',
    ],
    'create-database-backup' => [
        'successful' => 'فایل پیشتیبان پایگاه داده نرم افزار با موفقیت ایجاد شد.',
    ],
    'appearance-restore' => [
        'successful' => 'تنظیمات رنگ ها به حالت اولیه تبدیل شد.',
    ],
    'successful' => 'اجرای عملیات موفقیت آمیز بود.',
    'failed' => 'در اجرای درخواست خطایی رخ داده است.',
    'request-expired' => 'درخواست شما منقضی شده است.',
    'errors' => [
        '404' => 'صفحه پیدا نشد',
        '503' => 'نرم افزار در حال بروزرسانی است',
    ],
    'atipay' => [
        'withdraw-fiat' => [
            'Invalid destination iban number/ach-transfer' => 'شباء مقصد نامعتبر است'
        ],
    ],
    'kyc' => [
        'status'    =>  'وضعیت جدید احراز هویت کاربر',
        'errors'  => [
            'exist-client-token' => 'با این شناسه قبلا درخواستی ثبت شده است.',
            'disagreement-client-token' => 'شناسه شما از قبل در سیستم ثبت شده است و با این شناسه مغایرت دارد!'
        ],
        'status-code' =>  [
            '200' => 'عملیات با موفقیت انجام شد',
            '429' => 'درخواست بیش از حد مجاز',
            '400' => 'درخواست نامعتبر',
            '500' => 'خطای داخلی سرور',
            '409' => 'شما قبلا احراز هویت شده اید',
            '404' => 'میزبان یا کاربر مورد نظر یافت نشد',
            '503' => 'سرویس در دسترس نمی باشد',
            '417' => 'ویدیو نامعتبر',
            '403' => 'دسترسی غیر مجاز به سرویس',
            '1'   => 'احرازهویت با موفقیت به پایان رسید',
            '2'   => 'دوربین پشتیبانی نمیشود',
            '3'   => 'دسترسی دوربین به SDK داده نشد',
            '4'   => 'خطای ناشناخته',
            '5'   => 'ورودی‌های SDk نامعتبر است',
            '6'   => 'ثبت احوال در دسترس نیست',
            '7'   => 'کاربر قبلا فرایند را طی کرده است',
            '8'   => 'درخواست بیش از حد مجاز',
            '9'   => 'کاربر در مرحله‌ی تصویر امضا، فرایند را لغو کرده',
            '10'   => 'در مرحله‌ی ویدیوی سلفی، فرایند ناموفق به پایان رسید',
            '11'   => 'عدم تطابق اطلاعات کاربر و ثبت احوال',
            'SUCCESS' => 'success',
            'SUCCESS_SUPERVISOR' => 'pending',
            'WAITING' => 'pending',
            'FAILED'  => 'error',
        ],
        'success'  => 'موفقیت آمیز',
        'pending'  => 'در حال بررسی',
        'error'    => 'خطا',
    ],
    'throttle' => 'تعداد درخواست بیش از حد مجاز است.',
    'permissions' => 'حساب شما مجوز اجرای این عملیات را ندارد.',
    'auth' => [
        'register' => [
            'invalidCredential' => 'اطلاعات ورودی صحیح نیست',
            'alreadyExists' => 'این اطلاعات قبلا برای ثبت نام استفاده شده',
            'successful' => 'ثبت نام با موفقیت انجام شد',
        ],
        'login' => [
            'successful' => 'ورود به حساب موفق بود',
            'failed' => 'اطلاعات ورود صحیح نمی باشد',
            'notAuthenticated' => 'مجاز به عملیات نیستید',
            'alreadyLoggedIn' => 'قبلا وارد حساب کابری خود شده اید'
        ],
        'forgot' => [
            'successful' => 'تغییر کلمه عبور موفق بود و ارسال شده به :destination شما',
            'newPassword' => [
                'subject' => 'کلمه عبور جدید شما',
                'body' => 'کلمه عبور جدید   :password'
            ],
        ],
        'logout' => [
            'successful' => 'شما از حساب کاربری خارج شدید',
        ],
        'otp' => [
            'wait' => 'لطفا :seconds ثانیه صبر کنید ',
            'enterToken' => 'لطفا رمز یکبار مصرف ارسال شده به :destination را وارد کنید',
            'EmailVerification' => 'تایید ایمیلی',
            'YourVerificationCode' => 'کد احراز هویت شما :token میباشد.',
            'wrongToken' => 'کد وارد شده صحیح نیست.',
            'successful' => 'احراز هویت شما با موفقیت انجام شد.',
            'resend' => [
                'successful' => 'درخواست ارسال رمز یک بارمصرف انجام شد.',
            ],
            'failed' => 'ارسال کد تایید با خطا مواجه شده است.',
        ],
        '2fa' => [
            'enter2fa' => 'رمز دوم خود را وارد کنید',
            'alreadyEnabled' => 'ورود دو مرحله ای قبلا فعال شده است',
            'alreadyDisabled' => 'ورود دو مرحله ای قبلا غیر فعال شده است',
            'alreadyVerified' => 'ورود دو مرحله ای قبلا احراز شده است',
            'notEnoughData' => 'اطلاعات حساب کاربری تکمیل نیست',
            'notEnabled' => 'ورود دو مرحله برای شما فعال نیست',
            'enable' => [
                'successful' => 'ورود دو مرحله ای فعال شد'
            ],
            'disable' => [
                'successful' => 'ورود دو مرحله ای غیر فعال شد'
            ],
        ],
        'invalidEmailMobile' => 'ایمیل یا شماره همراه نامعتبر است.',
        'invalidEmail' => 'ایمیل نامعتبر است.',
        'needAgree' => 'شما باید موافقت کنید.',
        'weekPassword' => 'پسورد ضعیف است.',
        'passwordRequired' => 'گذرواژه الزامی است.',
        'passwordMustMatch' => 'رمزهای عبور باید مطابقت داشته باشند.',

    ],
    'profile' => [
        'password' => [
            'successful' => 'کلمه عبور تغییر کرد',
            'currentPasswordIsWrong' => 'کلمه عبور فعلی صحیح وارد نشده است',
            'samePasswordError' => 'کلمه عبور جدید نباید با قبلی یکی باشد',
        ],
        'update' => [
            'successful' => 'اطلاعات شما به روز شد',
        ],
    ],
    'referrals' => [
        'submitReferrer' => [
            'alreadySubmitted' => 'معرف شما از قبل ثبت شده است.',
            'successful' => 'معرف شما ثبت شد',
            'wrongReferralToken' => 'چنین معرفی وجود ندارد',
        ],
    ],
    'tickets' => [
        'store' => [
            'successful' => 'درخواست شما ثبت شد',
        ],
        'close' => [
            'successful' => 'درخواست شما بسته شد',
        ],
        'answer' => [
            'successful' => 'پاسخ شما ثبت شد',
        ],
    ],
    'WalletAddresses' => [
        'isAllocated' => 'والت های در حال استفاده نمی توانند تغییر کنند و یا حذف شوند',
        'store' => [
            'successful' => 'آدرس والت جدید اضافه شد'
        ],
        'update' => [
            'successful' => 'آدرس والت ویرایش شد'
        ],
        'destroy' => [
            'successful' => 'آدرس والت حذف شد'
        ],
    ],
    'wallets' => [
        'deposit' => [
            'notAvailable' => 'امکان واریز وجود ندارد ، بعدا امتحان کنید',
            'successful' => 'واریز ثبت شد.',
            'select-blockchain' => 'بلاکچین مدنظر را انتخاب نمایید:',
            'update' => [
                'successful' => 'واریز مدنظر با موفقیت ویرایش شد.',
                'failed' => 'ویرایش واریز مدنظر با خطا مواجه شده است.'
            ],
            'online-payment' => [
                'notFound' => 'درخواست واریز یافت نشد.',
                'failed' => 'پرداخت‌ آنلاین شما با خطا مواجه شده است.',
                'successful' => 'پرداخت آنلاین شما با موفقیت ثبت شد.',
            ],
        ],
        'withdraw' => [
            'notFound' => 'درخواست برداشت یافت نشد',
            'notValidCurrency' => 'دارایی انتخاب شده معتبر نمیباشد.',
            'notValidBankAccount' => 'حساب مقصد یافت نشد.',
            'successful' => 'برداشت با موفقیت انجام شد',
            'insufficientBalance' => 'موجودی کافی نیست',
            'cancelWithdraw' => [
                'successful' => 'درخواست برداشت لغو شد',
            ],
            'update' => [
                'successful' => 'درخواست برداشت ویرایش شد',
                'failed' => 'ویرایش درخواست برداشت موفق نبود',
            ],
        ],
        'reset-virtual-assets' => [
            'successful' => 'بازگردانی موجودی های مجازی با موفقیت انجام شد.',
        ],
    ],
    'exchange' => [
        'orders' => [
            'store' => [
                'successful' => 'سفارش دریافت شد.',
                'invalidMarket' => 'بازار معتبر نیست.',
                'insufficientBalance' => 'موجودی شما کافی نیست.',
                'failed' => 'ثبت سفارش با خطا مواجه شده است.',
            ],
            'cancel' => [
                'received' => 'درخواست انصراف دریافت شد.',
                'successful' => 'سفارش کنسل شد.',
                'failed' => 'مشکلی در کنسل کردن سفارش پیش آمده است.',
                'uncancelable' => 'سفارش مدنظر قابل کنسل کردن نمیباشد.',
            ],
        ],
        'price-tickers' => [
            'failed' => 'خطایی در دریافت لیست قیمت ها رخ داده است.',
        ],
    ],
    'notifications' => [
        'store' => [
            'successful' => 'اعلان با موفقیت ثبت شد.'
        ],
        'destroy' => [
            'successful' => 'اعلان حذف شد.',
        ],
        'presets' => [
            'user-document-update' => [
                'title' => 'مدرک آپلود شده به روز شد.',
            ],
            'withdraw-update' => [
                'title' => 'درخواست برداشت به روز شد.'
            ],
            'deposit-store' => [
                'title' => 'واریز شما دریافت شد.'
            ],
            'order-update' => [
                'title' => 'سفارش :order به روز شد.',
            ],
            'order-rejected' => [
                'title' => 'سفارش شما رد شد.',
            ],
        ],
    ],
    'bankAccounts' => [
        'store' => [
            'successful' => 'اطلاعات اکانت ثبت شد.',
        ],
        'update' => [
            'successful' => 'اطلاعات اکانت به روز شد.',
        ],
        'destroy' => [
            'successful' => 'اکانت حذف شد.',
        ],
        'notFound' => 'لطفاً ابتدا یک حساب ایجاد نمایید.',

    ],
    'roles' => [
        'store' => [
            'successful' => 'نقش جدیداضافه شد',
        ],
        'update' => [
            'successful' => 'نقش جدید به روز شد',
        ],
        'destroy' => [
            'successful' => 'نقش مورد نظر حذف شد',
        ],
    ],
    'users' => [
        'update' => [
            'successful' => 'اطلاعات کاربر به روز شد',
        ],
        'store' => [
            'successful' => 'کاربر جدید با موفقیت ایجاد شد.',
        ],
    ],
    'documents' => [
        'notFound' => 'مدرک مورد نظر پیدا نشد.',
        'store' => [
            'successful' => 'مدرک جدید ثبت شد.',
        ],
        'update' => [
            'successful' => 'مدرک به روز شد.',
        ],
        'destroy' => [
            'successful' => 'مدرک حذف شد.',
            'failed' => 'مدرک قابل حذف نمیباشد.',
        ],
    ],
    'admins' => [
        'store' => [
            'successful' => 'مدیر جدید اضافه شد',
        ],
        'update' => [
            'successful' => 'مشخصات مدیر مورد نظر به روز شد',
        ],
    ],
    'settings' => [
        'update' => [
            'successful' => 'تنظیمات با موفقیت به روز شد.',
        ],
    ],
    'otc' => [
        'isNotEnabled' => 'سرویس سفارش سریع غیر فعال است.',

        'rules' => [
            'store' => [
                'successful' => 'قانون قیمت با موفقیت ایجاد شد.',
                'alreadyExists' => 'قانون قیمت برای بازار مدنظر از قبل وجود دارد.',
            ],
            'update' => [
                'successful' => 'قانون قیمت با موفقیت ویرایش شد.',
            ],
            'destroy' => [
                'successful' => 'قانون قیمت با موفقیت حذف شد.'
            ],
        ],
        'orders' => [
            'store' => [
                'successful' => 'سفارش شما با موفقیت ثبت شد.',
                'insufficientSystemBalance' => 'موجودی صندوق جهت تامین سفارش شما کافی نمیباشد.',
                'insufficientUserBalance' => 'موجودی شما جهت ثبت سفارش کافی نمیباشد.',
            ],
        ],
    ],
    'groups' => [
        'store' => [
            'successful' => 'گروه جدید ایجاد شد.',
        ],
        'update' => [
            'successful' => 'گروه ویرایش شد.',
        ],
        'destroy' => [
            'successful' => 'گروه حذف شد.'
        ]
    ],
    'privacy-policies' => [
        'store' => [
            'successful' => 'توافق نامه ایجاد شد.',
        ],
        'update' => [
            'successful' => 'توافق نامه ویرایش شد.',
        ],
        'destroy' => [
            'successful' => 'توافق نامه حذف شد.'
        ]
    ],
    'blockchains' => [
        'store' => [
            'successful' => 'بلاکچین ایجاد شد.',
        ],
        'update' => [
            'successful' => 'بلاکچین ویرایش شد.',
        ],
        'destroy' => [
            'successful' => 'بلاکچین حذف شد.'
        ]
    ],
    'symbols' => [
        'store' => [
            'successful' => 'نماد جدید با موفقیت ایجاد شد.',
            'alreadyExists' => 'نماد مدنظر از قبل موجود است.',
        ],
        'update' => [
            'successful' => 'نماد مدنظر با موفقیت ویرایش شد.',
        ],
        'destroy' => [
            'successful' => 'نماد مدنظر با موفقیت حذف شد.',
            'failed' => 'نماد مدنظر در حال استفاده است و قابل خذف نمیباشد.'
        ]
    ],
    'markets' => [
        'notFound' => 'بازار مدنظر یافت نشد.',
        'store' => [
            'successful' => 'بازار جدید با موفقیت ایجاد شد.',
            'symbolNotValid' => 'نماد انتخاب شده معتبر نمیباشد.',
            'alreadyExists' => 'بازار مدنظر از قبل موجود است.',
        ],
        'update' => [
            'successful' => 'بازار مدنظر با موفقیت ویرایش شد.',
        ],
        'destroy' => [
            'successful' => 'بازار مدنظر با موفقیت حذف شد.',
            'failed' => 'بازار مدنظر در حال استفاده است و قابل حذف نمیباشد.'
        ]
    ],
    'accountancy' =>  [
        'store' =>  [
            'successful'    =>  'ترازنامه جدید با موفقیت ایجاد شد.'
        ],
    ],
    'authentication' =>  [
        'error' =>  [
            'nationalCode' =>  'عملیات احراز هویت نیازمند ثبت کد ملی از بخش اطلاعات هویتی می باشد.'
        ],
        'perform' => 'لطفا از بخش حساب کاربری، مراحل احراز هویت را انجام دهید.'
    ],
    'tradeCreated'  =>  'معامله جدید: :side :quantity :srcSymbol به قیمت :price :dstSymbol',
    'normalFiatTransfer'  => 'شما در حال انتقال :amount تومان به شماره کارت :card هستید، آیا تایید می کنید؟',
    'payaFiatTransfer'  => 'شماره در حال انتقال :amount تومان به شبا :ibanNumber هستید، آیا تایید می کنید؟',
    'increase-credit'   => 'افزایش اعتبار',
    'withdraw-fiat'   => 'برداشت ریالی',

    //
    'not-fount' => 'پیدا نشد.',
    'exceptions' => [
        'primary' => [
            \App\Exceptions\Primary\NotFoundException::MARKET_NOT_FOUND => 'مارکت یافت نشد',
            \App\Exceptions\Primary\NotFoundException::ORDER_NOT_FOUND => 'سفارش یافت نشد',
            \App\Exceptions\Primary\NotFoundException::USER_NOT_FOUND => 'کاربر مورد نظر یافت نشد',
            \App\Exceptions\Primary\NotFoundException::WALLET_ADDRESS_NOT_FOUND => 'آدرس کیف پول یافت نشد',
            \App\Exceptions\Primary\NotFoundException::BLOCKCHAIN_NOT_FOUND => 'بلاک چین مورد نظر یافت نشد',
            \App\Exceptions\Primary\NotFoundException::DEPOSIT_NOT_FOUND => 'رکورد واریز یافت نشد',
        ],
    ],
];
