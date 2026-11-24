<?php

class ServiceCounter
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function countersFor(int $serviceId): array
    {
        $statement = $this->db->prepare(
            'SELECT c.id, c.number, c.status
             FROM counters c
             INNER JOIN service_counter sc ON sc.counter_id = c.id
             WHERE sc.service_id = :id
             ORDER BY c.number'
        );
        $statement->execute(['id' => $serviceId]);

        return $statement->fetchAll();
    }

    public function serviceCountForCounter(int $counterId): int
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*) FROM service_counter WHERE counter_id = :id'
        );
        $statement->execute(['id' => $counterId]);

        return (int) $statement->fetchColumn();
    }

    public function assign(int $serviceId, int $counterId): bool
    {
        $statement = $this->db->prepare(
            'INSERT IGNORE INTO service_counter (service_id, counter_id) VALUES (:service_id, :counter_id)'
        );

        return $statement->execute([
            'service_id' => $serviceId,
            'counter_id' => $counterId,
        ]);
    }

    public function replaceForService(int $serviceId, array $counterIds): void
    {
        $this->db->beginTransaction();

        try {
            $delete = $this->db->prepare('DELETE FROM service_counter WHERE service_id = :id');
            $delete->execute(['id' => $serviceId]);

            foreach ($counterIds as $counterId) {
                $this->assign($serviceId, (int) $counterId);
            }

            $this->db->commit();
        } catch (PDOException $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }
}
