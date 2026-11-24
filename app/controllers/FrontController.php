<?php

class FrontController extends Controller
{
    private Service $services;
    private Ticket $tickets;

    public function __construct()
    {
        $db = Database::getConnection();
        $this->services = new Service($db);
        $this->tickets = new Ticket($db);
    }

    public function dashboard(): void
    {
        $this->render('front/dashboard', [
            'services' => $this->services->all(),
            'tickets'  => $this->tickets->all(),
        ], 'Smart Queue');
    }

    public function services(): void
    {
        $this->render('front/services', [
            'services' => $this->services->all(),
        ], 'Services');
    }

    public function tickets(): void
    {
        $errors = [];
        $client = $this->post('id_client');
        $serviceId = $this->post('id_service');

        if ($this->isPost()) {
            $validator = new Validator();

            $validator->required('id_client', $client)
                ->intRange('id_client', $client, 1, PHP_INT_MAX)
                ->required('id_service', $serviceId)
                ->intRange('id_service', $serviceId, 1, PHP_INT_MAX);

            if ($validator->isValid()) {
                $this->tickets->create([
                    'id_Ticket'  => $this->tickets->nextId(),
                    'id_client'  => (int) $client,
                    'id_service' => (int) $serviceId,
                    'date'       => date('Y-m-d'),
                    'time'       => date('H:i'),
                ]);

                $this->redirect('front.tickets');
            }

            $errors = $validator->errors();
        }

        $this->render('front/tickets', [
            'tickets'   => $this->tickets->all(),
            'services'  => $this->services->all(),
            'client'    => $client,
            'serviceId' => $serviceId,
            'errors'    => $errors,
        ], 'Reserve a ticket');
    }
}
