<?php

class BackCategorieController extends Controller
{
    private $categories;

    public function __construct()
    {
        $this->categories = new Categorie(Database::getConnection());
    }

    public function index()
    {
        $error = '';
        if (isset($_GET['error']) && $_GET['error'] === '1') {
            $error = 'This category still has services.';
        }

        $this->render('back/categories', [
            'categories' => $this->categories->all(),
            'error'      => $error
        ], 'Categories');
    }

    public function create()
    {
        $errors = [];
        $name = '';
        $description = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = isset($_POST['nomCategorie']) ? trim($_POST['nomCategorie']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';

            if ($name === '') {
                $errors[] = 'Name is required.';
            } elseif (strlen($name) < 3 || strlen($name) > 100) {
                $errors[] = 'Name must be between 3 and 100 characters.';
            } elseif ($this->categories->nameExists($name, 0)) {
                $errors[] = 'Name already exists.';
            }

            if (count($errors) === 0) {
                $this->categories->create([
                    'id_categorie' => $this->categories->nextId(),
                    'nomCategorie' => $name,
                    'description'  => $description
                ]);

                $this->redirect('back.categories');
            }
        }

        $category = [
            'nomCategorie' => $name,
            'description'  => $description
        ];

        $this->render('back/categorie-form', [
            'category' => $category,
            'errors'   => $errors,
            'action'   => 'back.categorie.create'
        ], 'Add a category');
    }

    public function edit()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $categoryFromDb = $this->categories->find($id);

        if (!$categoryFromDb) {
            $this->redirect('back.categories');
        }

        $errors = [];
        $name = $categoryFromDb['nomCategorie'];
        $description = $categoryFromDb['description'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = isset($_POST['nomCategorie']) ? trim($_POST['nomCategorie']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';

            if ($name === '') {
                $errors[] = 'Name is required.';
            } elseif (strlen($name) < 3 || strlen($name) > 100) {
                $errors[] = 'Name must be between 3 and 100 characters.';
            } elseif ($this->categories->nameExists($name, $id)) {
                $errors[] = 'Name already exists.';
            }

            if (count($errors) === 0) {
                $this->categories->update($id, [
                    'nomCategorie' => $name,
                    'description'  => $description
                ]);

                $this->redirect('back.categories');
            }
        }

        $category = [
            'nomCategorie' => $name,
            'description'  => $description
        ];

        $this->render('back/categorie-form', [
            'category' => $category,
            'errors'   => $errors,
            'action'   => 'back.categorie.edit&id=' . $id
        ], 'Edit category');
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

            if ($this->categories->hasServices($id)) {
                $this->redirect('back.categories&error=1');
            }

            $this->categories->delete($id);
        }

        $this->redirect('back.categories');
    }
}
