<?php

/****************************************************************************************
 * This project is not free and has business trademarks which belongs to Vorna Company. *
 *                                                                                      *
 * Team-lead of software engineers contact information:                                 *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                                       *
 * Copyright (c)  2020-2022, Vorna Co.                                                  *
 ****************************************************************************************/

namespace Tests\Feature\User;

use Tests\TestCase;

class TranslationTest extends TestCase
{
    public function test_get_translations()
    {
        $response = $this->get(route('translations'));

        $response->assertDownload();

        $this->assertFileExists(public_path('translations.js'));
    }
}
