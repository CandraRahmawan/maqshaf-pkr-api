<?php

namespace App\Exports;

use App\Models\User;
use App\Models\DepositTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Invoice_debet implements FromCollection, WithHeadings
{
    protected $year;
    protected $month;

    function __construct($year, $month) {
        $this->year = $year;
        $this->month = $month;
    }

    public function headings(): array
    {
        return [            
            'Code',
            'Tanggal',
            'Debet',
            'NIS',
            'Nama Lengkap',
            'Kelas'
        ];
    }

    public function collection()
    {
        return DepositTransaction::printAllDebitByYearAndMonth($this->year, $this->month);
    }
}

