<?php


namespace App\Helpers;


use App\Models\Konten\KontenModel;
use App\Models\UserManagement\UserHakAksesKontenModel;
use App\Models\UserManagement\UserModel;
use App\Scope\JenisKontenArtikelScope;
use App\Scope\JenisKontenCaseStudyScope;
use App\Scope\JenisKontenDokumenScope;
use App\Scope\JenisKontenForumScope;
use App\Scope\JenisKontenInformasiScope;
use App\Scope\JenisKontenVideoScope;

class KontenHelper
{
    const JENIS_KONTEN_VIDEO = 'video';
    const JENIS_KONTEN_ARTIKEL = 'artikel';
    const JENIS_KONTEN_CASE_STUDY = 'case-study';
    const JENIS_KONTEN_FORUM = 'forum';
    const JENIS_KONTEN_DOKUMEN = 'dokumen';
    const JENIS_KONTEN_INFORMASI = 'informasi';

    public static $paramsTaxonomi = null;
    public static $isDetail = 0;

    public static function generateQueryGetAll($jenisKonten, $penggunaAktif = null, $isWithRelation = true, $bypassUser = false, $customHidden = [])
    {
        self::setHiddenProperty($jenisKonten);
        $query = KontenModel::query();
        if (count($customHidden) > 0) {
            self::setCustomHiddenProperty($customHidden);
        }
        
        if(!is_null($jenisKonten)) {
            self::rejectScopeQueryWithout($jenisKonten, $query);
        } else {
            self::rejectAllScopeQuery($query);
        }

        $query->whereNull('dihapus_pada');
        $query->whereNull('revision_konten_id');
        if(!$bypassUser) {
            if(is_null($penggunaAktif)) {
                $query->where('is_publik_akses', '=', 1);
                $query->where('is_restricted', '=', 0);
            } else {
                /** @var UserModel $penggunaAktif */
                if($penggunaAktif->userDomain()->isRegisteredUser()) {
                    //Ambil konten yang dia punya hak akses
                    $hakAksesKonten = self::getUserHakAksesKonten($penggunaAktif->user_id);
                    $query->where(function($q) use ($penggunaAktif, $hakAksesKonten) {
                        $q->where('is_publik_akses', '=', 1);
                        $q->orWhere('dibuat_oleh_user_id', '=', $penggunaAktif->user_id);
                        $q->orWhereIn('konten_id', $hakAksesKonten);
                    });
                }

                $query->with(['getHakAksesKonten']);
            }
        }

        if($isWithRelation) {
            $query->with(['getKategoriKonten']);
        }
        $query->orderBy('dibuat_pada', 'desc');
        return $query;
    }

    public static function generateQueryPencarian(&$query, $keyword)
    {
        $query->where(function($q) use ($keyword){
            $q->whereRaw('lower(judul) like ?', ['%'.strtolower($keyword).'%']);
        });
    }

    public static function generateQuerySuggestions(&$query)
    {
        $query->select('judul', 'konten_id', 'jenis_konten');
    }

    public static function generateQueryFilterKategori(&$query, $kategoriKontenId)
    {
        $query->where('kategori_konten_id', '=', $kategoriKontenId);
    }

    public static function generateQueryFilterTaksonomi(&$query, $taksonomiId)
    {
        if(env('DB_CONNECTION') == 'sqlsrv') {
            $query->where('taksonomi_id', 'LIKE', '%'.$taksonomiId.'%');
        } else {
            $query->where('taksonomi_id', 'ILIKE', '%'.$taksonomiId.'%');
        }
    }

    public static function generateQueryRekomendasi(&$query, $kontenId)
    {
        $query->whereNotIn('konten_id', [$kontenId]);
    }

    public static function generateQueryFilterById(&$query, $kontenId)
    {
        if(is_array($kontenId)) {
            $query->whereIn('konten_id', $kontenId);
        } else {
            $query->where(['konten_id' => $kontenId]);
        }
    }

    public static function generateQueryPagination(&$query, $page, $limit)
    {
        $query->take($limit);
        $query->skip(($page - 1) * $limit);
    }

    public static function generateQueryMonitoringDateRange(&$query, $tglAwal, $tglAkhir)
    {
        $query->whereBetween('dibuat_pada', [$tglAwal, $tglAkhir]);
    }

    public static function generateQueryMonitoringTopKonten(&$query, $count = 10)
    {
        $query->limit($count);
    }

