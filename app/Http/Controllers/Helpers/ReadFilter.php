<?php

namespace App\Http\Controllers\Helpers;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ReadFilter implements IReadFilter
{
    public function readCell($column, $row, $worksheetName = '') {
        if (in_array($column, ['E','F', 'I', 'O', 'P', 'Q', 'R', 'S', 'T', 'U'])) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
}
