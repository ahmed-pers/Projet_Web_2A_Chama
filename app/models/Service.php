<?php

class Service extends Model
{
    protected string $table = 'services';

    public function create(array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO services (name, description, average_time, status)
             VALUES (:name, :description, :average_time, :status)'
        );
        $statement->execute([
            'name'         => $data['name'],
            'description'  => $data['description'],
            'average_time' => $data['average_time'],
            'status'       => $data['status'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $statement = $this->db->prepare(
            'UPDATE services
             SET name = :name, description = :description, average_time = :average_time, status = :status
             WHERE id = :id'
        );

        return $statement->execute([
            'id'           => $id,
            'name'         => $data['name'],
            'description'  => $data['description'],
            'average_time' => $data['average_time'],
            'status'       => $data['status'],
        ]);
    }

    public function nameExists(string $name, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM services WHERE name = :name';
        $parameters = ['name' => $name];

        if ($exceptId !== null) {
            $sql .= ' AND id <> :id';
            $parameters['id'] = $exceptId;
        }

        $statement = $this->db->prepare($sql);
        $statement->execute($parameters);

        return (int) $statement->fetchColumn() > 0;
    }
}
