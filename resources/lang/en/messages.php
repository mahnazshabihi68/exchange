<?php

return [
    'cache-clear' => [
        'successful' => 'Cache had been cleared successfully.',
    ],
    'create-database-backup' => [
        'successful' => 'New Database backup had been stored.',
    ],
    'appearance-restore' => [
        'successful' => 'Appearances had been restored to default.',
    ],
    'failed' => 'Operation faced some problems.',
    'successful' => 'Operation had been run successfully.',
    'request-expired' => 'Your request had been expired.',
    'errors' => [
        '404' => 'Not found',
        '503' => 'The system is in maintenance mode',
    ],
    'atipay' => [
        'withdraw-fiat' => [
            'Invalid destination iban number/ach-transfer' => 'Invalid destination iban number/ach-transfer'
        ],
    ],
    'kyc' => [
        'status' => 'New user authentication status',
        'errors' => [
            'exist-client-token' => 'A request has already been registered with this id',
            'disagreement-client-token' => 'Your id is already registered in the system and it is different from this id!'
        ],
        'status-code' => [
            '200' => 'Mission accomplished',
            '429' => 'Request more than allowed',
            '400' => 'Invalid request',
            '500' => 'Internal server error',
            '409' => 'You are already authenticated',
            '404' => 'The requested host or user could not be found',
            '503' => 'The service is not available',
            '417' => 'Invalid video',
            '403' => 'Unauthorized access to the service',
            '1' => 'Authentication completed successfully',
            '2' => 'Camera not supported',
            '3' => 'Camera access to SDK was not granted',
            '4' => 'Unknown error',
            '5' => 'Invalid SDK entries',
            '6' => 'Registration is not available',
            '7' => 'The user has already gone through the process',
            '8' => 'Request more than allowed',
            '9' => 'The user canceled the process at the signature image stage',
            '10' => 'At the selfie video stage, the process ended unsuccessfully',
            '11' => 'Mismatch of user information and registration',
            'SUCCESS' => 'success',
            'SUCCESS_SUPERVISOR' => 'pending',
            'WAITING' => 'pending',
            'FAILED' => 'error',
        ],
        'success' => 'Success',
        'pending' => 'Pending',
        'error' => 'Error'
    ],
    'throttle' => 'Too many attempts!',
    'permissions' => 'User does not have right permission to do this action.',
    'auth' => [
        'register' => [
            'invalidCredential' => 'Given data was invalid.',
            'alreadyExists' => 'Your credential has been chosen by someone else!',
            'successful' => 'Your registration has been completed successfully.',
        ],
        'login' => [
            'successful' => 'You are successfully logged in your account.',
            'failed' => 'These credentials do not match out records.',
            'notAuthenticated' => 'You are not authenticated!',
            'alreadyLoggedIn' => 'Already Logged In  Into Your Account'
        ],
        'forgot' => [
            'successful' => 'Password reset has been successfully done and new password has been sent to :destination',
            'newPassword' => [
                'subject' => 'Your new password',
                'body' => 'Your new password is :password'
            ],
        ],
        'logout' => [
            'successful' => 'You are successfully logged out of your account.',
        ],
        'otp' => [
            'wait' => 'Please wait :seconds seconds and try again!',
            'enterToken' => 'Please enter the OTP we send to :destination',
            'EmailVerification' => 'Email Verification',
            'YourVerificationCode' => 'Your verification code is :token',
            'wrongToken' => 'Entered token is incorrect!',
            'successful' => 'Your verification has been completed successfully!',
            'resend' => [
                'successful' => 'Resend OTP has been submitted successfully.',
            ],
            'failed' => 'Sending token was unsuccessful',
        ],
        '2fa' => [
            'enter2fa' => 'Please enter your second password.',
            'alreadyEnabled' => 'Your two factor authentication is already enabled.',
            'alreadyDisabled' => 'Your two factor authentication is already disabled.',
            'alreadyVerified' => 'Your two factor authentication is already verified.',
            'notEnoughData' => 'Your profile has not enough data!',
            'notEnabled' => 'Your two factor authentication is not enabled.',
            'enable' => [
                'successful' => 'Your two factor authentication has been enabled successfully.'
            ],
            'disable' => [
                'successful' => 'Your two factor authentication has been disabled successfully.'
            ],
        ],
        'invalidEmailMobile' => 'Invalid email or phone.',
        'invalidEmail' => 'Invalid Email.',
        'needAgree' => 'You need to agree',
        'weekPassword' => 'Weak password',
        'passwordRequired' => 'Password is required',
        'passwordMustMatch' => 'Passwords must match',

    ],
    'profile' => [
        'password' => [
            'successful' => 'Your password has been changed successfully.',
            'currentPasswordIsWrong' => 'Current password is wrong!',
            'samePasswordError' => 'New Password must not be the same as current password!',
        ],
        'update' => [
            'successful' => 'Your profile has been updated successfully.',
        ],
    ],
    'referrals' => [
        'submitReferrer' => [
            'alreadySubmitted' => 'You already submitted your referrer!',
            'successful' => 'Referrer has been submitted successfully.',
            'wrongReferralToken' => 'Entered referral token was wrong.',
        ],
    ],
    'tickets' => [
        'store' => [
            'successful' => 'New ticket has been submitted successfully.',
        ],
        'close' => [
            'successful' => 'Ticket has been closed successfully.',
        ],
        'answer' => [
            'successful' => 'Your answer has been submitted successfully.',
        ],
    ],
    'WalletAddresses' => [
        'isAllocated' => 'Wallet is allocated to a user and cannot be destroyed or edited.',
        'store' => [
            'successful' => 'New wallet address has been stored successfully.'
        ],
        'update' => [
            'successful' => 'Wallet address has been updated successfully.'
        ],
        'destroy' => [
            'successful' => 'Wallet address has been deleted successfully.'
        ],
    ],
    'wallets' => [
        'deposit' => [
            'notAvailable' => 'Deposits cant be processed now. Please try again later.',
            'successful' => 'Deposit has been submitted successfully.',
            'select-blockchain' => 'Select your desired blockchain.',
            'update' => [
                'successful' => 'Deposits has been updated successfully.',
                'failed' => 'Updating deposit had been faced a problem.',
            ],
            'online-payment' => [
                'notFound' => 'Deposit could not be found.',
                'failed' => 'Your online deposit has had faced problem.',
                'successful' => 'Your online deposit had been submitted successfully.',
            ],
        ],
        'withdraw' => [
            'notFound' => 'Withdrawal request not found!',
            'notValidCurrency' => 'Selected assets is not valid.',
            'notValidBankAccount' => 'Destination bank account could not found.',
            'successful' => 'Your withdrawal request has been submitted successfully.',
            'insufficientBalance' => 'Your balance is insufficient!',
            'cancelWithdraw' => [
                'successful' => 'Withdrawal request has been canceled successfully.',
            ],
            'update' => [
                'successful' => 'Withdrawal request has been updated successfully.',
                'failed' => 'Withdrawal request could not get updated.',
            ],
        ],
        'reset-virtual-assets' => [
            'successful' => 'Resetting virtual assets had been done successfully.',
        ],
    ],
    'exchange' => [
        'orders' => [
            'store' => [
                'successful' => 'New order has been submitted.',
                'invalidMarket' => 'Market is invalid.',
                'insufficientBalance' => 'Your balance is insufficient!',
                'failed' => 'Whoops!something went wrong during submitting order.',
            ],
            'cancel' => [
                'received' => 'Cancel order request has been received.',
                'successful' => 'Order has been canceled successfully.',
                'failed' => 'There was a problem during canceling your order.',
                'uncancelable' => 'Order is not cancelable',
            ],
        ],
        'price-tickers' => [
            'failed' => 'Failed to get price tickers.',
        ],
    ],
    'notifications' => [
        'store' => [
            'successful' => 'New notification has been submitted successfully.'
        ],
        'destroy' => [
            'successful' => 'Notification has been destroyed successfully.',
        ],
        'presets' => [
            'user-document-update' => [
                'title' => 'Uploaded document had been updated!',
            ],
            'withdraw-update' => [
                'title' => 'Your withdrawal request has been updated!'
            ],
            'deposit-store' => [
                'title' => 'Your deposit has been processed!'
            ],
            'order-update' => [
                'title' => 'Order :order has been updated.',
            ],
        ],
    ],
    'bankAccounts' => [
        'store' => [
            'successful' => 'New account has been created successfully.',
        ],
        'update' => [
            'successful' => 'Account has been updated successfully.',
        ],
        'destroy' => [
            'successful' => 'Account has been successfully destroyed.',
        ],
        'notFound' => 'No accounts available. Please create one.',
    ],
    'roles' => [
        'store' => [
            'successful' => 'New role has been created successfully.',
        ],
        'update' => [
            'successful' => 'Role has been updated successfully.',
        ],
        'destroy' => [
            'successful' => 'Role has been destroyed successfully.',
        ],
    ],
    'users' => [
        'update' => [
            'successful' => 'User has been updated successfully.',
        ],
        'store' => [
            'successful' => 'New user has been created.',
        ],
    ],
    'documents' => [
        'notFound' => 'Document not found.',
        'store' => [
            'successful' => 'New document has been stored successfully.',
        ],
        'update' => [
            'successful' => 'Document has been updated successfully.',
        ],
        'destroy' => [
            'successful' => 'Document has been destroyed successfully.',
            'failed' => 'Document has relations and could not get deleted.'
        ],
    ],
    'admins' => [
        'store' => [
            'successful' => 'New admin has been stored successfully.',
        ],
        'update' => [
            'successful' => 'Admin has been updated successfully.',
        ],
    ],
    'settings' => [
        'update' => [
            'successful' => 'Settings has been updated successfully.',
        ],
    ],
    'otc' => [
        'isNotEnabled' => 'OTC is not enabled.',
        'rules' => [
            'store' => [
                'successful' => 'New OTC rule had been stored successfully.',
                'alreadyExists' => 'OTC rule is already exists for requested market.',
            ],
            'update' => [
                'successful' => 'OTC rule had been updated successfully.',
            ],
            'destroy' => [
                'successful' => 'OTC rule had been destroyed successfully.'
            ],
        ],
        'orders' => [
            'store' => [
                'successful' => 'Order had been submitted successfully.',
                'insufficientSystemBalance' => 'Sorry! our assets could not supply your order.',
                'insufficientUserBalance' => 'Your balance is insufficient to make order.',
            ],
        ],
    ],
    'groups' => [
        'store' => [
            'successful' => 'New group has been created.',
        ],
        'update' => [
            'successful' => 'Group has been updated.',
        ],
        'destroy' => [
            'successful' => 'Group has been destroyed.'
        ]
    ],
    'privacy-policies' => [
        'store' => [
            'successful' => 'New Privacy Policy has been created.',
        ],
        'update' => [
            'successful' => 'Privacy Policy has been updated.',
        ],
        'destroy' => [
            'successful' => 'Privacy Policy has been destroyed.'
        ]
    ],
    'blockchains' => [
        'store' => [
            'successful' => 'New Blockchain has been created.',
        ],
        'update' => [
            'successful' => 'Blockchain has been updated.',
        ],
        'destroy' => [
            'successful' => 'Blockchain has been destroyed.'
        ]
    ],
    'symbols' => [
        'store' => [
            'successful' => 'Nee symbol had been stored successfully.',
            'alreadyExists' => 'Selected symbol is already exists.',
        ],
        'update' => [
            'successful' => 'Symbol had been updated successfully.',
        ],
        'destroy' => [
            'successful' => 'Symbol had been destroyed successfully.',
            'failed' => 'Symbol is in use and could not get destroyed.'
        ]
    ],
    'markets' => [
        'notFound' => 'Market not found.',
        'store' => [
            'successful' => 'New market had been stored successfully.',
            'symbolNotValid' => 'Selected symbol is not valid.',
            'alreadyExists' => 'Selected Market is already exists.',
        ],
        'update' => [
            'successful' => 'Market had been updated successfully.',
        ],
        'destroy' => [
            'successful' => 'Market had been destroyed successfully.',
            'failed' => 'Market is in use and could not get deleted.'
        ]
    ],
    'authentication' => [
        'error' => [
            'nationalCode' => 'The authentication operation requires the registration of the national code from the identity information department.'
        ],
        'perform' => 'Please complete the authentication process from the user account section.'
    ],
    'normalFiatTransfer' => 'You are transferring :amount to :card number, do you confirm?',
    'payaFiatTransfer' => 'You are transferring :amount to Sheba:ibanNumber, do you confirm?',
    'increase-credit' => 'Increase Credit',

    //
    'not-fount' => 'Not Found.',
    'exceptions' => [
        'primary' => [
            \App\Exceptions\Primary\NotFoundException::MARKET_NOT_FOUND => 'Market not found!',
            \App\Exceptions\Primary\NotFoundException::ORDER_NOT_FOUND => 'Order not found!',
            \App\Exceptions\Primary\NotFoundException::USER_NOT_FOUND => 'User not found!',
            \App\Exceptions\Primary\NotFoundException::WALLET_ADDRESS_NOT_FOUND => 'Wallet address not found!',
            \App\Exceptions\Primary\NotFoundException::BLOCKCHAIN_NOT_FOUND => 'Blockchain not found!',
            \App\Exceptions\Primary\NotFoundException::DEPOSIT_NOT_FOUND => 'Deposit not found!',
        ],
    ],
];
