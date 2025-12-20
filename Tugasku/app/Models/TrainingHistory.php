<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_type',
        'epochs',
        'accuracy',
        'loss',
        'plot_file',
        'model_file',
    ];
}