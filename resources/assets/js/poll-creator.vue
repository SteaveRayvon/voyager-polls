<template>
	<div id="vueify">
		<h1 class="page-title">
	    	<i class="voyager-bar-chart"></i> New Poll
	    </h1>

		<div id="polls">

			<div class="container-fluid">

				<div class="panel panel-bordered">

					<div class="panel-heading">
						<h3 class="panel-title">Add Your New Poll Below</h3>
					</div>

					<div class="panel-body">

						<div class="col-md-6" id="poll_name">
							<input type="text" name="poll_name" placeholder="Give this poll a name" v-model="poll.name" class="form-control">
						</div>

						<div class="col-md-6" id="poll_slug">
							<input type="text" name="poll_slug" placeholder="Poll Slug (Unique Identifier)" v-model="poll.slug" class="form-control">
						</div>


						<div class="col-md-6">

							<draggable v-model="poll.questions">
		    					<transition-group>
									<poll-question v-for="(question, index) in poll.questions" :question="question" :index="index" :key="index" v-on:delete-question="deleteQuestion(index)"></poll-question>
                                </transition-group>

							</draggable>
							<button class="btn btn-primary btn-question" @click="createNewQuestion"><i class="voyager-question"></i> Another Question +</button>
	                    </div>

                        <div class="col-md-6" id="poll_type">
                            <label>Выбор</label>
                            <select name="poll_type" v-model="poll.type" class="form-control">
                                <option :selected="[poll.type == 'radio' ? 'selected' : '']" value="radio">Одиночный</option>
                                <option :selected="[poll.type == 'checkbox' ? 'selected' : '']" value="checkbox">Множественный</option>
                                <option :selected="[poll.type == 'variant' ? 'selected' : '']" value="variant">Одиночный с правильным вариантом</option>
                                <option :selected="[poll.type == 'one_answer' ? 'selected' : '']" value="one_answer">Со своим вариантом ответа</option>
                            </select>
							<br><br>
							<div id="correct_answer" class="d-none">
							<label>Количество первых правильных</label>
                            <input name="correct_answer" v-model="poll.correct_answer" class="form-control" type="text" value="1">
							</div>
                        </div>

	                    <div class="col-md-6">
	                    	<label for="preview" class="d-none">Preview:</label>
	                    	<poll :poll="poll"></poll>
	                    </div>

					</div>
					<div class="panel-footer">
						<button class="btn btn-primary pull-right" id="create" @click="savePoll" v-html="saveCopy"></button>
						<div style="clear:both"></div>
					</div>
				</div>

			</div>
		</div>

	</div>
</template>

<style type="text/css">
		#poll_name, #poll_slug{
			padding-bottom:30px;
			margin-bottom:30px;
			border-bottom:1px solid #f1f1f1;
		}

		#poll_name input, #poll_slug input{
			font-size: 20px;
	    	padding: 30px 20px;
		}

		.btn-question{
			margin-top:20px;
			display:block;
			width:auto;
			margin:20px auto;
		}

		.voyager-refresh{
			-webkit-animation: spin 0.6s infinite linear;
		    -moz-animation: spin 0.6s infinite linear;
		    animation: spin 0.6s infinite linear;
			display: inline-block;
		    width: 18px;
		    height: auto;
		    transform-origin: 8px 9px;
		    position: relative;
		    top: 2px;
		}

		.btn-question i{
			position:relative;
			top:2px;
		}
</style>

<script>
	var draggable = require('vuedraggable');
	var axios = require('axios');
	var slugify = require('slugify');

    $(document).ready( function () {
        var x = $('select[name="poll_type"]').val();
        if(x == 'variant') {
            $('.answer .radio, #correct_answer').removeClass('d-none');
        } else {
            $('.answer .radio, #correct_answer').addClass('d-none');
        }
        if(x == 'one_answer') {
            $('.answer_show').addClass('d-none');
        }
    });

	$('body').on('change mouseenter', 'select[name="poll_type"]', function () {
	    var x = $(this).val();
	    if(x == 'variant') {
            $('.answer .radio, #correct_answer').removeClass('d-none');
        } else {
            $('.answer .radio, #correct_answer').addClass('d-none');
        }
        if(x == 'one_answer') {
            $('.answer_show').addClass('d-none');
        } else {
            $('.answer_show').removeClass('d-none');
        }
    });

	module.exports = {

		props: ['url', 'edit_poll'],

		data: function(){
			return {
				newQuestionCopy : 'Create New Question',
				newQuestionLoadingCopy: '<span class="voyager-refresh"></span> Saving New Poll',
				updateQuestionCopy : 'Update Question',
				saveCopy: '',
				post_url: '',
				poll: {
					id: '',
					name:'',
                    type:'',
					slug: '',
					correct_answer: '',
					questions:[]
				}
			}

		},
		watch: {
			'poll.name': function(){
				this.poll.slug = slugify(this.poll.name).toLowerCase();
			}
		},
		methods:{
			newQuestion: function(){
				return {
					id:'',
					question: '',
					answers: [
						{ 'id': '', 'answer': '', 'correct': '0' },
						{ 'id': '', 'answer': '', 'correct': '0' },
						{ 'id': '', 'answer': '', 'correct': '0' }
					],
					correct: 0,
				}
			},
			createNewQuestion: function(){
				this.poll.questions.push(this.newQuestion());
			},
			savePoll: function(){
				this.saveCopy = this.newQuestionLoadingCopy;


				var xpoll = this.poll.questions[0].answers
				var map = this.poll.questions[0].answers;

				$('input[name="correct"]').each(function(index) {
					if($(this).is(':checked')) {
						var arn = index;
						//alert(arn);
						return implode_to_poll(arn,map,xpoll);
					}
				});

				function implode_to_poll(arn,map,xpoll) {
					var curr = $('input[name="correct"]:checked').val();
					var count = [$('input[name="correct"]').length - 1];
					var pk = [arn];

					count.forEach(function(value) {
						map[value];
					});

					xpoll.forEach(function(entry) {
						if (map[pk]) {
							entry.correct = curr;
						} else {
							entry.correct = 0;
						}
					});
				}

				var that = this;
				axios.post(this.post_url, { poll: this.poll })
					.then(function (response) {
						if(response.data.status == "success"){
							toastr.success(response.data.message);
							that.saveCopy = that.updateQuestionCopy;
							that.newQuestionCopy = that.updateQuestionCopy;
							that.post_url = that.url + '/admin/polls/edit';
							that.poll = response.data.poll;
							//console.log(response.data.poll);
							history.replaceState(null, null, '/admin/polls/' + that.poll.id + '/edit');
						} else {
							toastr.error(response.data.message);
							that.saveCopy = that.newQuestionCopy;
						}

				  	})
					.catch(function (error) {
						that.saveCopy = that.newQuestionCopy;
						toastr.error(error.message);
					});
			},
			deleteQuestion: function(index){
				this.poll.questions.splice(index, 1);
			}
		},
		created: function(){
			if(this.edit_poll){
				this.poll = JSON.parse(this.edit_poll);
				this.newQuestionCopy = this.updateQuestionCopy;
				this.post_url = this.url + '/admin/polls/edit';
			} else {
				this.createNewQuestion();
				this.post_url = this.url + '/admin/polls/add';
			}

			this.saveCopy = this.newQuestionCopy;
		},
		components: {
			draggable:draggable,
		},
	};
</script>
