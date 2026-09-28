<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;


class InvoicesExport implements FromCollection
{
    public $_data;

    public function __construct($_data)
    {
        $this->_data = $_data;
    }


    public function collection()
    {
//        return dd($this->_data);

        return $this->_data;
    }
}
