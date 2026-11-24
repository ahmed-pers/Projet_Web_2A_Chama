<?php

class FrontController extends Controller
{
    private Categorie $categories;
    private Service $services;
    private Ticket $tickets;

    public function __construct()
    {
        $db = Database::getConnection();
        $this->categories = new Categorie($db);
        $this->services = new Service($db);
        $this->tickets = new Ticket($db);
    }

    public function dashboard(): void
    {
        $this->render('front/dashboard', [
            'categories'  => $this->categories->all(),
            'services'    => $this->services->allWithCategories(),
            'ticketCount' => count($this->tickets->all()),
        ], 'Front Office - Smart Queue');
    }

    public function services(): void
    {
        $this->render('front/services', [
            'services' => $this->services->allWithCategories(),
        ], 'Our services');
    }

    public function tickets(): void
    {
        $errors = [];
        $ticket = $this->readForm();

        if ($this->isPost()) {
            $validator = $this->validate($ticket);

            if ($validator->isValid()) {
                $this->tickets->create($ticket);
                $this->flash('Ticket created.');
                $this->redirect('front.tickets');
            }

            $errors = $validator->errors();
        }

        $this->render('front/tickets', [
            'tickets'  => $this->tickets->allWithDetails(),
            'services' => $this->services->all(),
            'ticket'   => $ticket,
            'errors'   => $errors,
        ], 'My tickets');
    }

    private function readForm(): array
    {
        return [
            'id_Ticket' => $this->post('id_Ticket'),
            'id_client' => $this->post('id_client'),
            'id_service' => $this->post('id_service'),
            'date'      => $this->post('date'),
            'time'      => $this->post('time'),
        ];
    }

    private function validate(array $data): Validator
    {
        $validator = new Validator();

        $validator->required('id_Ticket', $data['id_Ticket'])
            ->intRange('id_Ticket', $data['id_Ticket'], 1, PHP_INT_MAX)
            ->required('id_client', $data['id_client'])
            ->intRange('id_client', $data['id_client'], 1, PHP_INT_MAX)
            ->required('id_service', $data['id_service'])
            ->intRange('id_service', $data['id_service'], 1, PHP_INT_MAX)
            ->required('date', $data['date'])
            ->matches('date', $data['date'], '/^\d{4}-\d{2}-\d{2}$/', 'Date must be YYYY-MM-DD.')
            ->required('time', $data['time'])
            ->matches('time', $data['time'], '/^\d{2}:\d{2}$/', 'Time must be HH:MM.');

        if (!$validator->isValid()) {
            return $validator;
        }

        $service = $this->services->find((int) $data['id_service']);

        if ($service === null) {
            $validator->fail('id_service', 'This service does not exist.');
        } elseif ($service['status'] !== 'open') {
            $validator->fail('id_service', 'This service is closed.');
        }

        if ($this->tickets->exists((int) $data['id_Ticket'])) {
            $validator->fail('id_Ticket', 'This ticket number already exists.');
        }

        if ($validator->isValid() && $this->tickets->existsOnDate(
            (int) $data['id_client'],
            (int) $data['id_service'],
            $data['date']
        )) {
            $validator->fail('id_service', 'This client already has a ticket for this service on this date.');
        }

        return $validator;
    }
}
