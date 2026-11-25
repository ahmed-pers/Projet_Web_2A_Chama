<?php

class Categorie extends Model
{
    protected $table = 'CATEGORIE';
    protected $primaryKey = 'id_categorie';

    public function create($data)
    {
        $sql = 'INSERT INTO CATEGORIE (id_categorie, nomCategorie, description)
                VALUES (:id, :name, :description)';
        $statement = $this->db->prepare($sql);
        $statement->execute([
            'id'          => $data['id_categorie'],
            'name'        => $data['nomCategorie'],
            'description' => $data['description']
        ]);
    }

    public function update($id, $data)
    {
        $sql = 'UPDATE CATEGORIE
                SET nomCategorie = :name, description = :description
                WHERE id_categorie = :id';
        $statement = $this->db->prepare($sql);
        $statement->execute([
            'id'          => $id,
            'name'        => $data['nomCategorie'],
            'description' => $data['description']
        ]);
    }

    // $exceptId = 0 when we add a new category.
    // When we edit, we ignore the category we are editing.
    public function nameExists($name, $exceptId)
    {
        $sql = 'SELECT COUNT(*) FROM CATEGORIE
                WHERE nomCategorie = :name AND id_categorie <> :id';
        $statement = $this->db->prepare($sql);
        $statement->execute([
            'name' => $name,
            'id'   => $exceptId
        ]);

        return $statement->fetchColumn() > 0;
    }

    public function hasServices($id)
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*) FROM SERVICE WHERE id_categorie = :id'
        );
        $statement->execute(['id' => $id]);

        return $statement->fetchColumn() > 0;
    }
}
