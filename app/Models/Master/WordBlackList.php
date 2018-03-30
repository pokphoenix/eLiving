<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class WordBlackList extends Model
{
    protected $table = 'master_word_black_list';
    public $timestamps = false;
    protected $fillable = ['text'];
}
