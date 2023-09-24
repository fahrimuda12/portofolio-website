<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Main\MainController;
use App\Repositories\Main\ProjectRepository;
use Illuminate\Http\Request;

class ProjectController extends MainController
{
    public function index(Request $request)
    {
        $params = [
            'cari' => $request->input('cari'),
            'limit' => $request->input('limit'),
            'page' => $request->input('page'),
            'attribut_id' => $request->input('attribut_id')
        ];
        $params = array_merge($this->getDefaultParameter($request), $params);

        $result = (new ProjectRepository())->aksiGetSemua($params, false);
        if($result['code'] == 200) {
            return view('project.project', [
               'data' => $result['data']
            ]);
        }else{
            dd($result);
        }
        // return response()->json($result, $result['code']);
    }

    public function detail(Request $request, $id)
    {
        $params = [
            'project_id' => $id
        ];
        $params = array_merge($this->getDefaultParameter($request), $params);

        $result = (new ProjectRepository())->aksiGetDetail($params);
        if($result['code'] == 200) {
            return view('project.project-detail', [
               'data' => $result['data']
            ]);
        }else{
            dd($result);
        }
    }
}
