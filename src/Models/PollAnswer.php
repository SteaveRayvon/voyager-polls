<?php

namespace VoyagerPolls\Models;

use Illuminate\Database\Eloquent\Model;

class PollAnswer extends Model
{
    protected $table = 'voyager_poll_answers';
    protected $fillable = ['question_id', 'answer', 'order', 'correct'];

    public function question(){
    	return $this->belongsTo('VoyagerPolls\Models\PollQuestion', 'question_id');
    }

}
