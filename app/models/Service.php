<?php

class Service extends Model
{
    protected string $table = 'SERVICE';
    protected string $primaryKey = 'id_service';

    public function create(array $data): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO SERVICE (id_service, nomService, status, id_categorie)
             VALUES (:id, :name, :status, :categorie)'
        );
        $statement->execute([
            'id'        => $data['id_service'],
            'name'      => $data['nomService'],
            'status'    => $data['status'],
            'categorie' => $data['id_categorie'],
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $statement = $this->db->prepare(
            'UPDATE SERVICE
             SET nomService = :name, status = :status, id_categorie = :categorie
             WHERE id_service = :id'
        );

        return $statement->execute([
            'id'        => $id,
            'name'      => $data['nomService'],
            'status'    => $data['status'],
            'categorie' => $data['id_categorie'],
        ]);
    }

    public function nameExists(string $name, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM SERVICE WHERE nomService = :name';
        $parameters = ['name' => $name];

        if ($exceptId !== null) {
            $sql .= ' AND id_service <> :id';
            $parameters['id'] = $exceptId;
        }

        $statement = $this->db->prepare($sql);
        $statement->execute($parameters);

        return (int) $statement->fetchColumn() > 0;
    }

    public function hasTickets(int $id): bool
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*) FROM TICKET WHERE id_service = :id'
        );
        $statement->execute(['id' => $id]);

        return (int) $statement->fetchColumn() > 0;
    }
}
