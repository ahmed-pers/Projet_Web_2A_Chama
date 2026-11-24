<?php

class BackServiceController extends Controller
{
    private PDO $db;
    private Service $services;
    private Counter $counters;
    private ServiceCounter $links;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->services = new Service($this->db);
        $this->counters = new Counter($this->db);
        $this->links = new ServiceCounter($this->db);
    }

    public function index(): void
    {
        $services = [];

        foreach ($this->services->all() as $service) {
            $service['counter_count'] = count($this->links->countersFor((int) $service['id']));
            $services[] = $service;
        }

        $this->render('back/services', ['services' => $services], 'Back office - Services');
    }

    private function readForm(): array
    {
        return [
            'name'         => trim((string) $this->post('name')),
            'description'  => trim((string) $this->post('description')),
            'average_time' => $this->post('average_time'),
            'status'       => $this->post('status'),
        ];
    }

    private function validate(array $data, ?int $id): Validator
    {
        $validator = new Validator();

        $validator->required('name', $data['name'])
            ->length('name', $data['name'], 3, 50)
            ->unique('name', $data['name'], function ($name) use ($id) {
                return $this->services->nameExists($name, $id);
            })
            ->intRange('average_time', $data['average_time'], 1, 120)
            ->in('status', $data['status'], ['open', 'closed']);

        return $validator;
    }

    public function create(): void
    {
        $errors = [];
        $data = $this->readForm();

        if ($this->isPost()) {
            $validator = $this->validate($data, null);

            if ($validator->isValid()) {
                $this->services->create($data);
                $this->flash('Service created.');
                $this->redirect('back.services');
            }

            $errors = $validator->errors();
        }

        $this->render('back/service-form', [
            'service' => $data,
            'errors'  => $errors,
            'action'  => 'back.service.create',
        ], 'New service');
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
            'service' => $data,
            'errors'  => $errors,
            'action'  => 'back.service.edit&id=' . $id,
        ], 'Edit service');
    }

    public function delete(): void
    {
        if ($this->isPost()) {
            $id = (int) $this->post('id');
            $this->services->delete($id);
            $this->flash('Service deleted.');
        }

        $this->redirect('back.services');
    }

    public function assign(): void
    {
        $id = (int) $this->get('id');
        $service = $this->services->find($id);

        if ($service === null) {
            $this->flash('Service not found.');
            $this->redirect('back.services');
        }

        $error = '';
        $selected = array_map('intval', array_column($this->links->countersFor($id), 'id'));

        if ($this->isPost()) {
            $selected = array_map('intval', (array) $this->post('counter_ids', []));

            foreach ($selected as $counterId) {
                $counter = $this->counters->find($counterId);

                if ($counter === null || $counter['status'] !== 'open') {
                    $error = 'A closed counter cannot be assigned to a service.';
                    break;
                }

                $alreadyLinked = in_array($counterId, array_map('intval', array_column($this->links->countersFor($id), 'id')), true);
                $total = $this->links->serviceCountForCounter($counterId) - ($alreadyLinked ? 1 : 0);

                if ($total >= MAX_SERVICES_PER_COUNTER) {
                    $error = 'A counter can serve at most ' . MAX_SERVICES_PER_COUNTER . ' services.';
                    break;
                }
            }

            if ($error === '') {
                $this->links->replaceForService($id, $selected);
                $this->flash('Assignments saved.');
                $this->redirect('back.services');
            }
        }

        $this->render('back/assign', [
            'service'  => $service,
            'counters' => $this->counters->all(),
            'selected' => $selected,
            'error'    => $error,
        ], 'Assign counters');
    }
}
