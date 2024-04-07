<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MIdeaDetail extends Model
{
    use HasFactory;
    
    protected $table = "mIdeaDetail";

    public function ins($id, $detailList) {

        $query = DB::table($this->table);
        $query->where('mIdeaId', $id);
        $query->delete();

        foreach ($detailList as $key => $item) {

            $query = DB::table($this->table);
            $query->insert([
                'id' => $key,
                'mIdeaId' => $id,
                'name' => $item["name"],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
