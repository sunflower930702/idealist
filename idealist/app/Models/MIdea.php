<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MIdea extends Model
{
    use HasFactory;

    protected $table = "mIdea";

    public function getDetail($id, $deep = 1, $onlyOwn = false) {

        $detailModel = new MIdeaDetail();
        $propertyModel = new MIdeaProperty();
        $methodModel = new MIdeaMethod();

        $result = [];

        // 対象データを取得
        $query = DB::table($this->table);
        $query->where('id', $id);
        $result['target'] = $query->first();

        if ($result['target'] == null) {
            return null;
        }

        // 対象が継承しているデータを取得
        $result['parent'] = $this->getParent($result['target']->extendsId, $onlyOwn);

        // 対象を継承しているデータを取得
        $result['child'] = $this->getChild($id, $deep);

        // 対象の保有するプロパティを取得
        $result['properties'] = $propertyModel->getDetail($id);
        if ($onlyOwn == false) {

            $targetParent = $result['parent'];
            while($targetParent != null && $targetParent["info"] != null) {

                $tmpProperies = $propertyModel->getDetail($targetParent["info"]->id);
                foreach ($tmpProperies as $item) {
                    $result['properties'][] = $item;
                }

                $targetParent = $targetParent["parent"];
            }
        }

        // 対象の保有するメソッド取得
        $result['methods'] = $methodModel->getDetail($id);
        if ($onlyOwn == false) {

            $targetParent = $result['parent'];
            while($targetParent != null && $targetParent["info"] != null) {

                $tmpMethods = $methodModel->getDetail($targetParent["info"]->id);
                foreach ($tmpMethods as $item) {
                    $result['methods'][] = $item;
                }

                $targetParent = $targetParent["parent"];
            }
        }

        // 対象の具体例（詳細）を取得
        $query = DB::table('mIdeaDetail');
        $query->where('mIdeaId', $id);
        $result['details'] = $query->get()->toArray();

        return $result;
    }

    public function getParent($id) {

        $result = [];

        $query = DB::table($this->table . ' AS mySelf');
        $query->select([
            'mySelf.id AS id',
            'mySelf.extendsId AS extendsId',
            'mySelf.name AS name',
        ]);
        $query->leftJoin($this->table . ' AS parent', function($join) {
            $join->on('mySelf.extendsId', 'parent.id');
        });
        $query->where('mySelf.id', $id);

        $result['info'] = $query->first();
        $result['parent'] = null;

        if ($result['info'] == null) {
            return $result;
        }

        if ($result['info']->extendsId != null) {
            $result['parent'] = $this->getParent($result['info']->extendsId);
        }

        return $result;
    }

    public function getChild($id, $deep) {

        if ($deep == 0) {
            return [];
        }

        $result = [];
        $tmpResult = [];

        // 自身と同じものを取得
        $query = DB::table($this->table);
        $query->select([
            'id AS id',
            'name AS name',
            'extendsId AS extendsId',
        ]);
        $query->selectRaw($deep . " AS deep");
        $query->where('extendsId', $id);
        $result = $query->get()->toArray();

        foreach ($result as $item) {
            foreach ($this->getChild($item->id, $deep - 1) as $item2) {
                $tmpResult[] = $item2;
            }
        }

        foreach ($tmpResult as $item) {
            $result[] = $item;
        }

        return $result;
    }

    public function getList($cond) {

        // 対象データを取得
        $query = DB::table($this->table . ' AS main');
        $query->select([
            'main.id AS id',
            'main.name AS name',
            'main.contents AS contents',
            'parent.id AS extendsId',
            'parent.name AS extendsName',
        ]);

        $query->leftJoin($this->table . ' AS parent', function ($join) {
            $join->on('parent.id', 'main.extendsId');
        });

        if ($cond['name'] != "") {
            $query->whereRaw("main.name like '%" . $cond['name'] . "%'");
        }

        if ($cond['contents'] != "") {
            $query->whereRaw("main.contents like '%" . $cond['contents'] . "%'");
        }

        if ($cond['parentName'] != "") {
            $query->whereRaw("parent.name like '%" . $cond['parentName'] . "%'");
        }

        if ($cond['childName'] != "") {

            $subQuery = DB::table($this->table);
            $subQuery->select('extendsId');
            $subQuery->whereRaw("name like '%" . $cond['childName'] . "%'");
            $subQuery->groupBy("extendsId");

            $query->joinSub($subQuery, 'child', function ($join) {
                $join->on('child.extendsId', 'main.id');
            });
        }

        $query->limit(100000);
        return $query->get()->toArray();
    }

    public function ins($dataSet) {

        $detailModel = new MIdeaDetail();
        $propertyModel = new MIdeaProperty();
        $methodModel = new MIdeaMethod();

        DB::beginTransaction();

        try {

            $id = $this->getNextId();

            $query = DB::table($this->table);
            $query->insert([
                'id' => $id,
                'extendsId' => $dataSet["extendsId"],
                'name' => $dataSet["name"],
                'contents' => $dataSet["contents"],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $detailModel->ins($id, $dataSet["detail"]);
            $propertyModel->ins($id, $dataSet["property"]);
            $methodModel->ins($id, $dataSet["method"]);
        } catch (Exception $e) {
            
            DB::rollBack();
            return false;
        }

        DB::commit();
        return true;
    }

    public function upd($dataSet) {

        $detailModel = new MIdeaDetail();
        $propertyModel = new MIdeaProperty();
        $methodModel = new MIdeaMethod();

        DB::beginTransaction();

        try {

            $query = DB::table($this->table);
            $query->where("id", $dataSet["id"]);
            $query->update([
                'name' => $dataSet["name"],
                'extendsId' => $dataSet["extendsId"],
                'contents' => $dataSet["contents"],
                'updated_at' => now()
            ]);

            $detailModel->ins( $dataSet["id"], $dataSet["detail"]);
            $propertyModel->ins($dataSet["id"], $dataSet["property"]);
            $methodModel->ins($dataSet["id"], $dataSet["method"]);
        } catch (Exception $e) {
            
            DB::rollBack();
            return false;
        }

        DB::commit();
        return true;
    }

    /**
     * 参照の循環が発生するかをチェックする
     *  => $idの子供に$extendsIdがいてはいけない
     *     再帰処理で$idを少しずつ下げて探索を行う
     * 
     */
    public function checkLoop($id, $extendsId) {

        // 自分を継承しているもの
        $query = DB::table($this->table);
        $query->where('id', $extendsId);
        $query->where('extendsId', $id);
        $result = $query->get()->toArray();

        if (count($result) > 0) {
            return false;
        }

        // 子を見に行く
        $query = DB::table($this->table);
        $query->where('extendsId', $id);
        $result = $query->get()->toArray();
        foreach ($result as $item) {
            if ($this->checkLoop($item->id, $extendsId) == false) {
                return false;
            }
        }

        return true;
    }

    // ==============================================
    // Privateメソッド
    // ==============================================

    /**
     * 次のIDを取得する
     *  -> 最大値+1
     * 
     */
    private function getNextId() {

        $query = DB::table($this->table);
        return $query->max('id') + 1;
    }
}
