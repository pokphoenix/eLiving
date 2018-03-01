@extends('front.app')

@section('content-wrapper')

	 <style type="text/css">
	 	.cr{color:#3c8dbc;}
	 </style>
  
  <!-- Start main content -->
    
  <main role="main">

	

    <!-- Start Contact -->
    <section id="mu-contact">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="mu-contact-area">

              <div class="text-center">
                <h2 class="mu-heading-title">เลือก คอนโดที่ต้องการ</h2>
                <span class="mu-header-dot"></span>
                @include('layouts.error')
              </div>

            	
              <div class="col-sm-offset-1 col-sm-10">
				<div class="rows">
					<div class="col-sm-6">
						
					</div>
					<div class="col-sm-6 text-center">
						<h4>สร้าง บัญชีคอนโด</h4>
						 <button type="submit" class="mu-send-msg-btn"><span>Create</span></button>
					</div>
				</div>
                

                <form id="signup-form" method="post" action="{{ route('signup') }}" class="mu-contact-form">
                	 {{ csrf_field() }}
                  	<div class="row">
                  		<div class="col-sm-offset-2 col-sm-4">
                  			
			                	<div class="form-group">             
			                  		<input type="text" class="form-control" placeholder="ชื่อ" id="first_name" name="first_name" value="{{old('first_name')}}" >
			                  	</div>
			                  	<div class="form-group">                
			                  		<input type="text" class="form-control" placeholder="สกุล" id="last_name" name="last_name" value="{{old('last_name')}}">
			                  	</div>
			                  	<div class="form-group">                
			                  		<input type="email" class="form-control" placeholder="อีเมล์" id="email" name="email" value="{{old('email')}}">
			                  	</div>
			                
                  		</div>
                  		<div class="col-sm-4">
                  			
                  				<div class="form-group">                
			                  		<input type="text" class="form-control" placeholder="username" id="username" name="username" value="{{old('username')}}">
			                  	</div>
			                  	<div class="form-group">                
			                  		<input type="password" class="form-control" placeholder="password" id="password" name="password" value="{{old('password')}}">
			                  	</div>
			                  	<div class="form-group">                
			                  		<input type="password" class="form-control" placeholder="confirm password" id="password_confirmation" name="password_confirmation" value="{{old('password_confirmation')}}">
			                  	</div>   
			                
                  		</div>
                  	</div>
                  	<div class="row">
                  		<div class="col-sm-offset-2 col-sm-8">
                  			<div class="form-check">
							    <label class="form-check-label">
							      <input type="checkbox" id="agree" name="agree" class="form-check-input" @if(old('agree'))  checked="" @endif >
							      I agree to the Terms of Service and Privacy Policy.

Give us a call and we'll help you get your account activated

This is where you and your employees will log in:

https://yourcompany.bamboohr.com
							    </label>
							</div>	
			                  	  
								
								
							<div  >
								 <button type="submit" class="mu-send-msg-btn"><span>SUBMIT</span></button>
							</div>
			                  	<!-- <div class="form-group">                
			                  		<input type="text" class="form-control" placeholder="ชื่อโครงการ" id="residence_name" name="residence_name"  value="{{old('residence_name')}}">
			                  	</div>  -->    
		                </div>
		                       
							<!-- <div class="row">
			                	<div class="form-group col-sm-offset-4 col-sm-4">                
			                  		<input type="text" class="form-control" placeholder="ชื่อบริษัท" id="company_name" name="company_name" value="{{old('company_name')}}">
			                  	</div>	  
		                  	</div>  -->
                  		
                  		
                  	</div>
					<div class="row" >
						<div class="col-sm-offset-2 col-sm-8" >
							<div class="row" style="margin:10px 0;color: #ccc;border-top: 1px solid #CCC;">
								
							</div>
							<div class="row">
								<div class="col-sm-offset-2 col-sm-8">
									<a class="btn btn-primary btn-block btn-social btn-facebook">
							            <i class="fa fa-facebook"></i> Sign in with Facebook
							        </a>
								</div>
							</div>
							
						</div>
			                	<!-- <div class="form-group col-sm-4">                
			                  		<input type="text" class="form-control" placeholder="ตำแหน่งงาน" id="job_title" name="job_title" value="{{old('last_name')}}">
			                  	</div>
			                  	<div class="form-group col-sm-4">                
			                  		<input type="text" class="form-control" placeholder="โทรศัพท์" id="tel" name="tel" value="{{old('tel')}}">
			                  	</div> -->
			                  
			                  	
		             </div> 

                  	<!-- <div class="row" style="margin-top: 20px; border-bottom: 1px solid #ccc;">
                  		<div class="col-sm-2 text-right" style="color:#ff871c;">
                  			<h6>ข้อมูลชื่อบัญชี</h6>
                  		</div>
                  		<div class="col-sm-8">
                  			<div class="row">
			                	<div class="form-group col-sm-4">  
			                		<label class="col-form-label">Number of Unit</label>           
			                  		<select id="unit" name="unit" class="form-control">
			                  			<option value=""></option>
			                  			<option value="1" @if( old('unit') &&  old('unit'))==1 ) selected="selected" @endif >01-10 unit</option>
			                  			<option value="1">11-15 unit</option>
			                  			<option value="1">16-25 unit</option>
			                  			<option value="1">26-50 unit</option>
			                  			<option value="1">51-75 unit</option>
			                  			<option value="1">76-100 unit</option>
			                  			<option value="1">101-150 unit</option>
			                  			<option value="1">151-200 unit</option>
			                  			<option value="1">201-250 unit</option>
			                  			<option value="1">251-300 unit</option>
			                  			<option value="1">301-400 unit</option>
			                  			<option value="1">401-500 unit</option>
			                  			<option value="1">501-600 unit</option>
			                  			<option value="1">601-700 unit</option>
			                  			<option value="1">701-800 unit</option>
			                  			<option value="1">801-900 unit</option>
			                  			<option value="1">901-1000 unit</option>
			                  			<option value="1">1001-1100 unit</option>
			                  			<option value="1">1101-1200 unit</option>
			                  			<option value="1">1201-1300 unit</option>
			                  			<option value="1">1301-1400 unit</option>
			                  			<option value="1">1401-1500 unit</option>
			                  			<option value="1">1501-1600 unit</option>
			                  			<option value="1">1601-1700 unit</option>
			                  		</select>
			                  	</div>

			                  	<div class="form-group col-sm-8" style="position:relative;">
			                  		<label class="col-form-label">Direct url</label>
							      <input type="text" class="form-control" id="domain" name="domain" style="text-align: right; padding-right: 80px;"
									value="{{old('domain')}}"
							      >
							      <span style="position:absolute;right:30px;top:37px;color:#ff871c;	">.rm.com</span>
							    </div> 
			                  	
		                  	</div>
		                    <div class="row">
			                	 
		                  	</div>     
							
                  		</div>
                  		<div class="col-sm-2"></div>
                  	</div>   -->  	

               		
                 
				  </form>
              </div>
              

            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- End Contact -->

   

  </main>
  
  <!-- End main content --> 

@endsection


@section('javascript')
<script type="text/javascript" src="plugins/jquery-validate/jquery.validate.min.js"></script>
<script type="text/javascript">
	$(function() {
		$("#group").select2({
		    minimumInputLength: 2,
		    tags: [],
		    ajax: {
		        url: URL,
		        dataType: 'json',
		        type: "GET",
		        quietMillis: 50,
		        data: function (term) {
		            return {
		                term: term
		            };
		        },
		        results: function (data) {
		            return {
		                results: $.map(data, function (item) {
		                    return {
		                        text: item.completeName,
		                        slug: item.slug,
		                        id: item.id
		                    }
		                })
		            };
		        }
		    }
		});

	});
</script>

@endsection