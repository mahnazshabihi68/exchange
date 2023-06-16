<?php

/****************************************************************************************
 * This project is not free and has business trademarks which belongs to Vorna Company. *
 *                                                                                      *
 * Team-lead of software engineers contact information:                                 *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                                       *
 * Copyright (c)  2020-2022, Vorna Co.                                                  *
 ****************************************************************************************/

namespace Tests\Feature\Admin;

use App\Models\Ticket;
use App\Models\User;
use Tests\AdminTestCase;

class TicketTest extends AdminTestCase
{
    public function test_can_see_tickets()
    {
        $response = $this->get(route('admin.tickets.index'));

        $response->assertSuccessful()
            ->assertJsonStructure([
                'tickets'
            ]);
    }

    public function test_can_answer_user_ticket()
    {
        $response = $this->post(route('admin.tickets.answer', $this->fakeTicket()), [
            'content' => 'A message from admin.',
        ]);

        $response->assertSuccessful();
    }

    private function fakeTicket()
    {
        return Ticket::factory()->create([
            'user_id' => User::factory()->create()->id,
        ]);
    }

    public function test_can_see_user_ticket()
    {
        $response = $this->get(route('admin.tickets.show', $ticket = $this->fakeTicket()));

        $response->assertSuccessful()
            ->assertJsonStructure([
                'ticket',
                'answers',
            ])
            ->assertSee([
                $ticket->subject,
                $ticket->department,
                $ticket->content,
            ]);
    }

    public function test_can_close_user_ticket()
    {
        $response = $this->delete(route('admin.tickets.delete', $ticket = $this->fakeTicket()));

        $response->assertSuccessful();

        $ticket->fresh();

        $this->assertTrue($ticket->isClosed());
    }
}
