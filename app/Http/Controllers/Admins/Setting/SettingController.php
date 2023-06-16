<?php


/******************************************************************************
 * This project is not free and has business trademarks.                      *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                             *
 * Copyright (c)  2020-2022, Vorna Co.                                        *
 ******************************************************************************/

namespace App\Http\Controllers\Admins\Setting;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class SettingController extends Controller
{
    /**
     * SettingController constructor.
     */

    public function __construct()
    {
        $this->middleware(['auth:admin'])->except('downloadDatabaseBackup');

        $this->middleware(['permission:maintenance-mode'])->only('maintenanceMode');

        $this->middleware(['permission:cache-clear'])->only('cacheClear');

        $this->middleware(['permission:database-backup'])->only(['createDatabaseBackup', 'databaseBackups']);
    }

    /**
     * @return JsonResponse
     */

    public function index(): JsonResponse
    {
        return response()->json([
            'settings' => DB::table('settings')->whereIn('group', $this->getAdminSettingPermissions())->get()->map(function ($setting) {
                try {
                    $setting->value = unserialize($setting->value);
                } catch (Exception) {
                    null;
                }
                return $setting;
            })
        ]);
    }

    /**
     * @return Collection
     */

    private function getAdminSettingPermissions(): Collection
    {
        return $this->admin()->getPermissionsViaRoles()->filter(function ($permission) {
            return str_contains($permission['name'], 'setting');
        })->map(function ($permission) {
            return collect($permission)->only('name');
        })->flatten();
    }

    /**
     * @return Authenticatable
     */

    private function admin(): Authenticatable
    {
        return auth('admin')->user();
    }

    /**
     * @param Request $request
     * @param string $group
     * @return JsonResponse
     * @throws ValidationException
     */

    public function update(Request $request, string $group): JsonResponse
    {
        /**
         * Validate group.
         */

        if (!in_array($group, ['seo-setting', 'appearance-setting', 'social-setting', 'api-setting', 'financial-setting'])) {

            return response()->json([
                'error' => __('messages.failed'),
            ], 429);

        }

        /**
         * Validate
         */

        if (!$this->groupValidator($group)) {

            return response()->json([
                'error' => __('messages.failed'),
            ], 403);

        }

        /**
         * Create new variable to store data.
         */

        $settingsData = [];

        /**
         * Validate request based on requested group.
         */

        if ($group === 'seo-setting') {

            $this->validate($request, [
                'name_fa' => 'required|string',
                'name_en' => 'required|string',
                'description_fa' => 'required|string',
                'description_en' => 'required|string',
                'keywords_fa' => 'required|string',
                'keywords_en' => 'required|string',
                'additional_js' => 'string'
            ]);

            $settingsData = $request->only(['name_fa', 'name_en', 'description_fa', 'description_en', 'keywords_fa', 'keywords_en', 'additional_js']);

        } elseif ($group === 'appearance-setting') {

            $this->validate($request, [
                'logo' => 'image|max:1024',
                'favicon' => 'image|max:1024|dimensions:min_width=128,min_height=128',
                'auth_background_picture' => 'image|max:1024',
                'theme' => 'required|array',
            ]);

            if ($request->hasFile('logo')) {

                $settingsData['logo'] = $request->file('logo')->store('logos');

            }

            if ($request->hasFile('favicon')) {

                $settingsData['favicon'] = $request->file('favicon')->store('logos');

            }

            if ($request->hasFile('auth_background_picture')) {

                $settingsData['auth_background_picture'] = $request->file('auth_background_picture')->store('backgrounds');

            }

            $settingsData['theme'] = serialize($request->theme);

        } elseif ($group === 'social-setting') {

            $this->validate($request, [
                'instagram' => 'string',
                'telegram' => 'string',
                'twitter' => 'string',
                'whatsapp' => 'string',
                'phone' => 'string',
            ]);

            $settingsData = $request->only(['instagram', 'telegram', 'twitter', 'whatsapp', 'phone']);

        } elseif ($group === 'api-setting') {

            $this->validate($request, [
                'smsir_api_key' => 'required|string',
                'smsir_secret_key' => 'required|string',
                'etherscan_api_key' => 'required|string',
                'google_api_key' => 'required|string',
                'google_secret_key' => 'required|string',
                'github_api_key' => 'required|string',
                'github_secret_key' => 'required|string',
                'binance_api_key' => 'required|string',
                'binance_secret_key' => 'required|string',
                'payping_api_key' => 'required|string',
                'nobitex_username' => 'required|string',
                'nobitex_password' => 'required|string',
                'atipay_api_key' => 'string'
            ]);

            $settingsData = $request->only(['smsir_api_key', 'smsir_secret_key', 'etherscan_api_key', 'google_api_key', 'google_secret_key', 'github_api_key', 'github_secret_key', 'binance_api_key', 'binance_secret_key', 'payping_api_key', 'nobitex_username', 'nobitex_password', 'atipay_api_key']);

        } elseif ($group === 'financial-setting') {

            $this->validate($request, [
                'accepted_order_types' => 'required|array',
                'accepted_order_types.*' => 'required|string|in:' . implode(',', (unserialize(config('settings.all_order_types')))),
                'trade_wage' => 'required|numeric|min:0',
                'referral_reward' => 'required|numeric|min:0',
                'bankAccount_cardNumber' => 'required|numeric|ir_bank_card_number',
                'bankAccount_accountNumber' => 'required|string',
                'bankAccount_shebaNumber' => 'required|string|ir_sheba',
                'bankAccount_bankName' => 'required|string',
                'bankAccount_ownerName' => 'required|string',
                'BTC_address' => 'required|string',
                'ETH_address' => 'required|string',
                'TRX_address' => 'required|string',
                'trade_wage_receiver_user_id' => 'numeric|exists:users,id',
                'virtual_USDT_wallet_default_amount' => 'required|numeric|gte:0',
                'deallocating_wallets_after_hours' => 'required|numeric|gt:0',
                'crypto_deposit_method' => 'required|numeric|in:1,2',
                'irt_deposit_gateway_is_enabled' => 'required|boolean',
                'irt_deposit_gateway' => 'required|string|in:payping,atipay,zarinpal',
                'irt_deposit_min_amount' => 'required|numeric|min:1000',
                'irt_deposit_max_amount' => 'required|numeric|gt:irt_deposit_min_amount'
            ]);

            $settingsData = $request->only(['trade_wage', 'referral_reward', 'bankAccount_cardNumber', 'bankAccount_accountNumber', 'bankAccount_shebaNumber', 'bankAccount_bankName', 'bankAccount_ownerName', 'BTC_address', 'ETH_address', 'TRX_address', 'trade_wage_receiver_user_id', 'virtual_USDT_wallet_default_amount', 'deallocating_wallets_after_hours', 'crypto_deposit_method', 'irt_deposit_gateway_is_enabled', 'irt_deposit_gateway', 'irt_deposit_min_amount', 'irt_deposit_max_amount']);

            $settingsData['accepted_order_types'] = serialize($request->accepted_order_types);

        }

        /**
         * Update values in database.
         */

        if (!empty($settingsData)) {

            foreach ($settingsData as $key => $value) {

                $db = DB::table('settings')->where('key', $key);

                if ($db->exists()) {

                    $db->update([
                        'value' => $value
                    ]);

                }

            }

        }

        /**
         * Clear cache.
         */

        $this->appReOptimize();

        /**
         * Return response.
         */

        return response()->json([
            'message' => __('messages.settings.update.successful'),
        ]);
    }

