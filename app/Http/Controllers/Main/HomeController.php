<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Main\MainController;
use App\Repositories\Main\ProjectRepository;
use Illuminate\Http\Request;

class HomeController extends MainController
{
    public function index(Request $request)
    {
        $params = [
            'limit' => 3,
            'page' => 1,
        ];
        $params = array_merge($this->getDefaultParameter($request), $params);

        $project = (new ProjectRepository())->aksiGetSemua($params);

        if($project['code'] != 200) {
            dd($project);
        }
        return view('landing', [
            'project' => $project['data']
        ]);


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
