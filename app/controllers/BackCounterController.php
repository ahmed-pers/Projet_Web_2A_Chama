<?php

class BackCounterController extends Controller
{
    private PDO $db;
    private Counter $counters;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->counters = new Counter($this->db);
    }

    public function index(): void
    {
        $this->render('back/counters', ['counters' => $this->counters->all()], 'Back office - Counters');
    }

    private function readForm(): array
    {
        return [
            'number'   => trim((string) $this->post('number')),
            'location' => trim((string) $this->post('location')),
            'status'   => $this->post('status'),
        ];
    }

    private function validate(array $data, ?int $id): Validator
    {
        $validator = new Validator();

        $validator->required('number', $data['number'])
            ->length('number', $data['number'], 2, 20)
            ->unique('number', $data['number'], function ($number) use ($id) {
                return $this->counters->numberExists($number, $id);
            })
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
                $this->counters->create($data);
                $this->flash('Counter created.');
                $this->redirect('back.counters');
            }

            $errors = $validator->errors();
        }

        $this->render('back/counter-form', [
            'counter' => $data,
            'errors'  => $errors,
            'action'  => 'back.counter.create',
        ], 'New counter');
    }

    public function edit(): void
    {
        $id = (int) $this->get('id');
        $counter = $this->counters->find($id);

        if ($counter === null) {
            $this->flash('Counter not found.');
            $this->redirect('back.counters');
        }

        $errors = [];
        $data = $counter;

        if ($this->isPost()) {
            $data = $this->readForm();
            $validator = $this->validate($data, $id);

            if ($validator->isValid()) {
                $this->counters->update($id, $data);
                $this->flash('Counter updated.');
                $this->redirect('back.counters');
            }

            $errors = $validator->errors();
        }

        $this->render('back/counter-form', [
            'counter' => $data,
            'errors'  => $errors,
            'action'  => 'back.counter.edit&id=' . $id,
        ], 'Edit counter');
    }

    public function delete(): void
    {
        if ($this->isPost()) {
            $id = (int) $this->post('id');

            if ($this->isOnlyCounterForOpenService($id)) {
                $this->flash('This counter is the only one serving an open service. Assign another counter first.');
            } else {
                $this->counters->delete($id);
                $this->flash('Counter deleted.');
            }
        }

        $this->redirect('back.counters');
    }

    private function isOnlyCounterForOpenService(int $counterId): bool
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*) FROM services s
             INNER JOIN service_counter sc ON sc.service_id = s.id
             WHERE sc.counter_id = :id AND s.status = "open"
             AND (SELECT COUNT(*) FROM service_counter sc2 WHERE sc2.service_id = s.id) = 1'
        );
        $statement->execute(['id' => $counterId]);

        return (int) $statement->fetchColumn() > 0;
    }
}
