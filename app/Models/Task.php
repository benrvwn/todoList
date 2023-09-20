<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'task_name', 'desc', 'status'
    ];

    public function insert($data){
        return $this->create($data);
    }
    public function remove($id){
        $data = $this->find($id);
        return $data->delete();
    }

    public function attachments()
    {
        return $this->hasMany(Attachement::class, 'task_id');
    }

}
