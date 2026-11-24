<?php

abstract class Model
{
    protected PDO $db;
    protected string $table;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function all(): array
    {
        $statement = $this->db->query('SELECT * FROM ' . $this->table . ' ORDER BY id');
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM ' . $this->table . ' WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        return $row ?: null;
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare('DELETE FROM ' . $this->table . ' WHERE id = :id');
        return $statement->execute(['id' => $id]);
    }
}
