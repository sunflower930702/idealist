<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MIdeaDetail extends Model
{
    use HasFactory;
    
    protected $table = "mIdeaDetail";

    public function ins($userId, $id, $detailList) {

        $query = DB::table($this->table);
        $query->where('userId', $userId);
        $query->where('mIdeaId', $id);
        $query->delete();

        foreach ($detailList as $key => $item) {

            $query = DB::table($this->table);
            $query->insert([
                'userId' => $userId,
                'id' => $key,
                'mIdeaId' => $id,
                'name' => $item["name"],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
