<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class WordWhiteList extends Model
{
    protected $table = 'master_word_white_list';
    public $timestamps = false;
    protected $fillable = ['text'];
}
