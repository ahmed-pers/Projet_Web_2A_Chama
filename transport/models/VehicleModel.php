<?php
declare(strict_types=1);

class VehicleModel
{
    private PDO $db;

    public function __construct(PDO $db) { $this->db = $db; }

    public function all(bool $availableOnly = false): array
    {
        $sql = 'SELECT * FROM rental_vehicle' . ($availableOnly ? " WHERE status = 'available'" : '') . ' ORDER BY id DESC';
        return $this->db->query($sql)->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM rental_vehicle WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $vehicle): void
    {
        $stmt = $this->db->prepare('INSERT INTO rental_vehicle (name,type,location,details,price_per_day,status) VALUES (:name,:type,:location,:details,:price,:status)');
        $this->save($stmt, $vehicle);
    }

    public function update(int $id, array $vehicle): void
    {
        $stmt = $this->db->prepare('UPDATE rental_vehicle SET name=:name,type=:type,location=:location,details=:details,price_per_day=:price,status=:status WHERE id=:id');
        $this->save($stmt, $vehicle + ['id' => $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM rental_vehicle WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    private function save(PDOStatement $stmt, array $vehicle): void
    {
        $stmt->execute([
            'name' => $vehicle['name'], 'type' => $vehicle['type'],
            'location' => $vehicle['location'], 'details' => $vehicle['details'] ?: null,
            'price' => $vehicle['price'], 'status' => $vehicle['status'],
        ] + (isset($vehicle['id']) ? ['id' => $vehicle['id']] : []));
    }
}
