<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MIdea;
use App\Models\MIdeaMethod;
use App\Models\MIdeaPropety;
use App\Models\mMethod;

class IdeaContoller extends Controller
{

    /**
     * 配布画面
     * 
     */
    public function index(Request $request) {

        return view('index');
    }

    /**
     * 詳細API
     * 
     */
    public function detail(Request $request) {

        $json = json_decode($request->getContent(), true);

        $id = $json["query"]["id"];
        $deep = $json["query"]["deep"];

        if ($deep == null) {
            $deep = 1;
        }

        $ideaModel = new MIdea();
        $modelResult = $ideaModel->getDetail($id, $deep);

        return response()->json(['dataSet' => $modelResult]);
    }

    /**
     * 一覧API
     */
    public function list(Request $request) {

        $json = json_decode($request->getContent(), true);

        $cond = [
            'name' => $json["query"]["name"],
            'contents' => $json["query"]["contents"],
            'parentName' => $json["query"]["parentName"],
            'childName' => $json["query"]["childName"],
        ];

        $ideaModel = new MIdea();
        $modelResult = $ideaModel->getList($cond);

        return response()->json(['idea' => $modelResult]);
    }
}
