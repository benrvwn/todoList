<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachement extends Model
{

    use HasFactory;
    protected $fillable = [
        'task_id', 'file_name'
    ];

    public function attach($data){
        return $this->create($data);
    }
    public function removeAttach($id){
        $data = $this->find($id);
        return  $data->delete();
    }
    public function removeAllAttach($id){
        $data = $this->where('task_id', $id)->delete();
        return $data;
    }
}
