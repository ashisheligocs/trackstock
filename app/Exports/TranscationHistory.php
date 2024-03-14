<?php

namespace App\Exports;
 
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
 
use App\Models\Product;
// use App\Http\Resources\AccountTransactionResource; 
 
class TranscationHistory implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data) 
    {
        
        $this->data = $data;
         
    }

    public function headings(): array
    {
        return [
            'Date',
            'Hotel', 
            'Description',
            'Debit',
            'Credit',
            'Created By'
        ];
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $columns = [];
        if (!empty($this->data)) {
            $i = 0;
            
            foreach ($this->data as $key => $list) {
                    if($i == $key)
                    {
                        $columns[] = array(
                            '',
                            '',
                            $list['note'],
                            '',
                            '',
                        );
                    }
                
                foreach($list['items'] as $transaction){
                    // echo $transaction['date']; exit();
                    $columns[] = array(
                        $transaction['date'],
                        $transaction['hotel'],
                        $transaction['ledger_name'] .'-'. $transaction['ledger_category'] .'( '.$transaction['ledger_type'].' )',
                        $transaction['debit'] ?? 0,
                        $transaction['credit'] ?? 0,
                        $transaction['user_name'] ?? '',
                    );
                }
               $i++; 
            }
        }
        return collect($columns);
    }
}