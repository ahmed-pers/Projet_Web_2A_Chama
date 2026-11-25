<?php

class FrontController extends Controller
{
    private $services;
    private $tickets;

    public function __construct()
    {
        $db = Database::getConnection();
        $this->services = new Service($db);
        $this->tickets = new Ticket($db);
    }

    public function dashboard()
    {
        $this->render('front/dashboard', [
            'services' => $this->services->all(),
            'tickets'  => $this->tickets->all()
        ], 'Smart Queue');
    }

    public function services()
    {
        $this->render('front/services', [
            'services' => $this->services->all()
        ], 'Services');
    }

    public function tickets()
    {
        $errors = [];

        $client = isset($_POST['id_client']) ? trim($_POST['id_client']) : '';
        $serviceId = isset($_POST['id_service']) ? trim($_POST['id_service']) : '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check the client number.
            if ($client === '') {
                $errors[] = 'Client number is required.';
            } elseif (!filter_var($client, FILTER_VALIDATE_INT) || (int) $client < 1) {
                $errors[] = 'Client number must be a positive number.';
            }

            // Check the service id.
            if ($serviceId === '') {
                $errors[] = 'Service is required.';
            } elseif (!filter_var($serviceId, FILTER_VALIDATE_INT) || (int) $serviceId < 1) {
                $errors[] = 'Service must be a number.';
            }

            // No errors: save the ticket and reload the page.
            if (count($errors) === 0) {
                $this->tickets->create([
                    'id_Ticket'  => $this->tickets->nextId(),
                    'id_client'  => (int) $client,
                    'id_service' => (int) $serviceId,
                    'date'       => date('Y-m-d'),
                    'time'       => date('H:i')
                ]);

                $this->redirect('front.tickets');
            }
        }

        $this->render('front/tickets', [
            'tickets'   => $this->tickets->all(),
            'services'  => $this->services->all(),
            'client'    => $client,
            'serviceId' => $serviceId,
            'errors'    => $errors
        ], 'Reserve a ticket');
    }
}
