<?php

/****************************************************************************************
 * This project is not free and has business trademarks which belongs to Vorna Company. *
 *                                                                                      *
 * Team-lead of software engineers contact information:                                 *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                                       *
 * Copyright (c)  2020-2022, Vorna Co.                                                  *
 ****************************************************************************************/

namespace Tests\Feature\Admin;

use Tests\AdminTestCase;

class StatisticsTest extends AdminTestCase
{
    public function tests_can_see_statistics()
    {
        $response = $this->get(route('admin.statistics.index'));

        $response->assertSuccessful()
            ->assertJsonStructure([
                'statistics'
            ]);
    }
}
