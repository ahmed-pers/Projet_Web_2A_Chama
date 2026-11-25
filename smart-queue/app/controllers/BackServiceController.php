<?php

class BackServiceController extends Controller
{
    private $categories;
    private $services;

    public function __construct()
    {
        $db = Database::getConnection();
        $this->categories = new Categorie($db);
        $this->services = new Service($db);
    }

    public function index()
    {
        $error = '';
        if (isset($_GET['error']) && $_GET['error'] === '1') {
            $error = 'This service still has tickets.';
        }

        $this->render('back/services', [
            'services' => $this->services->all(),
            'error'    => $error
        ], 'Services');
    }

    public function create()
    {
        $errors = [];
        $name = '';
        $status = 'open';
        $categoryId = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = isset($_POST['nomService']) ? trim($_POST['nomService']) : '';
            $status = isset($_POST['status']) ? $_POST['status'] : 'open';
            $categoryId = isset($_POST['id_categorie']) ? trim($_POST['id_categorie']) : '';

            if ($name === '') {
                $errors[] = 'Name is required.';
            } elseif (strlen($name) < 3 || strlen($name) > 100) {
                $errors[] = 'Name must be between 3 and 100 characters.';
            } elseif ($this->services->nameExists($name, 0)) {
                $errors[] = 'Name already exists.';
            }

            if ($status !== 'open' && $status !== 'closed') {
                $errors[] = 'Status is not valid.';
            }

            if ($categoryId === '') {
                $errors[] = 'Category is required.';
            } elseif (!filter_var($categoryId, FILTER_VALIDATE_INT) || (int) $categoryId < 1) {
                $errors[] = 'Category must be a number.';
            }

            if (count($errors) === 0) {
                $this->services->create([
                    'id_service'   => $this->services->nextId(),
                    'nomService'   => $name,
                    'status'       => $status,
                    'id_categorie' => (int) $categoryId
                ]);

                $this->redirect('back.services');
            }
        }

        $service = [
            'nomService'   => $name,
            'status'       => $status,
            'id_categorie' => $categoryId
        ];

        $this->render('back/service-form', [
            'service'    => $service,
            'categories' => $this->categories->all(),
            'errors'     => $errors,
            'action'     => 'back.service.create'
        ], 'Add a service');
    }

    public function edit()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $serviceFromDb = $this->services->find($id);

        if (!$serviceFromDb) {
            $this->redirect('back.services');
        }

        $errors = [];
        $name = $serviceFromDb['nomService'];
        $status = $serviceFromDb['status'];
        $categoryId = $serviceFromDb['id_categorie'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = isset($_POST['nomService']) ? trim($_POST['nomService']) : '';
            $status = isset($_POST['status']) ? $_POST['status'] : 'open';
            $categoryId = isset($_POST['id_categorie']) ? trim($_POST['id_categorie']) : '';

            if ($name === '') {
                $errors[] = 'Name is required.';
            } elseif (strlen($name) < 3 || strlen($name) > 100) {
                $errors[] = 'Name must be between 3 and 100 characters.';
            } elseif ($this->services->nameExists($name, $id)) {
                $errors[] = 'Name already exists.';
            }

            if ($status !== 'open' && $status !== 'closed') {
                $errors[] = 'Status is not valid.';
            }

            if ($categoryId === '') {
                $errors[] = 'Category is required.';
            } elseif (!filter_var($categoryId, FILTER_VALIDATE_INT) || (int) $categoryId < 1) {
                $errors[] = 'Category must be a number.';
            }

            if (count($errors) === 0) {
                $this->services->update($id, [
                    'nomService'   => $name,
                    'status'       => $status,
                    'id_categorie' => (int) $categoryId
                ]);

                $this->redirect('back.services');
            }
        }

        $service = [
            'nomService'   => $name,
            'status'       => $status,
            'id_categorie' => $categoryId
        ];

        $this->render('back/service-form', [
            'service'    => $service,
            'categories' => $this->categories->all(),
            'errors'     => $errors,
            'action'     => 'back.service.edit&id=' . $id
        ], 'Edit service');
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

            if ($this->services->hasTickets($id)) {
                $this->redirect('back.services&error=1');
            }

            $this->services->delete($id);
        }

        $this->redirect('back.services');
    }
}
