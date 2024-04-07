<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class mIdea extends Model
{
    use HasFactory;

    protected $table = "mIdea";

    public function getDetail($id, $deep = 1) {

        $result = [];

        // 対象データを取得
        $query = DB::table($this->table);
        $query->where('id', $id);
        $result['target'] = $query->first();

        if ($result['target'] == null) {
            return null;
        }

        // 対象の保有するプロパティを取得
        $query = DB::table('mIdeaProperty AS mIP');
        $query->select([
            'mI.name AS name',
            'mIP.value AS value'
        ]);
        $query->leftJoin('mIdea AS mI', function($join) {
            $join->on('mIP.propetyId', 'mI.id');
        });
        $query->where('mIP.mIdeaId', $id);
        $result['properties'] = $query->get()->toArray();

        // 対象の保有するメソッド取得
        $query = DB::table('mIdeaMethod AS mIM');
        $query->select([
            'mM.name AS name'
        ]);
        $query->leftJoin('mMethod AS mM', function($join) {
            $join->on('mIM.methodId', 'mM.id');
        });
        $query->where('mIM.mIdeaId', $id);
        $result['methods'] = $query->get()->toArray();

        // 対象が継承しているデータを取得
        $result['parent'] = $this->getParent($result['target']->extendsId);

        // 対象を継承しているデータを取得
        $result['child'] = $this->getChild($id, $deep);

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
}
