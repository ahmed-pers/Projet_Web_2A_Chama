<?php

class Ticket extends Model
{
    protected $table = 'TICKET';
    protected $primaryKey = 'id_Ticket';

    public function create($data)
    {
        $sql = 'INSERT INTO TICKET (id_Ticket, id_client, id_service, `date`, `time`)
                VALUES (:id, :client, :service, :date, :time)';
        $statement = $this->db->prepare($sql);
        $statement->execute([
            'id'      => $data['id_Ticket'],
            'client'  => $data['id_client'],
            'service' => $data['id_service'],
            'date'    => $data['date'],
            'time'    => $data['time']
        ]);
    }
}