    /**
     * @param string $group
     * @return bool
     */

    private function groupValidator(string $group): bool
    {
        return $this->getAdminSettingPermissions()->contains($group);
    }

    /**
     * ReOptimizes the whole app.
     */

    private function appReOptimize()
    {
        Artisan::call('optimize:clear');

        Artisan::call('clear-compiled');

        Artisan::call('queue:restart');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */

    public function maintenanceMode(Request $request): JsonResponse
    {
        /**
         * Validate request.
         */

        $this->validate($request, [
            'maintenance_mode' => 'required|boolean'
        ]);

        /**
         * Artisan call.
         */

        Artisan::call($request->maintenance_mode ? 'up' : 'down');

        /**
         * Return response.
         */

        return response()->json([
            'message' => __('messages.settings.update.successful'),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function appearanceRestore(Request $request): JsonResponse
    {
        /**
         * Take effect.
         */

        DB::table('settings')->where('key', '=', 'theme')->update([
            'value' => serialize(json_decode(file_get_contents(public_path('appearances/colors.json')), true))
        ]);

        /**
         * App ReOptimize.
         */

        $this->appReOptimize();

        /**
         * Return response.
         */

        return response()->json([
            'message' => __('messages.appearance-restore.successful')
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function cacheClear(Request $request): JsonResponse
    {
        /**
         * ReOptimize app.
         */

        $this->appReOptimize();

        /**
         * Return response.
         */

        return response()->json([
            'message' => __('messages.cache-clear.successful'),
        ]);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function createDatabaseBackup(Request $request): JsonResponse
    {
        /**
         * Create new database backup.
         */

        Artisan::call('backup:run --only-db');

        /**
         * Return response.
         */

        return response()->json([
            'message' => __('messages.create-database-backup.successful')
        ]);
    }

    /**
     * @return JsonResponse
     */

    public function databaseBackups(): JsonResponse
    {
        /**
         * Handle all database backups.
         */

        $databaseBackups = str_replace('backups/', '', Storage::disk('local')->files('backups'));

        /**
         * Process each database backup.
         */

        $databaseBackups = collect($databaseBackups)->map(function ($backup) {
            return collect([
                'backup-name' => $backup,
                'backup-download-url' => URL::temporarySignedRoute('admin.settings.download-database-backup', now()->addSeconds(10), ['backup' => $backup])
            ]);
        });

        return response()->json([
            'database-backups' => $databaseBackups
        ]);
    }

    /**
     * @param Request $request
     * @param string $backup
     * @return JsonResponse|BinaryFileResponse
     */

    public function downloadDatabaseBackup(Request $request, string $backup)
    {
        /**
         * Check validity of url.
         */

        if (!$request->hasValidSignature()) {

            return response()->json([
                'error' => __('messages.request-expired')
            ], 401);

        }

        /**
         * Try to find the backup file.
         */

        if (!Storage::disk('local')->exists('backups/' . $backup)) {

            return response()->json([
                'error' => __('messages.failed')
            ], 404);

        }

        /**
         * Return response download.
         */

        return response()->download(storage_path('app/backups/' . $backup));
    }
}
