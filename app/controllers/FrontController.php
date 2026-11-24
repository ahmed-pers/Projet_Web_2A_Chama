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
                $ticket['id_Ticket'] = $this->tickets->nextId();
                $this->tickets->create($ticket);
                $this->flash('Ticket reserved.');
                $this->redirect('front.tickets');
            }

            $errors = $validator->errors();
        }

        $this->render('front/tickets', [
            'tickets'  => $this->tickets->allWithDetails(),
            'services' => $this->services->all(),
            'ticket'   => $ticket,
            'errors'   => $errors,
        ], 'Reserve a ticket');
    }

    private function readForm(): array
    {
        return [
            'id_client'  => $this->post('id_client'),
            'id_service' => $this->post('id_service'),
            'date'       => date('Y-m-d'),
            'time'       => date('H:i'),
        ];
    }

    private function validate(array $data): Validator
    {
        $validator = new Validator();

        $validator->required('id_client', $data['id_client'])
            ->intRange('id_client', $data['id_client'], 1, PHP_INT_MAX)
            ->required('id_service', $data['id_service'])
            ->intRange('id_service', $data['id_service'], 1, PHP_INT_MAX);

        if (!$validator->isValid()) {
            return $validator;
        }

        $service = $this->services->find((int) $data['id_service']);

        if ($service === null) {
            $validator->fail('id_service', 'This service does not exist.');
        } elseif ($service['status'] !== 'open') {
            $validator->fail('id_service', 'This service is closed.');
        }

        if ($validator->isValid() && $this->tickets->existsOnDate(
            (int) $data['id_client'],
            (int) $data['id_service'],
            $data['date']
        )) {
            $validator->fail('id_service', 'You already have a ticket for this service today.');
        }

        return $validator;
    }
}
