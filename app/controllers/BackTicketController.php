<?php

class BackTicketController extends Controller
{
    private $tickets;

    public function __construct()
    {
        $this->tickets = new Ticket(Database::getConnection());
    }

    public function index()
    {
        $this->render('back/tickets', [
            'tickets' => $this->tickets->all()
        ], 'Tickets');
    }
}
