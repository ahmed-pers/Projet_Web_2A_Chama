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
}
