<?php

/****************************************************************************************
 * This project is not free and has business trademarks which belongs to Vorna Company. *
 *                                                                                      *
 * Team-lead of software engineers contact information:                                 *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                                       *
 * Copyright (c)  2020-2022, Vorna Co.                                                  *
 ****************************************************************************************/

namespace Tests\Feature\User;

use App\Models\Ticket;
use Tests\UserTestCase;

class TicketTest extends UserTestCase
{
    public function test_can_create_ticket()
    {
        $response = $this->post(route('user.tickets.store'), $this->fakeData());

        $response->assertSuccessful()->assertJsonMissingValidationErrors();
    }

    private function fakeData()
    {
        return [
            'subject' => $this->faker->title(),
            'department' => $this->faker->sentence(),
            'content' => $this->faker->text(300),
        ];
    }

    public function test_can_see_ticket()
    {
        $response = $this->get(route('user.tickets.show', $ticket = $this->fakeTicket()));

        $response->assertSuccessful()
            ->assertSee([
                $ticket->subject,
                $ticket->department,
                $ticket->content,
            ]);
    }

    private function fakeTicket()
    {
        return Ticket::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    public function test_can_delete_ticket()
    {
        $response = $this->delete(route('user.tickets.delete', $this->fakeTicket()));

        $response->assertJsonMissingValidationErrors()->assertSuccessful();
    }

    public function test_can_answer_ticket()
    {
        $response = $this->post(route('user.tickets.answer', $this->fakeTicket()), $this->fakeData());

        $response->assertSuccessful();
    }
}
