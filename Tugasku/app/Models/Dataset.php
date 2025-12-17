<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
   protected $fillable = 
   ['name',
    'file_path', 
    'total_rows',
     'total_columns'
    ];
}
