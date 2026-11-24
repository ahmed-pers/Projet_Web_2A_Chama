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
        $categories = [];

        foreach ($this->categories->all() as $category) {
            $category['service_count'] = $this->categories->serviceCount((int) $category['id_categorie']);
            $categories[] = $category;
        }

        $this->render('back/categories', ['categories' => $categories], 'Back office - Categories');
    }

    private function readForm(): array
    {
        return [
            'id_categorie' => $this->post('id_categorie'),
            'nomCategorie' => trim((string) $this->post('nomCategorie')),
            'description'  => trim((string) $this->post('description')),
        ];
    }

    private function validate(array $data, ?int $id): Validator
    {
        $validator = new Validator();

        $validator->required('id_categorie', $data['id_categorie'])
            ->intRange('id_categorie', $data['id_categorie'], 1, PHP_INT_MAX)
            ->required('nomCategorie', $data['nomCategorie'])
            ->length('nomCategorie', $data['nomCategorie'], 3, 100)
            ->unique('nomCategorie', $data['nomCategorie'], function ($name) use ($id) {
                return $this->categories->nameExists($name, $id);
            })
            ->length('description', $data['description'], 0, 255);

        return $validator;
    }

    public function create(): void
    {
        $errors = [];
        $data = $this->readForm();

        if ($this->isPost()) {
            $validator = $this->validate($data, null);

            if ($validator->isValid()) {
                $id = (int) $data['id_categorie'];

                if ($this->categories->exists($id)) {
                    $validator->fail('id_categorie', 'This id already exists.');
                } else {
                    $this->categories->create($data);
                    $this->flash('Category created.');
                    $this->redirect('back.categories');
                }
            }

            $errors = $validator->errors();
        }

        $this->render('back/categorie-form', [
            'category' => $data,
            'errors'   => $errors,
            'action'   => 'back.categorie.create',
        ], 'New category');
    }

    public function edit(): void
    {
        $id = (int) $this->get('id');
        $category = $this->categories->find($id);

        if ($category === null) {
            $this->flash('Category not found.');
            $this->redirect('back.categories');
        }

        $errors = [];
        $data = $category;

        if ($this->isPost()) {
            $data = $this->readForm();
            $validator = $this->validate($data, $id);

            if ($validator->isValid()) {
                $this->categories->update($id, $data);
                $this->flash('Category updated.');
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
                $this->flash('This category still has services. Delete or move them first.');
            } else {
                $this->categories->delete($id);
                $this->flash('Category deleted.');
            }
        }

        $this->redirect('back.categories');
    }
}
