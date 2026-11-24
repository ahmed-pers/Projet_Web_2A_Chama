<?php

class Counter extends Model
{
    protected string $table = 'counters';

    public function create(array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO counters (number, location, status)
             VALUES (:number, :location, :status)'
        );
        $statement->execute([
            'number'   => $data['number'],
            'location' => $data['location'],
            'status'   => $data['status'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $statement = $this->db->prepare(
            'UPDATE counters
             SET number = :number, location = :location, status = :status
             WHERE id = :id'
        );

        return $statement->execute([
            'id'       => $id,
            'number'   => $data['number'],
            'location' => $data['location'],
            'status'   => $data['status'],
        ]);
    }

    public function numberExists(string $number, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM counters WHERE number = :number';
        $parameters = ['number' => $number];

        if ($exceptId !== null) {
            $sql .= ' AND id <> :id';
            $parameters['id'] = $exceptId;
        }

        $statement = $this->db->prepare($sql);
        $statement->execute($parameters);

        return (int) $statement->fetchColumn() > 0;
    }
}
