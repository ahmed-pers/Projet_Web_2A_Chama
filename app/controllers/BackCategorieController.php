<?php

class BackCategorieController extends Controller
{
    private Categorie $categories;

    public function __construct()
    {
        $this->categories = new Categorie(Database::getConnection());
    }

    public function index(): void
    {
        $error = $this->get('error') === '1' ? 'This category still has services.' : '';

        $this->render('back/categories', [
            'categories' => $this->categories->all(),
            'error'      => $error,
        ], 'Categories');
    }

    private function readForm(): array
    {
        return [
            'nomCategorie' => trim((string) $this->post('nomCategorie')),
            'description'  => trim((string) $this->post('description')),
        ];
    }

    private function validate(array $data, ?int $id): Validator
    {
        $validator = new Validator();

        $validator->required('nomCategorie', $data['nomCategorie'])
            ->length('nomCategorie', $data['nomCategorie'], 3, 100)
            ->unique('nomCategorie', $data['nomCategorie'], function ($name) use ($id) {
                return $this->categories->nameExists($name, $id);
            });

        return $validator;
    }

    public function create(): void
    {
        $errors = [];
        $data = $this->readForm();

        if ($this->isPost()) {
            $validator = $this->validate($data, null);

            if ($validator->isValid()) {
                $data['id_categorie'] = $this->categories->nextId();
                $this->categories->create($data);
                $this->redirect('back.categories');
            }

            $errors = $validator->errors();
        }

        $this->render('back/categorie-form', [
            'category' => $data,
            'errors'   => $errors,
            'action'   => 'back.categorie.create',
        ], 'Add a category');
    }

    public function edit(): void
    {
        $id = (int) $this->get('id');
        $category = $this->categories->find($id);

        if ($category === null) {
            $this->redirect('back.categories');
        }

        $errors = [];
        $data = $category;

        if ($this->isPost()) {
            $data = $this->readForm();
            $validator = $this->validate($data, $id);

            if ($validator->isValid()) {
                $this->categories->update($id, $data);
                $this->redirect('back.categories');
            }

            $errors = $validator->errors();
        }

        $this->render('back/categorie-form', [
            'category' => $data,
            'errors'   => $errors,
            'action'   => 'back.categorie.edit&id=' . $id,
        ], 'Edit category');
    }

    public function delete(): void
    {
        if ($this->isPost()) {
            $id = (int) $this->post('id');

            if ($this->categories->hasServices($id)) {
                $this->redirect('back.categories&error=1');
            }

            $this->categories->delete($id);
        }

        $this->redirect('back.categories');
    }
}