    public static function generateResponseTopKonten($query, $topKontenType = 'views')
    {
        $availableJenisKonten  = [
            self::JENIS_KONTEN_ARTIKEL,
            self::JENIS_KONTEN_CASE_STUDY,
            self::JENIS_KONTEN_DOKUMEN,
            self::JENIS_KONTEN_FORUM,
            self::JENIS_KONTEN_INFORMASI,
            self::JENIS_KONTEN_VIDEO
        ];

        $result = [];
        foreach ($availableJenisKonten as $valJenisKonten) {
            $tempData = [];
            $tempData['jenis_konten'] = $valJenisKonten;

            $currentQuery = clone($query);
            self::rejectScopeQueryWithout($valJenisKonten, $currentQuery);
            self::generateQueryMonitoringTopKonten($currentQuery);

            switch ($topKontenType) {
                case 'views':
                    $currentQuery->reorder('jumlah_akses', 'desc');
                    break;
                case 'commented':
                    $currentQuery->reorder('jumlah_komentar', 'desc');
                    break;
                case 'download':
                    $currentQuery->reorder('jumlah_download', 'desc');
                    break;
            }

            $tempData['top_konten'] = $currentQuery->get();
            $result[] = $tempData;
        }

        return $result;
    }

    public static function generateResponseJumlahPerJenisKonten($query, $jenisKonten = null)
    {
        $availableJenisKonten  = [
            self::JENIS_KONTEN_ARTIKEL,
            self::JENIS_KONTEN_CASE_STUDY,
            self::JENIS_KONTEN_DOKUMEN,
            self::JENIS_KONTEN_FORUM,
            self::JENIS_KONTEN_INFORMASI,
            self::JENIS_KONTEN_VIDEO
        ];

        $result = ['semua' => 0];
        $isFilterJenisKontenActive = (!is_null($jenisKonten));
        foreach ($availableJenisKonten as $iterasiJenisKonten) {
            if($isFilterJenisKontenActive) {
                if($jenisKonten != $iterasiJenisKonten) {
                    $result[$iterasiJenisKonten] = 0;
                    continue;
                }
            }

            $currentQuery = clone($query);
            self::rejectScopeQueryWithout($iterasiJenisKonten, $currentQuery);
            $result[$iterasiJenisKonten] = $currentQuery->count();
            $result['semua'] += $result[$iterasiJenisKonten];
        }

        return $result;
    }

    public static function rejectAllScopeQuery(&$query)
    {
        $query->withoutGlobalScopes();
    }

    public static function rejectScopeQueryWithout($except, &$query)
    {
        $arrayAvailableScope = [
            self::JENIS_KONTEN_ARTIKEL => JenisKontenArtikelScope::class,
            self::JENIS_KONTEN_CASE_STUDY => JenisKontenCaseStudyScope::class,
            self::JENIS_KONTEN_DOKUMEN => JenisKontenDokumenScope::class,
            self::JENIS_KONTEN_FORUM => JenisKontenForumScope::class,
            self::JENIS_KONTEN_INFORMASI => JenisKontenInformasiScope::class,
            self::JENIS_KONTEN_VIDEO => JenisKontenVideoScope::class
        ];

        if(strlen($except) > 0) {
            if(isset($arrayAvailableScope[$except])) {
                $unsetScope = $arrayAvailableScope[$except];
                $identifierUnsetScope = substr(strrchr($unsetScope, "\\"), 1);
                $identifierUnsetScope = strtolower($identifierUnsetScope);
                $identifierUnsetScope = str_replace("scope", "", $identifierUnsetScope);

                $query->withoutGlobalScopes();
                $query->withGlobalScope($identifierUnsetScope, new $unsetScope);
            }
        }
    }

    private static function getUserHakAksesKonten($userId)
    {
        $data = UserHakAksesKontenModel::query()
            ->where(['user_id' => $userId])
            ->get();

        return (count($data) == 0) ? [] : $data->pluck('konten_id')->toArray();
    }

    private static function setHiddenProperty($jenisKonten)
    {
        $hidden = [];
        switch (strtolower($jenisKonten)) {
            case self::JENIS_KONTEN_VIDEO:
                $hidden = ['foto', 'file_dokumen'];
                break;
            case self::JENIS_KONTEN_CASE_STUDY:
                $hidden = ['video', 'thumbnail'];
                break;
            case self::JENIS_KONTEN_INFORMASI:
                $hidden = ['video', 'foto'];
                break;
            case self::JENIS_KONTEN_ARTIKEL:
            case self::JENIS_KONTEN_FORUM:
                $hidden = ['video', 'file_dokumen'];
                break;
            case self::JENIS_KONTEN_DOKUMEN:
                $hidden = ['video'];
                break;
            default:
                break;
        }

        $previousHidden = KontenModel::getDefaultHidden();
        KontenModel::setStaticHidden(array_merge($hidden, (is_array($previousHidden) ? $previousHidden : [])));
    }

    private static function setCustomHiddenProperty($hidden)
    {
        $previousHidden = KontenModel::getDefaultHidden();
        KontenModel::setStaticHidden(array_merge($hidden, (is_array($previousHidden) ? $previousHidden : [])));
    }
}
