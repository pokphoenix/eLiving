<div class="row">
							<div class="col-sm-4" id="task_job_type" >
								<h4 class="title"><i class="fa fa-circle-o"></i> @lang('work.jobtype')</h4> 
								<div class="task-job-type-show cp">
									test
								</div>
								<div class="task-job-type-edit none">
								<div class="row">
									<div class="col-sm-12">
										
										<select class="form-control" id="task_job_type_id">
											
											@foreach ($jobType as $category)
											<option value="{{ $category['id'] }}"> {{ $category['name'] }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<button class="btn bg-olive margin btn-save-edit-job-type" >@lang('main.btn_save')</button>
								<button class="btn btn-close-edit-job-type" ><i class="fa fa-close"></i></button>
								</div>
							</div>
						</div>