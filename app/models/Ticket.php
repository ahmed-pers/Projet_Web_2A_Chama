<?php

class Ticket extends Model
{
    protected string $table = 'TICKET';
    protected string $primaryKey = 'id_Ticket';

    public function create(array $data): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO TICKET (id_Ticket, id_client, id_service, `date`, `time`)
             VALUES (:id, :client, :service, :date, :time)'
        );
        $statement->execute([
            'id'      => $data['id_Ticket'],
            'client'  => $data['id_client'],
            'service' => $data['id_service'],
            'date'    => $data['date'],
            'time'    => $data['time'],
        ]);
    }

    // Jointure TICKET <-> SERVICE <-> CATEGORIE
    public function allWithDetails(): array
    {
        $statement = $this->db->query(
            'SELECT t.id_Ticket, t.id_client, t.`date`, t.`time`,
                    s.nomService, s.status, c.nomCategorie
             FROM TICKET t
             INNER JOIN SERVICE s ON s.id_service = t.id_service
             INNER JOIN CATEGORIE c ON c.id_categorie = s.id_categorie
             ORDER BY t.`date`, t.`time`'
        );

        return $statement->fetchAll();
    }

    public function existsOnDate(int $client, int $service, string $date): bool
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*) FROM TICKET
             WHERE id_client = :client AND id_service = :service AND `date` = :date'
        );
        $statement->execute([
            'client'  => $client,
            'service' => $service,
            'date'    => $date,
        ]);

        return (int) $statement->fetchColumn() > 0;
    }
}
