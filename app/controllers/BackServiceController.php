<?php

class BackServiceController extends Controller
{
    private Categorie $categories;
    private Service $services;

    public function __construct()
    {
        $db = Database::getConnection();
        $this->categories = new Categorie($db);
        $this->services = new Service($db);
    }

    public function index(): void
    {
        $services = [];

        foreach ($this->services->allWithCategories() as $service) {
            $service['ticket_count'] = $this->services->ticketCount((int) $service['id_service']);
            $services[] = $service;
        }

        $this->render('back/services', ['services' => $services], 'Back office - Services');
    }

    private function readForm(): array
    {
        return [
            'nomService'   => trim((string) $this->post('nomService')),
            'status'       => $this->post('status'),
            'id_categorie' => $this->post('id_categorie'),
        ];
    }

    private function validate(array $data, ?int $id): Validator
    {
        $validator = new Validator();

        $validator->required('nomService', $data['nomService'])
            ->length('nomService', $data['nomService'], 3, 100)
            ->unique('nomService', $data['nomService'], function ($name) use ($id) {
                return $this->services->nameExists($name, $id);
            })
            ->in('status', $data['status'], STATUSES)
            ->required('id_categorie', $data['id_categorie'])
            ->intRange('id_categorie', $data['id_categorie'], 1, PHP_INT_MAX);

        if ($validator->isValid() && !$this->categories->exists((int) $data['id_categorie'])) {
            $validator->fail('id_categorie', 'This category does not exist.');
        }

        return $validator;
    }

    public function create(): void
    {
        $errors = [];
        $data = $this->readForm();

        if ($this->isPost()) {
            $validator = $this->validate($data, null);

            if ($validator->isValid()) {
                $data['id_service'] = $this->services->nextId();
                $this->services->create($data);
                $this->flash('Service added.');
                $this->redirect('back.services');
            }

            $errors = $validator->errors();
        }

        $this->render('back/service-form', [
            'service'    => $data,
            'categories' => $this->categories->all(),
            'errors'     => $errors,
            'action'     => 'back.service.create',
        ], 'Add a service');
    }

    public function edit(): void
    {
        $id = (int) $this->get('id');
        $service = $this->services->find($id);

        if ($service === null) {
            $this->flash('Service not found.');
            $this->redirect('back.services');
        }

        $errors = [];
        $data = $service;

        if ($this->isPost()) {
            $data = $this->readForm();
            $validator = $this->validate($data, $id);

            if ($validator->isValid()) {
                $this->services->update($id, $data);
                $this->flash('Service updated.');
                $this->redirect('back.services');
            }

            $errors = $validator->errors();
        }

        $this->render('back/service-form', [
            'service'    => $data,
            'categories' => $this->categories->all(),
            'errors'     => $errors,
            'action'     => 'back.service.edit&id=' . $id,
        ], 'Edit service');
    }

    public function delete(): void
    {
        if ($this->isPost()) {
            $id = (int) $this->post('id');

            if ($this->services->hasTickets($id)) {
                $this->flash('This service still has tickets. Delete them first.');
            } else {
                $this->services->delete($id);
                $this->flash('Service deleted.');
            }
        }

        $this->redirect('back.services');
    }
}
