<?php

namespace VoyagerPolls\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use VoyagerPolls\Models\Poll;
use VoyagerPolls\Models\PollQuestion;
use VoyagerPolls\Models\PollAnswer;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PollsController extends \App\Http\Controllers\Controller
{
	//***************************************
	//               ____
	//              |  _ \
	//              | |_) |
	//              |  _ <
	//              | |_) |
	//              |____/
	//
	//      Browse the polls (B)READ
	//
	//****************************************

	public function browse(){
		$polls = Poll::paginate(1000);
		return view('polls::browse', compact('polls'));
	}


	//***************************************
	//                _____
	//               |  __ \
	//               | |__) |
	//               |  _  /
	//               | | \ \
	//               |_|  \_\
	//
	//      Read a specific poll B(R)EAD
	//
	//****************************************

	public function read($id){
		// $poll = Poll::findOrFail($id);

		$poll = $this->getPollData($id);

		// if($poll->type != 'one_answer') {
		//     $pollx = DB::table('voyager_polls')
		//         ->join( 'voyager_poll_questions', 'voyager_poll_questions.poll_id', '=', 'voyager_polls.id', '' )
		//         ->join( 'voyager_poll_answers', 'voyager_poll_answers.question_id', '=', 'voyager_poll_questions.id', '' )
		//         ->join( 'voyager_poll_answers_users', 'voyager_poll_answers_users.answer_id', '=', 'voyager_poll_answers.id', '' )
		//         ->join( 'users', 'users.id', '=', 'voyager_poll_answers_users.user_id', '' )

		//         ->select('users.name','users.login', 'voyager_poll_questions.question', 'voyager_poll_answers.id', 'voyager_poll_answers.correct')
		//         ->where(
		//              'voyager_polls.id', '=', $id,
		//         )->get();
		// // dd($pollx);
		// } else {
		//     $pollx = '';
		//     $answer_correct = '';
		//     $poll = $this->getPollData($id);
		// }

        $answer_correc ="";
        $pollx = "";

		if($poll->type == 'variant') {
            $answer_correct = DB::table('voyager_poll_answers')
                ->join( 'voyager_poll_questions', 'voyager_poll_answers.question_id', '=', 'voyager_poll_questions.id', 'left' )
                ->join( 'voyager_poll_answers_users', 'voyager_poll_answers_users.answer_id', '=', 'voyager_poll_answers.id', 'left' )
                ->join( 'users', 'users.id', '=', 'voyager_poll_answers_users.user_id', '' )

                ->select('voyager_poll_answers_users.id as answer_idx', 'voyager_poll_answers_users.created_at as created_at', 'users.name','users.login', 'voyager_poll_questions.question', 'voyager_poll_answers.id','voyager_poll_answers.answer', 'voyager_poll_answers.correct')
                ->where('voyager_poll_questions.poll_id', '=', $id )
                ->orderBy('voyager_poll_answers_users.id', 'ASC')
                ->get();

            } else {
                if($poll->type == 'one_answer') {
				$pollx = DB::table('voyager_poll_variants')
				           ->join( 'voyager_poll_questions', 'voyager_poll_variants.question_id', '=', 'voyager_poll_questions.id', 'left' )
				           ->join( 'users', 'voyager_poll_variants.user_id', '=', 'users.id', 'left' )

				           ->select('users.name','users.login', 'voyager_poll_questions.question', 'voyager_poll_variants.answer','voyager_poll_variants.created_at as created_at', 'voyager_poll_variants.id as answer_idx',)
				           ->where(
					           'voyager_poll_questions.poll_id', '=', $id,
                    )->get();
			} else {
				$pollx =  DB::table('voyager_poll_answers')
                            ->join( 'voyager_poll_questions', 'voyager_poll_answers.question_id', '=', 'voyager_poll_questions.id')
                            ->join( 'voyager_poll_answers_users', 'voyager_poll_answers_users.answer_id', '=', 'voyager_poll_answers.id')
                            ->join( 'users', 'users.id', '=', 'voyager_poll_answers_users.user_id', '' )

                            ->select('voyager_poll_answers_users.created_at as created_at', 'voyager_poll_answers_users.id as answer_idx','users.name','users.id','users.login', 'voyager_poll_questions.question', 'voyager_poll_answers.id','voyager_poll_answers.answer', 'voyager_poll_answers.correct')
                            ->where('voyager_poll_questions.poll_id', '=', $id )
                            ->orderBy('voyager_poll_answers_users.id', 'ASC')
                            ->get();
			}

			if(!isset($pollx)) {
				$pollx = '';
			}
			if(!isset($answer_correct)) {
				$answer_correct = '';
			}
			if(!isset($poll)) {
				$poll = $this->getPollData($id);
			}
		}
		return view('polls::read', compact('poll', 'pollx', 'answer_correct'));
	}


	//***************************************
	//                ______
	//               |  ____|
	//               | |__
	//               |  __|
	//               | |____
	//               |______|
	//
	//          Edit a poll BR(E)AD
	//
	//****************************************

	public function edit($id){
		$poll = $this->getPollData($id);
		return view('polls::edit-add', compact('poll'));
	}
	public function status($id){
		$poll = $this->getPollData($id);
		if($poll->status == 1) {
			$data = DB::table('voyager_polls')
			          ->where('id', $id)
			          ->update(['status' => 0]);
		} else {
			$data = DB::table('voyager_polls')
			          ->where('id', $id)
			          ->update(['status' => 1]);
		}
		return redirect()->route("voyager.polls")->with($data);
	}
	// BR(E)AD POST REQUEST
	public function edit_post(Request $request){
		return $this->updateOrCreatePoll($request, 'Опрос успешно обновлен!');
	}

	//***************************************
	//
	//                   /\
	//                  /  \
	//                 / /\ \
	//                / ____ \
	//               /_/    \_\
	//
	//
	//          Add a new poll BRE(A)D
	//
	//****************************************

	public function add(){
		return view('polls::edit-add');
	}

	// BRE(A)D POST REQUEST
	public function add_post(Request $request){
		// dd($request);
		return $this->updateOrCreatePoll($request, 'Опрос успешно добавлен!');
	}

	//***************************************
	//                _____
	//               |  __ \
	//               | |  | |
	//               | |  | |
	//               | |__| |
	//               |_____/
	//
	//          Delete a poll BREA(D)
	//
	//****************************************

	public function delete(Request $request){
		$id = $request->id;
		$data = Poll::destroy($id)
			? [
				'message'    => __('polls.successfully_poll_deleted'),
				'alert-type' => 'success',
			]
			: [
				'message'    => "Sorry it appears there was a problem deleting this poll",
				'alert-type' => 'error',
			];

		return redirect()->route("voyager.polls")->with($data);
	}


	protected function updateOrCreatePoll($request, $success_msg){
		try{
			// dd($request);
			$request->poll = json_decode(json_encode($request->poll), FALSE);

			if(!isset($request->poll->correct_answer)) {
				$request->poll->correct_answer = 0;
			}
//            if(empty($request->poll->correct)) {
//                $request->poll->correct_answer = 0;
//            }
			$poll = Poll::updateOrCreate(
				['id' => $request->poll->id],
				['name' => $request->poll->name, 'slug' => $request->poll->slug, 'status' => 0, 'type' => $request->poll->type, 'correct_answer' =>$request->poll->correct_answer]
			);

			// delete any questions that have been removed
			PollQuestion::where('poll_id', '=', $poll->id)->whereNotIn('id', Arr::pluck($request->poll->questions, 'id'))->delete();

			$question_order = 1;
			foreach($request->poll->questions as $questions){

				$question = PollQuestion::updateOrCreate(['id' => $questions->id], ['poll_id' => $poll->id, 'question' => $questions->question, 'order' => $question_order]);
				$question_order += 1;

				// delete any answers that have been removed

				PollAnswer::where('question_id', '=', $question->id)->whereNotIn('id', Arr::pluck($questions->answers, 'id'))->delete();

				$answer_order = 1;
				$i = 0;
				// dd($questions->answers);
				foreach($questions->answers as $answer){
					if(!empty($answer->answer)){

						if($answer->correct == $answer->id) {
							$correct = $answer->id;
						} elseif(!isset($answer->correct)) {
                            $correct = 0;
                        } else {
							$correct = 0;
						}

						$answer = PollAnswer::updateOrCreate(['id' => $answer->id], ['question_id' => $question->id, 'answer' => $answer->answer, 'order' => $answer_order, 'correct' => $correct]);
						unset($correct);
						$i++;
						$answer_order += 1;
					}
				}

			}

			$poll = $this->getPollData($poll->id);
			return response()->json( ['status' => 'success', 'message' => $success_msg, 'poll' => $poll] );
		} catch(Exception $e){
			return response()->json( ['status' => 'error', 'message' => $e->getMessage] );
		}
	}

	protected function getPollData($id){
		$poll = Poll::with('questions')->findOrFail($id);

//        $check_answers = DB::table( 'voyager_poll_questions' )
//           ->join( 'voyager_poll_answers', 'voyager_poll_answers.question_id', '=', 'voyager_poll_questions.id', 'left' )
//           ->join( 'voyager_poll_answers_users', 'voyager_poll_answers.id', '=', 'voyager_poll_answers_users.answer_id', 'left' )
//           ->select( 'voyager_poll_answers_users.answer_id' )
//           ->where(
//               [
//                   [ 'voyager_poll_questions.poll_id', '=', $poll->id ],
//                   [ 'voyager_poll_answers_users.user_id', '=', $user->id ]
//               ] )
//           ->first();

		foreach($poll->questions as $question){
			$question['user_id'] = 11;
			$question['answers'] = $question->answers;
		}
		return $poll;
	}

	public function json($slug){
		$poll = Poll::where('slug', '=', $slug)->firstOrFail();
		return response()->json($this->getPollData($poll->id));
	}

	public function api_vote(Request $request, $id){
		if($request->ajax()){
			$answer = PollAnswer::find($id);
			if(!isset($answer)){
				return response()->json( ['status' => 'error', 'message' => __('polls.answer_not_found')] );
			}
			$answer->votes += 1;
			$answer->save();

			$question = $answer->question;

			return response()->json( ['status' => 'success', 'message' => __('polls.successfully_voted'), 'question_id' => $question->id] );
		} else {
			return response()->json( ['status' => 'error', 'message' => 'Route cannot be called directly'] );
		}
	}
}
