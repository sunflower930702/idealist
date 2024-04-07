<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MIdeaProperty extends Model
{
    use HasFactory;

    protected $table = "mIdeaProperty";

    public function getDetail($id) {

        $query = DB::table('mIdeaProperty AS mIP');
        $query->select([
            'mIP.propertyId AS id',
            'mI.name AS type',
            'mIP.name AS name',
            'mIP.value AS value'
        ]);
        $query->leftJoin('mIdea AS mI', function($join) {
            $join->on('mIP.propertyId', 'mI.id');
        });
        $query->where('mIP.mIdeaId', $id);

        return $query->get()->toArray();
    }

    public function ins($id, $detailList) {

        $query = DB::table($this->table);
        $query->where('mIdeaId', $id);
        $query->delete();

        foreach ($detailList as $item) {

            $query = DB::table($this->table);
            $query->insert([
                'mIdeaId' => $id,
                'propertyId' => $item["id"],
                'name' => $item["name"],
                'value' => $item["value"],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
