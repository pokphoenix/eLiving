<div class="row">
							<div class="col-sm-4" id="task_pioritize" >
								<h4 class="title"><i class="fa fa-circle-o"></i> @lang('work.prioritize')</h4> 
								<div class="task-prioritize-show cp">
									test
								</div>
								<div class="task-prioritize-edit none">
								<div class="row">
									<div class="col-sm-12">
										
										<select class="form-control" id="task_prioritize_id">
										
											@foreach ($prioritize as $category)
											<option value="{{ $category['id'] }}"> {{ $category['name'] }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="row row-task-pioritized-desc none">
									<div class="col-sm-12">
										<label>&nbsp;</label>
										<input type="text" class="form-control" id="task_pioritized_desc" name="task_pioritized_desc" placeholder="@lang('work.prioritize_desc')" >
									</div>
								</div>
								<button class="btn bg-olive margin btn-save-edit-pioritize" >@lang('main.btn_save')</button>
								<button class="btn btn-close-edit-pioritize" ><i class="fa fa-close"></i></button>
								</div>
							</div>
						</div>