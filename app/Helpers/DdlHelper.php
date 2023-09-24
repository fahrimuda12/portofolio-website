<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DdlHelper
{
    const PRIMARY_KEY = 'system_app_id';
    const STRING_KOLOM_DIBUAT = 'dibuat_pada';
    const STRING_KOLOM_DIUBAH = 'diubah_pada';
    const STRING_KOLOM_DIHAPUS = 'dihapus_pada';
    const STRING_KOLOM_CHECKSUM = 'checksum_id';
    const DEFAULT_KOLOM = [
        'nama' => 'system_app_id',
        'tipe_data' => 'serial'
    ];
    const KOLOM_TIMESTAMP = [
        [
            'nama' => self::STRING_KOLOM_DIBUAT,
            'tipe_data' => 'timestamp'
        ],
        [
            'nama' => 'dibuat_oleh_user_id',
            'tipe_data' => 'int2'
        ],
        [
            'nama' => self::STRING_KOLOM_DIUBAH,
            'tipe_data' => 'timestamp'
        ],
        [
            'nama' => 'diubah_oleh_user_id',
            'tipe_data' => 'int2'
        ],
        [
            'nama' => self::STRING_KOLOM_DIHAPUS,
            'tipe_data' => 'timestamp'
        ],
        [
            'nama' => 'dihapus_oleh_user_id',
            'tipe_data' => 'int2'
        ]
    ];

    public static $isWithPk = 0;
    public static $isWithSystemId = 0;
    public static $isWithTimestamp = 0;

    public static function createTableIfNotFound($namaTabel, $arrayKolom, $connection = null)
    {
        $connection = self::getConnection($connection);

        if (!empty($namaTabel)) {
            array_unshift($arrayKolom, self::DEFAULT_KOLOM);

            if (is_null($connection)) {
                $arrayKolom = array_merge($arrayKolom, self::KOLOM_TIMESTAMP);
            }

            $namaTabel = strtolower(str_replace(config('database.removed_char'), '_', $namaTabel));
            if (!self::isHasTable($namaTabel, $connection)) {
                $sqlCreateTable = "CREATE TABLE IF NOT EXISTS ".$namaTabel." (";

                foreach ($arrayKolom as $key => $val) {
                    $kolom = strtolower(str_replace(config('database.removed_char'), '_', $val['nama']));
                    $tipeData = strtolower($val['tipe_data']);

                    $sqlCreateTable .= $kolom.' '
                        .$tipeData.' '
                        .($key == 0 ? 'NOT NULL' : 'NULL')
                        .($key+1 == count($arrayKolom) ? '' : ', ');
                }

                $sqlCreateTable .= ', PRIMARY KEY ('.self::PRIMARY_KEY.')';
                $sqlCreateTable .= '); ';

                DB::connection($connection)->select($sqlCreateTable);
            } else {
                $kolomSaatIni = Schema::connection($connection)->getColumnListing($namaTabel);
                $kolomChanged = [];
                foreach ($arrayKolom as $key => $kolom) {
                    if (!in_array($kolom['nama'], $kolomSaatIni)) {
                        $kolomChanged[] = $kolom;
                    }
                }

                if (count($kolomChanged) > 0) {
                    $sqlAlterTable = "ALTER TABLE ".$namaTabel;
                    foreach ($kolomChanged as $key => $val) {
                        $kolom = strtolower(str_replace(config('database.removed_char'), '_', $val['nama']));
                        $tipeData = strtolower($val['tipe_data']);

                        $sqlAlterTable .= ' ADD COLUMN '.$kolom.' '
                            .$tipeData.' '
                            .($key+1 == count($kolomChanged) ? '' : ', ');
                    }

                    DB::connection($connection)->select($sqlAlterTable);
                }
            }
        }
    }

    public static function renameTable($namaLama, $namaBaru, $connection = null)
    {
        $connection = self::getConnection($connection);

        if (self::isHasTable($namaLama, $connection)) {
            $sql = "ALTER TABLE ".$namaLama." RENAME TO ".$namaBaru;
            DB::connection($connection)->select($sql);
        }
    }

    public static function dropDb($namaDb, $connection = null)
    {
        $connection = self::getConnection($connection);
        $sql = "DROP DATABASE IF EXISTS " . $namaDb;
        DB::connection($connection)->select($sql);
    }

    public static function dropTable($namaTabel, $connection = null)
    {
        $connection = self::getConnection($connection);

        if (self::isHasTable($namaTabel, $connection)) {
            $sql = "DROP TABLE ".$namaTabel;
            DB::connection($connection)->select($sql);
        }
    }

    public static function isHasTable($namaTabel, $connection = null)
    {
        $connection = self::getConnection($connection);
        return Schema::connection($connection)->hasTable($namaTabel);
    }

    public static function updateTableStructure($table, $kolomTabelLama, $kolomTabelBaru, $connection = null)
    {
        $connection = self::getConnection($connection);

        if (!self::isHasTable($table, $connection)) {
            return false;
        }

        $dropKolom = [];
        $changeTypeKolom = [];
        $renameKolom = [];
        $addKolom = [];

        foreach ($kolomTabelLama as $key => $kolomLama) {
            $kolTypeLama = (self::getDetailTableColumn($table, $kolomLama, $connection))->udt_name;

            $arrayKolomBaru = array_values(array_filter($kolomTabelBaru, fn ($item) => $item['nama'] == $kolomLama));
            $kolomBaru = count($arrayKolomBaru) > 0 ? strtolower($arrayKolomBaru[0]['nama']) : null;
            $kolTypeBaru = count($arrayKolomBaru) > 0 ? strtolower($arrayKolomBaru[0]['tipe_data']) : null;
            $kolTypeBaru = explode(' ', $kolTypeBaru);
            $kolTypeBaru = isset($kolTypeBaru[0]) ? $kolTypeBaru[0] : '';

            if (strlen($kolTypeBaru) == 0) {
                $dropKolom[] = $kolomLama;
                continue;
            }
            elseif (!is_numeric(strpos($kolTypeLama, $kolTypeBaru))) {
                $changeTypeKolom[] = ['kol' => $kolomLama, 'val' => $kolTypeBaru];
                continue;
            }
            elseif ($kolomBaru != $kolomLama) {
                $renameKolom[] = ['kol' => $kolomLama, 'val' => $kolomBaru];
                continue;
            }
        }

        $countBaru = count($kolomTabelBaru);
        $countLama = count($kolomTabelLama);
        if ($countBaru > $countLama) {
            for ($i=$countLama; $i < $countBaru; $i++) {
                $addKolom[] = $kolomTabelBaru[$i];
            }
        }


        foreach ($dropKolom as $kolom) {
            $sqlAlter = "ALTER TABLE ".$table." DROP COLUMN ".$kolom;
            DB::connection($connection)->select($sqlAlter);
        }

        foreach ($addKolom as $kolom) {
            $sqlAlter = "ALTER TABLE ".$table." ADD COLUMN ".$kolom['nama']." ".$kolom['tipe_data'];
            DB::connection($connection)->select($sqlAlter);
        }

        foreach ($changeTypeKolom as $kolom) {
            $sqlAlter = "ALTER TABLE ".$table." ALTER COLUMN ".$kolom['kol']." TYPE ".$kolom['val'];
            DB::connection($connection)->select($sqlAlter);
        }

        foreach ($renameKolom as $kolom) {
            $sqlAlter = "ALTER TABLE ".$table." RENAME COLUMN ".$kolom['kol']." TO ".$kolom['val'];
            DB::connection($connection)->select($sqlAlter);
        }

        return true;
    }

    public static function backupDropRecreateTable($table, $kolomBaru, $connection = null)
    {
        $connection = self::getConnection($connection);

        $backupData = DB::connection($connection)->table($table)->get();
        self::dropTable($table, $connection);
        self::createTableIfNotFound($table, $kolomBaru, false, $connection);

        array_unshift($kolomBaru, self::DEFAULT_KOLOM);

        if (count($backupData) > 0) {
            $arrayRestoreData = [];
            foreach ($backupData as $key => $backup) {
                $kolomLama = [];
                array_push($arrayRestoreData, []);
                foreach ($backup as $idx => $data) {
                    array_push($kolomLama, $idx);
                    $arrayRestoreData[$key][$idx] = $data;
                }

                //Cek apakah ada kolom baru yg belum ada di field backup data
                $arrayKolomBaru = [];
                foreach ($kolomBaru as $kolom) {
                    $column = str_replace(config('database.removed_char'), '_', strtolower($kolom['nama']));
                    $arrayKolomBaru[] = $column;

                    if (!in_array($column, $kolomLama)) {
                        switch ($kolom['tipe_data']) {
                            case 'float':
                            case 'int':
                                $arrayRestoreData[$key][$column] = 0;
                                break;

                            case 'timestamp':
                                $arrayRestoreData[$key][$column] = date('Y-m-d H:i:s');
                                break;

                            default:
                                $arrayRestoreData[$key][$column] = null;
                                break;
                        }
                    }
                }

                //Cek apakah ada kolom lama yg sudah tidak dibutuhkan di design table baru
                $fieldTidakTerpakai = [];
                foreach ($kolomLama as $field) {
                    if(!in_array($field, $arrayKolomBaru)) {
                        $fieldTidakTerpakai[] = $field;
                    }
                }

                //Unset field tidak terpakai dari data backup
                foreach ($fieldTidakTerpakai as $field) {
                    unset($arrayRestoreData[$key][$field]);
                }
            }

            DB::connection($connection)->table($table)->insert($arrayRestoreData);
        }

    }

    public static function countData($namaTabel, $connection = null)
    {
        $connection = self::getConnection($connection);
        return (self::isHasTable($namaTabel, $connection)) ? DB::connection($connection)->table($namaTabel)->count() : 0;
    }

    public static function getAllCreatedTables($connection = null, $isSemua = false, $cari = '')
    {
        $connection = self::getConnection($connection);

        $whereQuery = strlen($cari) > 0 ? " AND table_name ILIKE '%".$cari."%' " : '';

        $tableNotIn = [];
        if (!$isSemua) {
            $tableNotIn = array_map(function ($str){
                return "'".$str."'";
            }, config('data.system_table'));
        }

        if (count($tableNotIn) > 0) {
            $whereQuery .= " AND table_name not in (".implode(',', $tableNotIn).")";
        }

        $tables = DB::connection($connection)->select("SELECT table_name FROM information_schema.tables
            WHERE table_type='BASE TABLE'
            AND is_insertable_into='YES'
            AND LEFT(table_name, 2) != 'pg'
            AND LEFT(table_name, 3) != 'sql'
            $whereQuery
            ORDER BY table_name
        ");

        $returnTable = array_column(json_decode(json_encode($tables), true), 'table_name');
        return $returnTable;
    }

    public static function getAllTableColumn($namaTabel, $connection = null)
    {
        $connection = self::getConnection($connection);

        if (!Schema::connection($connection)->hasTable($namaTabel)) {
            return [];
        }

        $listKolom = Schema::connection($connection)->getColumnListing($namaTabel);
        if (!self::$isWithPk && count($listKolom) > 0) {
            if ($listKolom[0] == self::getPrimaryKeyTable($namaTabel, $connection)) {
                unset($listKolom[0]);
            }
        }

        if (!self::$isWithSystemId) {
            $listKolom = array_filter($listKolom, fn ($item) => $item != DdlHelper::PRIMARY_KEY);
        }

        if (!self::$isWithTimestamp) {
            $listKolom = array_filter($listKolom, fn ($item) => !in_array($item, array_column(self::KOLOM_TIMESTAMP, 'nama')));
        }

        return array_values($listKolom);
    }

    public static function getDetailTableColumn($namaTabel, $namaKolom, $connection = null)
    {
        $connection = self::getConnection($connection);

        if (!Schema::connection($connection)->hasTable($namaTabel)) {
            return null;
        }

        $detail = DB::connection($connection)->select("SELECT * FROM information_schema.columns
            WHERE table_name = '".$namaTabel."'
            AND column_name = '".$namaKolom."'
        ");

        return count($detail) > 0 ? $detail[0] : null;
    }

    public static function getColumnDataType($namaTabel, $namaKolom, $connection = null)
    {
        $detailColumn = self::getDetailTableColumn($namaTabel, $namaKolom, $connection);
        return is_null($detailColumn) ? null : $detailColumn->udt_name;
    }

    public static function getColumnType($namaTabel, $namaKolom, $connection = null)
    {
        $connection = self::getConnection($connection);

        if (!Schema::connection($connection)->hasTable($namaTabel)) {
            return null;
        }

        return Schema::connection($connection)->getColumnType($namaTabel, $namaKolom);
    }

    public static function getPrimaryKeyTable($namaTabel, $connection = null)
    {
        $connection = self::getConnection($connection);

        if (!Schema::connection($connection)->hasTable($namaTabel)) {
            return null;
        }

        $listIndex = DB::connection($connection)->getDoctrineSchemaManager()->listTableIndexes($namaTabel);

        $columns = null;
        if (array_key_exists('primary', $listIndex)) {
            $pk = $listIndex['primary']->getColumns();

            if (is_array($pk)) {
                $columns = $pk;
            }
        }

        return is_null($columns) ? null : (count($columns) > 0 ? $columns[0] : null);
    }

    public static function getOpsiKolom($params)
    {
        $tabel = isset($params['tabel']) ? $params['tabel'] : null;
        if (is_null($tabel)) {
            return [];
        }

        $data = self::getAllTableColumn($tabel, null, true);

        $returnArray = [];
        foreach ($data as $key => $value) {
            $detailColumn = self::getDetailTableColumn($tabel, $value);

            $returnArray[$key]['text'] = $value;
            $returnArray[$key]['value'] = $value;
            $returnArray[$key]['tipe_data_id'] = $detailColumn->udt_name == 'varchar' ? $detailColumn->udt_name.'-(255)' : $detailColumn->udt_name;
        }

        return $returnArray;
    }

    private static function getConnection($connection)
    {
        return config('database.default');
    }

    public static function setConnection($connection = null, $userId = null)
    {
        return self::getConnection($connection, $userId);
    }
}
