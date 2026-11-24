<?php

class Categorie extends Model
{
    protected string $table = 'CATEGORIE';
    protected string $primaryKey = 'id_categorie';

    public function create(array $data): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO CATEGORIE (id_categorie, nomCategorie, description)
             VALUES (:id, :name, :description)'
        );
        $statement->execute([
            'id'          => $data['id_categorie'],
            'name'        => $data['nomCategorie'],
            'description' => $data['description'],
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $statement = $this->db->prepare(
            'UPDATE CATEGORIE
             SET nomCategorie = :name, description = :description
             WHERE id_categorie = :id'
        );

        return $statement->execute([
            'id'          => $id,
            'name'        => $data['nomCategorie'],
            'description' => $data['description'],
        ]);
    }

    public function nameExists(string $name, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM CATEGORIE WHERE nomCategorie = :name';
        $parameters = ['name' => $name];

        if ($exceptId !== null) {
            $sql .= ' AND id_categorie <> :id';
            $parameters['id'] = $exceptId;
        }

        $statement = $this->db->prepare($sql);
        $statement->execute($parameters);

        return (int) $statement->fetchColumn() > 0;
    }

    public function serviceCount(int $id): int
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*) FROM SERVICE WHERE id_categorie = :id'
        );
        $statement->execute(['id' => $id]);

        return (int) $statement->fetchColumn();
    }

    public function hasServices(int $id): bool
    {
        return $this->serviceCount($id) > 0;
    }
}
