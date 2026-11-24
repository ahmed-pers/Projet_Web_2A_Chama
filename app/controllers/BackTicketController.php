<?php

class BackTicketController extends Controller
{
    private Ticket $tickets;

    public function __construct()
    {
        $this->tickets = new Ticket(Database::getConnection());
    }

    public function index(): void
    {
        $this->render('back/tickets', [
            'tickets' => $this->tickets->all(),
        ], 'Tickets');
    }
}
