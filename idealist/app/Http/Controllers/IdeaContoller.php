<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MIdea;
use App\Models\MIdeaMethod;
use App\Models\MIdeaProperty;
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

        $userId = $json["query"]["userId"];
        $id = $json["query"]["id"];
        $deep = $json["query"]["deep"];
        $onlyOwn = $json["query"]["onlyOwn"];

        if ($deep == null) {
            $deep = 1;
        }

        $ideaModel = new MIdea();
        $modelResult = $ideaModel->getDetail($userId, $id, $deep, $onlyOwn );

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
        $modelResult = $ideaModel->getList($json["query"]["userId"], $cond);

        return response()->json(['idea' => $modelResult]);
    }
    
    /**
     * 更新API
     */
    public function update(Request $request) {

        $json = json_decode($request->getContent(), true);
        $dataSet = $json["dataSet"];

        $ideaModel = new MIdea();
        if ($dataSet["id"] == 0) {
            $modelResult = $ideaModel->ins($dataSet);
        } else {

            if ($dataSet["id"] == $dataSet["extendsId"]) {
                return response()->json(['result' => false, 'msg' => "継承元に同じものは選択できません。"]);
            }

            if ($ideaModel->checkLoop($dataSet["userId"], $dataSet["id"], $dataSet["extendsId"]) == false) {
                return response()->json(['result' => false, 'msg' => "循環が発生します。"]);
            }

            $modelResult = $ideaModel->upd($dataSet);
        }

        return response()->json(['result' => $modelResult, 'msg' => ""]);
    }
}
