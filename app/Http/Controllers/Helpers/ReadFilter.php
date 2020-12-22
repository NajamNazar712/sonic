<?php

namespace App\Http\Controllers\Helpers;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ReadFilter implements IReadFilter
{
    public function readCell($column, $row, $worksheetName = '') {
        if (in_array($column, ['E','F'])) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
}
