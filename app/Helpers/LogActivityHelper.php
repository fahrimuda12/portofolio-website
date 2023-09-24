<?php

namespace App\Helpers;

use App\Repositories\Log\LogActivityRepository;

class LogActivityHelper
{
    const ELEMEN_BASIC_CRUD_DICTIONARY = [
        'user',
        'user-group',
        'pengaturan',
        'wilayah',
        'custom-element-data',
        'klasifikasi-map-static',
        'map-static',
        'faq',
        'attribut',
        'attribut-custom-element-data',
        'data-source'
    ];
    const ELEMEN_BASIC_CRUD_WITH_SETAKTIF_DICTIONARY = [
        'layer'
    ];

    public static function simpanLog($activeUser, $aksi, $ids)
    {
        return (new LogActivityRepository())->aksiSimpan($activeUser, $aksi, $ids);
    }

    public static function getMap($key)
    {
        $arrayDictionary = self::getDictionary();
        return isset($arrayDictionary[$key]) ? $arrayDictionary[$key] : [];
    }

    public static function getDictionary()
    {
        // raw dictionary
        $dictionary = [
            'login' => [
                'nama' => 'Login Aplikasi',
                'keterangan' => self::messageKeterangan('login')
            ],
            'registrasi' => [
                'nama' => 'Registrasi Akun',
                'keterangan' => self::messageKeterangan('pendaftaran akun baru')
            ],
            'lupa-password' => [
                'nama' => 'Lupa Password',
                'keterangan' => self::messageKeterangan('proses lupa password akun')
            ],
            'ubah-profile' => [
                'nama' => 'Ubah Profile',
                'keterangan' => self::messageKeterangan('perubahan terhadap informasi profile akun')
            ],
            'ubah-password' => [
                'nama' => 'Ubah Password',
                'keterangan' => self::messageKeterangan('perubahan terhadap password akun')
            ],

            // user
            'reset-password-user' => [
                'nama' => 'Reset password user',
                'keterangan' => self::messageKeterangan('reset password user')
            ],

            // kegiatan
            'verifikasi-kegiatan' => [
                'nama' => 'Verifikasi kegiatan',
                'keterangan' => self::messageKeterangan('verifikasi kegiatan')
            ],
        ];

        // auto dictionary
        $mergedCrudGeneratorDict = [];
        foreach (self::ELEMEN_BASIC_CRUD_DICTIONARY as $elemen) {
            $mergedCrudGeneratorDict = array_merge($mergedCrudGeneratorDict, self::crudDictionaryGenerator($elemen));
        }

        foreach (self::ELEMEN_BASIC_CRUD_WITH_SETAKTIF_DICTIONARY as $elemen) {
            $mergedCrudGeneratorDict = array_merge($mergedCrudGeneratorDict, self::crudDictionaryGenerator($elemen, true));
        }

        return array_merge(
            $dictionary,
            $mergedCrudGeneratorDict
        );
    }

    private static function messageKeterangan($keyword)
    {
        return 'Pengguna melakukan ' . $keyword . ' pada aplikasi';
    }

    private static function crudDictionaryGenerator($keyword, $withSetAktif = false)
    {
        $listAksi = ['tambah', 'ubah', 'hapus'];
        if ($withSetAktif) {
            array_push($listAksi, 'set-aktif');
        }

        $log = [];
        foreach ($listAksi as $aksi) {
            $log[$aksi . '-' . $keyword] = [
                'nama' => str_replace('-', ' ', ucfirst($aksi)) . ' ' . str_replace('-', ' ', $keyword),
                'keterangan' => self::messageKeterangan(str_replace('-', ' ', $aksi) . ' ' . str_replace('-', ' ', $keyword))
            ];
        }

        return $log;
    }
}
