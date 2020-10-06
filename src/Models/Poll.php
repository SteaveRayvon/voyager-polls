<?php

namespace VoyagerPolls\Models;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
	protected $table = 'voyager_polls';
    protected $fillable = ['name', 'slug', 'status', 'type', 'correct_answer'];

    public function questions(){
    	return $this->hasMany('VoyagerPolls\Models\PollQuestion', 'poll_id')->orderBy('order', 'ASC');
    }
}
