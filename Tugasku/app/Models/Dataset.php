<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
   protected $fillable = 
   ['name',
    'file_path', 
    'total_rows',
     'total_columns',
     'analysis_json', 
    'status' 
    ];

public function getAnalysisAttribute()
    {
        return $this->analysis_json ? json_decode($this->analysis_json, true) : null;
    }
}


