<?php

class Service extends Model
{
    protected $table = 'SERVICE';
    protected $primaryKey = 'id_service';

    public function create($data)
    {
        $sql = 'INSERT INTO SERVICE (id_service, nomService, status, id_categorie)
                VALUES (:id, :name, :status, :categorie)';
        $statement = $this->db->prepare($sql);
        $statement->execute([
            'id'        => $data['id_service'],
            'name'      => $data['nomService'],
            'status'    => $data['status'],
            'categorie' => $data['id_categorie']
        ]);
    }

    public function update($id, $data)
    {
        $sql = 'UPDATE SERVICE
                SET nomService = :name, status = :status, id_categorie = :categorie
                WHERE id_service = :id';
        $statement = $this->db->prepare($sql);
        $statement->execute([
            'id'        => $id,
            'name'      => $data['nomService'],
            'status'    => $data['status'],
            'categorie' => $data['id_categorie']
        ]);
    }

    // $exceptId = 0 when we add a new service.
    public function nameExists($name, $exceptId)
    {
        $sql = 'SELECT COUNT(*) FROM SERVICE
                WHERE nomService = :name AND id_service <> :id';
        $statement = $this->db->prepare($sql);
        $statement->execute([
            'name' => $name,
            'id'   => $exceptId
        ]);

        return $statement->fetchColumn() > 0;
    }

    public function hasTickets($id)
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*) FROM TICKET WHERE id_service = :id'
        );
        $statement->execute(['id' => $id]);

        return $statement->fetchColumn() > 0;
    }
}
