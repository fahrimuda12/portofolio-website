<?php

namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\DB;
use App\Helpers\DdlHelper;
use App\Helpers\StringHelper;
use Carbon\Carbon;

class ExcelHelper
{
    const TEMPLATE_IMPORT_NAME = 'import_templates.xlsx';
    const TEMPLATE_FIELDDATA_NAME = 'fielddata_templates.xlsx';
    const FOLDER_TEMPLATE = '/public/templates/';
    const FOLDER_UPLOADS = 'uploads';

    public static function daftarKolom()
    {
        return ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    }

    public static function generateExcel($namaFile = null,$fieldData = [], $konten = [], $relasiTable = [], $rawRelasi = [])
    {
        $daftarKolom = self::daftarKolom();

        $folder = base_path().self::FOLDER_TEMPLATE;
        $file = $namaFile ?? self::TEMPLATE_IMPORT_NAME;
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($folder.$file);
        $sheet = $spreadsheet->getActiveSheet();

        $kolomBerelasi = count($relasiTable) > 0 ? (clone($relasiTable))->pluck('source_join_via_field')->toArray() : [];

        $row = 1;
        $indexKolom = 0;
        $namaKolomAwal = $daftarKolom[0];
        foreach ($fieldData as $key => $value) {
            $namaKolom = $daftarKolom[$indexKolom];
            $sheet->setCellValue($namaKolom.$row, $value->nama);
            $indexKolom++;
        }

        if (count($relasiTable) > 0) {
            $relationRow = 2;
            $indexKolom = 0;
            foreach ($fieldData as $key => $value) {
                $namaKolom = $daftarKolom[$indexKolom];
                if (in_array($value->nama_kolom_dalam_database, $kolomBerelasi)) {
                    $dataRelasi = (clone($relasiTable))->where('source_join_via_field', $value->nama_kolom_dalam_database)->first();
                    $connection = $dataRelasi->getConnectionName();

                    $kolomLabel = empty($dataRelasi->kolom_label) ? $dataRelasi->destination_join_via_field : $dataRelasi->kolom_label;
                    $dataDropdown = DB::connection($connection)->table($dataRelasi->destination_table)->select($kolomLabel)->get();
                    $dataDropdown = array_column( json_decode(json_encode($dataDropdown), true) , $kolomLabel);

                    if (count($dataDropdown) > 0) {
                        if (StringHelper::isBase64Encoded($dataDropdown[0])) {
                            $dataDropdown = array_map('base64_decode', $dataDropdown);
                        }
                    }

                    $sheet->setCellValue($namaKolom.$row, $kolomLabel);

                    for ($idx=$relationRow; $idx < 999; $idx++) {
                        $dropdown = $sheet->getCell($namaKolom.$idx)->getDataValidation();
                        $dropdown->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                        $dropdown->setFormula1('"'.implode(',', $dataDropdown).'"');
                        $dropdown->setAllowBlank( (intval($value->is_required) == 0) );
                        $dropdown->setShowDropDown(true);
                        $dropdown->setShowInputMessage(true);
                        $dropdown->setPromptTitle('Catatan');
                        $dropdown->setPrompt('Pilihlah salah satu dari pilihan berikut!');
                        $dropdown->setShowErrorMessage(true);
                        $dropdown->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                        $dropdown->setErrorTitle('Invalid');
                        $dropdown->setError('Hanya bisa memilih salah satu data pada dropdown!');
                    }
                }
                $indexKolom++;
            }
        }
        elseif (count($rawRelasi) > 0) {
            $relationRow = 2;
            $indexKolom = 0;
            foreach ($fieldData as $key => $value) {
                $namaKolom = $daftarKolom[$indexKolom];
                if (isset($rawRelasi[$value->nama])) {
                    $dataRelasi = $rawRelasi[$value->nama];

                    for ($idx=$relationRow; $idx < 999; $idx++) {
                        $dropdown = $sheet->getCell($namaKolom.$idx)->getDataValidation();
                        $dropdown->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                        $dropdown->setFormula1('"'.implode(',', array_slice($dataRelasi, 0, 24)).'"'); //sementara
                        $dropdown->setAllowBlank(false);
                        $dropdown->setShowDropDown(true);
                        $dropdown->setShowInputMessage(true);
                        $dropdown->setPromptTitle('Catatan');
                        $dropdown->setPrompt('Pilihlah salah satu dari pilihan berikut!');
                        $dropdown->setShowErrorMessage(true);
                        $dropdown->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                        $dropdown->setErrorTitle('Invalid');
                        $dropdown->setError('Hanya bisa memilih salah satu data pada dropdown!');
                    }
                }
                $indexKolom++;
            }
        }
        elseif (count($konten) > 0) {
            $row = 2;
            foreach ($konten as $key => $value) {
                // $pk = DdlHelper::PRIMARY_KEY;
                // unset($value->$pk);
                $indexKolom = 0;
                foreach ($value as $k => $v) {
                    $namaKolom = $daftarKolom[$indexKolom];

                    $sheet->setCellValue($namaKolom.$row, $value->$k);
                    $indexKolom++;
                }
                $row++;
            }

            $row = ($row-1);
        }

        $styleArray = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
                'inside' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
            ),
        );

        $sheet = $sheet->getStyle($namaKolomAwal.'1:'.$namaKolom.$row)->applyFromArray($styleArray);

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.((count($konten) > 0) ? 'Export' : 'Import').' - data.xlsx"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    public static function downloadTemplateField()
    {
        $folder = base_path().self::FOLDER_TEMPLATE;
        $file = self::TEMPLATE_FIELDDATA_NAME;
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($folder.$file);
        $sheet = $spreadsheet->getActiveSheet();

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$file.'"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    public static function readExcelWithTemplate($file, $elemenData, $relasiTable = [])
    {
        if (!file_exists(self::FOLDER_UPLOADS)) {
            mkdir(self::FOLDER_UPLOADS, 0777, true);
        }
        $folder = self::FOLDER_UPLOADS;
        $pathFile = $folder . '/' . $file;

        if (!file_exists($pathFile)) {
            return [];
        }

        $kolomBerelasi = count($relasiTable) > 0 ? (clone($relasiTable))->pluck('source_join_via_field')->toArray() : [];

        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($pathFile);

        $validatedExcel = self::validateHeader($spreadsheet);
        if (!$validatedExcel['validated']) {
            return [];
        }

        $getAllSheet = $spreadsheet->getSheetNames();
        $getActiveSheet = $spreadsheet->getActiveSheet();
        $highestRow = $spreadsheet->setActiveSheetIndex(0)->getHighestRow();

        $daftarKolom = self::daftarKolom();
        $barisSaatIni = 2;
        $importedData = [];
        for ($i = $barisSaatIni; $i < ($highestRow + 1); $i++) {
            $indexKolom = 0;

            $data = [];
            $isValidRow = false;
            foreach ($elemenData as $key => $elemen) {
                $abjadKolom = $daftarKolom[$indexKolom];

                if (count($relasiTable) > 0 && in_array($elemen->nama_kolom_dalam_database, $kolomBerelasi)) {
                    $dataRelasi = (clone($relasiTable))->where('source_join_via_field', $elemen->nama_kolom_dalam_database)->first();
                    $connection = $dataRelasi->getConnectionName();

                    $kolomLabel = empty($dataRelasi->kolom_label) ? $dataRelasi->destination_join_via_field : $dataRelasi->kolom_label;

                    $realValue = trim($getActiveSheet->getCell($abjadKolom . $barisSaatIni)->getValue());
                    $formattedValue = trim($getActiveSheet->getCell($abjadKolom . $barisSaatIni)->getFormattedValue());

                    if (strval($realValue) == strval($formattedValue)) {
                        $data[$kolomLabel] = $realValue;
                    } else {
                        $validateExcelDateValue = false;
                        if (strtotime($formattedValue) != false) {
                            $dateValue = Carbon::parse($formattedValue)->format('Y-m-d');
                            $validateExcelDateValue = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($dateValue);
                        }

                        $data[$kolomLabel] = $validateExcelDateValue ? $dateValue : $formattedValue;
                    }

                    if (strlen($data[$kolomLabel]) > 0) {
                        $isValidRow = true;
                    }
                } else {
                    $realValue = trim($getActiveSheet->getCell($abjadKolom . $barisSaatIni)->getValue());
                    $formattedValue = trim($getActiveSheet->getCell($abjadKolom . $barisSaatIni)->getFormattedValue());

                    if (strval($realValue) == strval($formattedValue)) {
                        $data[$elemen->nama] = $realValue;
                    } else {
                        $validateExcelDateValue = false;
                        if (strtotime($formattedValue) != false) {
                            $dateValue = Carbon::parse($formattedValue)->format('Y-m-d');
                            $validateExcelDateValue = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($dateValue);
                        }

                        $data[$elemen->nama] = $validateExcelDateValue ? $dateValue : $formattedValue;
                    }

                    if (strlen($data[$elemen->nama]) > 0) {
                        $isValidRow = true;
                    }

                    if ($elemen->is_required && strlen($data[$elemen->nama]) == 0) {
                        return 'Kolom '.$elemen->nama.' wajib diisi!';
                    }
                }

                $indexKolom++;
            }

            if ($isValidRow) {
                $importedData[] = $data;
            }
            $barisSaatIni++;
        }

        return $importedData;
    }

    public static function readExcelWithoutTemplate($file)
    {
        if (!file_exists(self::FOLDER_UPLOADS)) {
            mkdir(self::FOLDER_UPLOADS, 0777, true);
        }
        $folder = self::FOLDER_UPLOADS;
        $pathFile = $folder . '/' . $file;

        if (!file_exists($pathFile)) {
            return [];
        }

        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($pathFile);

        $validatedExcel = self::validateHeader($spreadsheet);
        if (!$validatedExcel['validated']) {
            return [];
        }

        $getAllSheet = $spreadsheet->getSheetNames();
        $getActiveSheet = $spreadsheet->getActiveSheet();
        $highestRow = $spreadsheet->setActiveSheetIndex(0)->getHighestRow();
        $highestColumn = $validatedExcel['highest_valid_column'];
        $range = range('A', 'Z');
        $countKolom = array_keys($range, $highestColumn)[0] + 1;

        $daftarKolom = self::daftarKolom();
        $barisSaatIni = 2;
        $kolomText = [];
        $importedData = [];
        for ($i = $barisSaatIni; $i < ($highestRow + 1); $i++) {
            $indexKolom = 0;

            $data = [];
            $isValidRow = false;
            for ($j = 0; $j < $countKolom; $j++) {
                $abjadKolom = $daftarKolom[$indexKolom];
                $namaKolom = $getActiveSheet->getCell($abjadKolom . '1')->getValue();
                $namaKolom = strtolower(str_replace(config('database.removed_char'), '_', $namaKolom));

                $realValue = trim($getActiveSheet->getCell($abjadKolom . $barisSaatIni)->getValue());
                $formattedValue = trim($getActiveSheet->getCell($abjadKolom . $barisSaatIni)->getFormattedValue());

                if (strval($realValue) == strval($formattedValue)) {
                    $data[$namaKolom] = $realValue;
                } else {
                    $validateExcelDateValue = false;
                    if (strtotime($formattedValue) != false) {
                        $dateValue = Carbon::parse($formattedValue)->format('Y-m-d');
                        $validateExcelDateValue = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($dateValue);
                    }

                    $data[$namaKolom] = $validateExcelDateValue ? $dateValue : $formattedValue;
                }

                if (strlen($data[$namaKolom]) > 0) {
                    $isValidRow = true;
                }

                if (strlen($data[$namaKolom]) > 200) {
                    if (!in_array($namaKolom, $kolomText)) {
                        $kolomText[] = $namaKolom;
                    }
                }

                $indexKolom++;
            }

            if ($isValidRow) {
                $importedData[] = $data;
            }
            $barisSaatIni++;
        }

        return [
            'imported_data' => $importedData,
            'kolom_text' => $kolomText
        ];
    }

    public static function readCsvFile($file, $separator = ',')
    {
        if (!file_exists(self::FOLDER_UPLOADS)) {
            mkdir(self::FOLDER_UPLOADS, 0777, true);
        }
        $folder = self::FOLDER_UPLOADS;
        $filePath = $folder . '/' . $file;

        if (!file_exists($filePath)) {
            return [];
        }

        $getContent = file_get_contents($filePath);

        if (empty($getContent)) {
            return [];
        }

        $arrayContent = explode("\r\n", $getContent);
        foreach ($arrayContent as $key => $konten) {
            if (empty($konten)) {
                unset($arrayContent[$key]);
            }
        }
        $arrayContent = array_values($arrayContent);

        if (count($arrayContent) == 0) {
            return [];
        }

        $header = explode($separator, $arrayContent[0]);
        array_shift($arrayContent);
        $importedData = [];
        $kolomText = [];

        foreach ($arrayContent as $konten) {
            $arrayKonten = explode($separator, $konten);
            $tempValue = [];
            $isValidRow = false;
            foreach ($header as $idx => $value) {
                $tempValue[$value] = $arrayKonten[$idx];

                if (strlen($tempValue[$value]) > 0) {
                    $isValidRow = true;
                }

                if (strlen($tempValue[$value]) > 200) {
                    if (!in_array($value, $kolomText)) {
                        $kolomText[] = $value;
                    }
                }
            }

            if ($isValidRow) {
                $importedData[] = $tempValue;
            }
        }

        return [
            'imported_data' => $importedData,
            'kolom_text' => $kolomText
        ];
    }

    private static function validateHeader($spreadsheet)
    {
        $getActiveSheet = $spreadsheet->getActiveSheet();
        $highestColumn = $spreadsheet->setActiveSheetIndex(0)->getHighestColumn();
        $highestValidColumn = null;
        $validated = true;

        $range = range('A', $highestColumn);
        foreach ($range as $abjad) {
            if (!empty($getActiveSheet->getCell($abjad . '1')->getValue())) {
                $highestValidColumn = $abjad;
            }
        }

        if (is_null($highestValidColumn)) {
            $validated = false;
        }

        return [
            'validated' => $validated,
            'highest_valid_column' => $highestValidColumn,
        ];
    }
}
