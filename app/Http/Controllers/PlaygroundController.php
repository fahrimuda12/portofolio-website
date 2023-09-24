<?php

namespace App\Http\Controllers;

use App\Models\Main\SummaryEntitasDataModel;
use Illuminate\Support\Facades\File;

class PlaygroundController extends Controller
{
    public function t3l37773322hddss()
    {
        $data = SummaryEntitasDataModel::query()
            ->get();

        foreach ($data as $num => $item) {
            $dir = 'summary/';
            if(!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            $filename = $item->table_penyimpanan.'-'.$item->entitas_data_id.'.csv';
            $storeFilename = $dir.$filename;
            File::put($storeFilename, $item->list_id_data_ditambahkan);

            $currentRow = SummaryEntitasDataModel::query()
                ->find($item->summary_entitas_data_id);

            $currentRow->list_id_data_ditambahkan = $filename;
            $currentRow->save();
        }

        echo "Done Bosque";
    }
}
