<?php

namespace App\Http\Controllers\Shippers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\ReadFilter;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use ZipArchive;

class ShipperTelenorController extends Controller
{

    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    private function read_recon_file($file) {
        $sheet_name = 'App & CCD';
        $spreadsheet = IOFactory::createReaderForFile($file);
        $spreadsheet->setReadDataOnly(TRUE);
        $spreadsheet->setLoadSheetsOnly($sheet_name);
        $spreadsheet->setReadFilter(new ReadFilter());
        $spreadsheet->getReadEmptyCells(FALSE);
        $spreadsheet = $spreadsheet->load($file)->getSheetByName($sheet_name)->toArray(NULL, FALSE, FALSE, FALSE);

        if (empty($spreadsheet) || count($spreadsheet) == 1) {
            return FALSE;
        }
        else {
            unset($spreadsheet[0]);

            return $spreadsheet;
        }
    }

    private function zip_file_creation($path, $filename) {
        $zip = new ZipArchive();

        File::isDirectory($path) or File::makeDirectory($path, 0777, TRUE, TRUE);

        if (file_exists($filename)) {
            unlink($filename);
        }

        if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
            return FALSE;
        }
        else {
            return $zip;
        }
    }

    private function type_definer($identifier) {
        if ($identifier == 500) {
            return 'up';
        }
        else {
            return 'paypak';
        }
    }

    private function stationary_verification(&$misidn, $file, &$card_numbers, $zip, $date, $type) {
        $stationary = explode("\n", file_get_contents($file));
        $carry_forward_stationary = array();

        if (!empty($misidn) && !empty($stationary)) {
            $stationary_length = 341;

            foreach ($stationary as $index => $row) {
                $key = array_search(substr($row, 23, 10), $misidn);

                if ($key !== FALSE) {
                    $row = preg_replace('~[\r\n]+~', '', preg_replace("/\|+/", "|", $row));

                    $length = $stationary_length - strlen($row);

                    if ($length > 0) {
                        $row = $row . str_repeat(" ", $length);
                    }

                    $stationary[$index] = $row;

                    $card_number = substr($row, 0, 16);

                    $card_numbers[] = $card_number;

                    $phone_number = $misidn[$key];

                    unset($misidn[$key]);
                }
                else {
                    $carry_forward_stationary[] = $row;

                    unset($stationary[$index]);
                }
            }

            $zip->addFromString($date . '_stationary_' . $type . '_' . count($stationary) . '.txt', implode("\n", $stationary));

            $zip->addFromString($date . '_carry_forward_stationary_' . $type . '_' . count($carry_forward_stationary) . '.txt', implode("\n", $carry_forward_stationary));
        }

        unset($stationary);
        unset($carry_forward_stationary);
    }

    private function missing_misidn_file_creation($misidn, $zip, $date) {
        $missing_misidn = array();

        if (!empty($misidn['up'])) {
            $missing_misidn = array_merge($missing_misidn, $misidn['up']);
        }

        if (!empty($misidn['paypak'])) {
            $missing_misidn = array_merge($missing_misidn, $misidn['paypak']);
        }

        if (!empty($missing_misidn)) {
            $zip->addFromString($date . '_missing_misidn_' . count($missing_misidn) . '.txt', implode("\n", $missing_misidn));
        }

        unset($missing_misidn);
    }

    private function card_file_creation($file, &$card_numbers, $zip, $date, $type) {
        $card = explode("\n", file_get_contents($file));
        $carry_forward_card = array();

        if (!empty($card_numbers) && !empty($card)) {
            if ($type == 'up') {
                $position = 304;
            }
            else {
                $position = 290;
            }

            foreach ($card as $index => $row) {
                $key = array_search(substr($row, $position, 16), $card_numbers);

                if ($key !== FALSE) {
                    unset($card_numbers[$key]);

                    $row = str_replace('i', 'I', $row);
                    $row = str_replace('j', 'J', $row);

                    $card[$index] = $row;
                }
                else {
                    $carry_forward_card[] = $row;

                    unset($card[$index]);
                }
            }

            $zip->addFromString($date . '_card_' . $type . '_' . count($card) . '.txt', implode("\n", $card));

            $zip->addFromString($date . '_carry_forward_card_' . $type . '_' . count($carry_forward_card) . '.txt', implode("\n", $carry_forward_card));
        }

        unset($card);
        unset($carry_forward_card);
    }

    public function data_conversion_index() {
        return view('client.telenor.data_conversion');
    }

    public function data_conversion_store(Request $request) {
        if (!empty($request->files)) {
            if ($spreadsheet = $this->read_recon_file($request->file('recon'))) {
                $date = date('Y_m_d', time());

                $path = 'storage/telenor/data_conversion/';
                $zip_filename = $path . $date . '.zip';

                if ($zip = $this->zip_file_creation($path, $zip_filename)) {
                    $misidn = array();
                    $misidn['up'] = array();
                    $misidn['paypak'] = array();

                    foreach ($spreadsheet as $row) {
                        $type = $this->type_definer($row[4]);

                        $misidn[$type][] = $row[5];
                    }

                    unset($spreadsheet);

                    $card_numbers = array();
                    $card_numbers['up'] = array();
                    $card_numbers['paypak'] = array();

                    $this->stationary_verification($misidn['up'], $request->file('stationary_file_up'), $card_numbers['up'], $zip, $date, 'up');
                    $this->stationary_verification($misidn['paypak'], $request->file('stationary_file_paypak'), $card_numbers['paypak'], $zip, $date, 'paypak');

                    $this->missing_misidn_file_creation($misidn, $zip, $date);

                    unset($misidn);

                    $this->card_file_creation($request->file('card_file_up'), $card_numbers['up'], $zip, $date, 'up');
                    $this->card_file_creation($request->file('card_file_paypak'), $card_numbers['paypak'], $zip, $date, 'paypak');

                    unset($card_numbers);

                    $zip->close();

                    return response()->download($zip_filename);
                }
                else {
                    return redirect()->back()->with('error', 'Cannot Create Zip');
                }
            }
            else {
                return redirect()->back()->with('error', 'Empty Recon File');
            }
        }
        else {
            return redirect()->back()->with('error', 'No Files');
        }
    }
}
