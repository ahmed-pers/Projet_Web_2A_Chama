<?php

class Model
{
    protected $db;
    protected $table;
    protected $primaryKey;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function all()
    {
        $sql = 'SELECT * FROM ' . $this->table . ' ORDER BY ' . $this->primaryKey;
        return $this->db->query($sql)->fetchAll();
    }

    public function find($id)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE ' . $this->primaryKey . ' = :id';
        $statement = $this->db->prepare($sql);
        $statement->execute(['id' => $id]);

        return $statement->fetch();
    }

    // Next id = biggest id + 1 (the tables do not use AUTO_INCREMENT).
    public function nextId()
    {
        $sql = 'SELECT COALESCE(MAX(' . $this->primaryKey . '), 0) + 1 FROM ' . $this->table;
        return $this->db->query($sql)->fetchColumn();
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM ' . $this->table . ' WHERE ' . $this->primaryKey . ' = :id';
        $statement = $this->db->prepare($sql);
        $statement->execute(['id' => $id]);
    }
}
