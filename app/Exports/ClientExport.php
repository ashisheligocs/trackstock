<?php

namespace App\Exports;

// use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientExport implements FromCollection, WithHeadings
{
    /** 
    * @return \Illuminate\Support\Collection
    */
    protected $data;

    public function __construct($data)
    {   
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            'Client ID',
            'Name',
            'Contact Number',
            'Email',
            'Address',
            'Nationality',
            'Type'
        ];
    }

   /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $columns = [];
        if(!empty($this->data)){
            foreach($this->data as $client){
                $columns [] = array(
                    $client->client_id,
                    $client->name,
                    $client->phone,
                    $client->email,
                    $client->address,
                    $client->nationality,
                    $client->type,
                );
            }
        }
        return collect($columns);
        // return $this->data;
    }
}
