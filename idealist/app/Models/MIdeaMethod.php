<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MIdeaMethod extends Model
{
    use HasFactory;

    protected $table = "mIdeaMethod";

    public function getDetail($userId, $id) {

        $query = DB::table('mIdeaMethod AS mIM');
        $query->select([
            'mIM.name AS name'
        ]);
        $query->where('mIM.userId', $userId);
        $query->where('mIM.mIdeaId', $id);

        return $query->get()->toArray();
    }

    public function ins($userId, $id, $detailList) {

        $query = DB::table($this->table);
        $query->where('userId', $userId);
        $query->where('mIdeaId', $id);
        $query->delete();

        foreach ($detailList as $key => $item) {

            $query = DB::table($this->table);
            $query->insert([
                'userId' => $userId,
                'mIdeaId' => $id,
                'methodId' => $key,
                'name' => $item["name"],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
