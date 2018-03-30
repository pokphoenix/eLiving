<div class="row">
							<div class="col-sm-4" id="task_area_type" >
								<h4 class="title"><i class="fa fa-circle-o"></i> @lang('work.area_type')</h4> 
								<div class="task-area-type-show cp">
									test
								</div>
								<div class="task-area-type-edit none">
								<div class="row">
									<div class="col-sm-12">
										<select class="form-control" id="task_area_type_id">
											
											@foreach ($areaType as $category)
											<option value="{{ $category['id'] }}"> {{ $category['name'] }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<button class="btn bg-olive margin btn-save-edit-area-type" >@lang('main.btn_save')</button>
								<button class="btn btn-close-edit-area-type" ><i class="fa fa-close"></i></button>
								</div>
							</div>
						</div>