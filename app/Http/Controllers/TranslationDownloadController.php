<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TranslationDownloadController extends Controller
{
    /**
     * @return BinaryFileResponse
     */

    public function download(): BinaryFileResponse
    {
        Artisan::call('lang:js translations.js --no-lib --quiet -c');

        return response()->download('translations.js');
    }
}
