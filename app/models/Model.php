<?php

abstract class Model
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function all(): array
    {
        $statement = $this->db->query('SELECT * FROM ' . $this->table . ' ORDER BY ' . $this->primaryKey);
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT * FROM ' . $this->table . ' WHERE ' . $this->primaryKey . ' = :id'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        return $row ?: null;
    }

    public function exists(int $id): bool
    {
        return $this->find($id) !== null;
    }

    public function nextId(): int
    {
        $statement = $this->db->query(
            'SELECT COALESCE(MAX(' . $this->primaryKey . '), 0) + 1 FROM ' . $this->table
        );

        return (int) $statement->fetchColumn();
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare(
            'DELETE FROM ' . $this->table . ' WHERE ' . $this->primaryKey . ' = :id'
        );
        return $statement->execute(['id' => $id]);
    }
}
