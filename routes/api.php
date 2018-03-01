<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/test_post', 'API\HomeController@testPost' ) ;
Route::put('/test_put', 'API\HomeController@testPut' ) ;

Route::get('/testss', 'API\HomeController@test' ) ;
Route::post('/test-upload', 'API\HomeController@upload' ) ;

Route::get('/lang/{lang}',function($lang){
    // Cookie::queue(Cookie::make('rm_locale', $lang, 86400000));
    // cookie('rm_locale', $lang, 8640000);
    setcookie('rm_locale', $lang, time() + (86400 * 30), "/");
    return ['result'=>true,'error'=> $_COOKIE['rm_locale'] ];
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/signup', 'API\User\RegistorController@signup' ) ;

Route::post('/resetpass', 'API\User\RegistorController@resetPass' ) ;
Route::post('/reset/change/password', 'API\User\RegistorController@reset' ) ;

Route::post('/signin', 'API\User\LoginController@signin' ) ;

Route::post('/search/temp', 'API\SearchController@temp'); 
Route::post('/search/user-data', 'API\SearchController@userData'); 

Route::post('/facebook_signup', 'API\User\RegistorController@facebookSignUp' ) ;
Route::post('/facebook_signin', 'API\User\LoginController@facebookSignin' ) ;

Route::group(['prefix'=>'/search'],function(){
	Route::post('/district', 'API\SearchController@district'); 
	Route::post('/amphur', 'API\SearchController@amphur'); 
	Route::post('/province', 'API\SearchController@province'); 
	Route::post('/amphur-id', 'API\SearchController@amphurById'); 
	Route::post('/district-id', 'API\SearchController@districtById'); 
	Route::post('/zipcode-id', 'API\SearchController@ZipcodeById'); 

});


Route::post('/{domain}/search/room', 'API\SearchController@room'); 

Route::group(['middleware'=> ['auth:api'] ],function(){
	Route::get('/domain', 'API\User\DomainController@index' ) ;
	Route::post('/domain', 'API\User\DomainController@store' ) ;
	Route::get('/domain/list', 'API\User\DomainController@lists' ) ;
	Route::post('/domain/search', 'API\User\DomainController@search' ) ;
	Route::post('/domain/join', 'API\User\DomainController@joinStore' ) ;
	Route::post('/notification', 'API\User\NotificationController@store' ) ;
	Route::get('/notification', 'API\User\NotificationController@index' ) ;
	Route::delete('/notification', 'API\User\NotificationController@destroy' ) ;
	Route::put('/notification/seen', 'API\User\NotificationController@seen' ) ;


	Route::get('/menu-count', 'API\HomeController@sidebarMenu'); 
	

	Route::group(['prefix'=>'/profile'],function(){
		Route::get('/show', 'API\User\ProfileController@show' ) ;
		Route::get('/edit', 'API\User\ProfileController@edit' ) ;
		Route::put('/update', 'API\User\ProfileController@update' ) ;
		Route::put('/changepass', 'API\User\ProfileController@changePassUpdate' ) ;
		Route::get('/room', 'API\User\ProfileController@room' ) ;

		Route::resource('/address', 'API\User\AddressController' );
		Route::post('/address/active/{id}', 'API\User\AddressController@active' );
		Route::resource('/attach', 'API\User\AttachmentController' );

		// Route::get('/address', 'API\User\ProfileController@address' ) ;
		// Route::post('/address', 'API\User\ProfileController@addressStore' ) ;
		// Route::get('/address/{id}/edit', 'API\User\ProfileController@addressEdit' ) ;
		// Route::put('/address/{id}', 'API\User\ProfileController@addressUpdate' ) ;
		// Route::delete('/address/{id}', 'API\User\ProfileController@addressDelete' ) ;
		Route::put('/room', 'API\User\ProfileController@roomUpdate' ) ;
		Route::post('/avatar', 'API\User\ProfileController@avatar' ) ;
		Route::post('/uploadimg', 'API\User\ProfileController@uploadProfileImg' ) ;
	});


	

} );

Route::get('/role', 'API\User\RoleController@index' ) ;
Route::post('/validate', 'API\ValidateController@index' ) ;

Route::group(['prefix'=>'validate'],function(){
	Route::post('/idcard', 'API\ValidateController@idcard' ) ;
	Route::post('/username', 'API\ValidateController@username' ) ;
	Route::post('/domain-name', 'API\ValidateController@domainName' ) ;
} );

Route::group(['prefix'=>'master'],function(){
	Route::get('/channeltype', 'API\MasterController@channeltype' ) ;
	Route::get('/channelicon', 'API\MasterController@channelicon' ) ;
	Route::get('/unit', 'API\MasterController@unit' ) ;
	Route::get('/address', 'API\MasterController@address' ) ;
	Route::get('/role', 'API\MasterController@role' ) ;
	Route::get('/prioritizes', 'API\MasterController@prioritize' ) ;
} );

Route::group(['prefix'=>'/{domain}','middleware'=>['auth:api'] ],function(){
	
	
	Route::resource('/log-activity', 'API\Admin\LogActivityController'); 

	Route::group(['prefix'=>'/channel'],function(){
		Route::post('/validate/name', 'API\Main\ChannelController@validateName'); 
		Route::post('/{channel_id}/pushoff', 'API\Main\ChannelController@pushoff'); 
		Route::post('/{channel_id}/invite', 'API\Main\ChannelController@invite'); 
		Route::post('/{channel_id}/join', 'API\Main\ChannelController@join'); 
		Route::get('/{channel_id}/member', 'API\Main\ChannelController@member'); 
		Route::post('/{channel_id}/accept', 'API\Main\ChannelController@accept'); 
		Route::post('/{channel_id}/owner', 'API\Main\ChannelController@owner'); 
		Route::delete('/{channel_id}/kick', 'API\Main\ChannelController@kick'); 
		Route::delete('/{channel_id}/leave', 'API\Main\ChannelController@leave'); 
		Route::delete('/message/{message_id}/', 'API\Main\ChannelController@destroyMessage'); 
		Route::put('/message/{message_id}/pin', 'API\Main\ChannelController@pinMessage'); 
		Route::put('/message/{message_id}/unpin', 'API\Main\ChannelController@unpinMessage'); 
		Route::post('/{channel_id}/chat', 'API\Main\ChannelController@chat'); 
		Route::post('/{channel_id}/attach', 'API\Main\ChannelController@chatAttachment'); 
		Route::post('/{channel_id}/push', 'API\Main\ChannelController@push'); 
		Route::post('/direct_chat', 'API\Main\ChannelController@directChat'); 
		Route::get('/contact', 'API\Main\ChannelController@contact');
		Route::get('/join', 'API\Main\ChannelController@getChannelJoin');
		Route::get('/list-contact', 'API\Main\ChannelController@getContact');
		Route::delete('/{channel_id}/remove-contact', 'API\Main\ChannelController@contactDestroy');
	});
	Route::resource('/channel', 'API\Main\ChannelController');

	Route::put('/wait-approve', 'API\Admin\CreateUserController@waitApproveUpdate');
	Route::get('/create-user/init', 'API\Admin\CreateUserController@init'); 
	Route::get('/create-user/{id_card}/approve', 'API\Admin\CreateUserController@approve'); 
	Route::put('/create-user/{id_card}/room-approve', 'API\Admin\CreateUserController@roomApprove'); 
	Route::resource('/create-user', 'API\Admin\CreateUserController'); 

	Route::resource('/wait-user', 'API\Admin\WaitUserController'); 
	Route::resource('/request-room', 'API\Admin\RequestRoomController'); 

	Route::resource('/create-admin', 'API\Admin\CreateAdminController');

	Route::resource('/phone', 'API\Officer\PhoneDirectoryController');
	Route::resource('/master/contact', 'API\Master\ContactController');
	Route::get('/contact/type', 'API\Main\ContactController@contactType');
	Route::resource('/contact', 'API\Main\ContactController');
	Route::resource('/pre-welcome', 'API\Admin\PreWelcomeController');

	Route::group(['prefix'=>'/search','namespace'=>'API'],function(){
		Route::post('/member/task/{task_id}', 'SearchController@memberTask' ) ;
		Route::post('/company', 'SearchController@company'); 
		Route::post('/user', 'SearchController@user'); 
		Route::post('/member/channel/{channel_id}', 'SearchController@channelMember');
	});


	// Route::group(['prefix'=>'/rooms' , 'namespace'=>'API\User' ],function(){
		
		
	// });

	Route::resource('/rooms', 'API\User\RoomController'); 

	Route::group(['namespace'=>'API\Officer\Purchase'],function(){
		Route::get('/quotation-vote-setting', 'QuotationController@voteSettingGet');  
		Route::put('/quotation-vote-setting', 'QuotationController@voteSettingUpdate'); 
	});

	Route::group(['prefix'=>'/parking','namespace'=>'API\Parking'],function(){
		Route::post('/guard/search', 'GuardController@search');
		Route::post('/guard/check-hour', 'GuardController@checkHour');
		Route::resource('/cancel', 'CancelController');  
		Route::resource('/guard', 'GuardController'); 
		Route::resource('/package', 'PackageController'); 
		Route::resource('/buy', 'BuyController'); 
		Route::get('/{room_id}/use/package', 'UseController@getPackage'); 
		Route::post('/{room_id}/hour-remain', 'UseController@getRemainHour'); 
		Route::resource('/{room_id}/use', 'UseController'); 

		Route::resource('/report', 'ReportController'); 
		Route::resource('/debt', 'DebtController'); 

		
	});

	Route::get('/parcel/master/type', 'API\Parcel\OfficerController@masterParcelType');

	Route::resource('/parcel/{room_id}/user', 'API\Parcel\UserController'); 
	Route::resource('/parcel/officer', 'API\Parcel\OfficerController'); 


	Route::post('/parcel/receive/{id}', 'API\Parcel\UserController@receive');   
	Route::delete('/parcel/receive/{id}', 'API\Parcel\UserController@unReceive');   
	Route::get('/parcel/print-gift', 'API\Parcel\PrintController@getGift');   
	Route::get('/parcel/print-gift/view', 'API\Parcel\PrintController@getParcelView');   
	Route::get('/parcel/print-mail', 'API\Parcel\PrintController@getMail');   
	Route::get('/parcel/print-mail/view', 'API\Parcel\PrintController@getParcelView');   
	Route::get('/parcel/print/setting', 'API\Parcel\PrintController@getSetting');  

	Route::resource('/parcel/print', 'API\Parcel\PrintController');  
	



	Route::group(['prefix'=>'/purchase/quotation','namespace'=>'API\Officer\Purchase'],function(){

		Route::get('/setting', 'QuotationController@settingGet'); 
		Route::get('/setting/edit', 'QuotationController@settingEdit'); 
		Route::put('/setting', 'QuotationController@settingUpdate'); 

		Route::post('/item', 'QuotationController@itemStore'); 
		Route::delete('/{quotation}/item/{item_id}', 'QuotationController@itemDelelete'); 
		Route::post('/company/create/', 'QuotationController@companyStore'); 
		Route::get('/data/{quotation}', 'QuotationController@data'); 
		Route::get('/{quotation}/company/{company_id}', 'QuotationController@companyData'); 
		Route::get('/{quotation}/company', 'QuotationController@companyList'); 
		Route::get('/{quotation}/company-instead', 'QuotationController@companyListInstead'); 
		Route::get('/{quotation}/voting/{voting}', 'QuotationController@voting'); 

		Route::post('/{quotation}/voting/{voting}', 'QuotationController@votingInstead'); 
		Route::delete('/{quotation}/voting/{voting}', 'QuotationController@votingInsteadDestroy'); 
		Route::get('/{quotation}/novote', 'QuotationController@novote'); 
		Route::post('/{quotation}/comment', 'QuotationController@commentStore'); 
		Route::post('/{quotation}/comment/{comment_id}', 'QuotationController@commentUpdate'); 
		Route::delete('/{quotation}/comment/{comment_id}', 'QuotationController@commentDelete');
		Route::delete('/{quotation}/attach/{file_code}', 'QuotationController@companyAttachmentDelete');
		Route::delete('/{quotation}/change_voted', 'QuotationController@changeVoted');
		Route::get('/{quotation}/attach/{company_id}', 'QuotationController@companyAttachment');
		Route::get('/{quotation}/attach-all', 'QuotationController@companyAttachmentAll');
		Route::put('/{quotation}/status', 'QuotationController@status');
		Route::get('/{quotation}/winner/{company_id}', 'QuotationController@voteWinner'); 
	});
	Route::resource('/purchase/quotation', 'API\Officer\Purchase\QuotationController'); 
	

	Route::group(['prefix'=>'/resolution','namespace'=>'API\Officer'],function(){
		Route::post('/item', 'ResolutionController@itemStore'); 
		Route::delete('/{quotation}/item/{item_id}', 'ResolutionController@itemDelelete');
		Route::get('/data/{quotation}', 'ResolutionController@data'); 
		Route::get('/{quotation}/voting/{voting}', 'ResolutionController@voting'); 
		Route::get('/{quotation}/novote', 'ResolutionController@novote'); 
		Route::delete('/{quotation}/change_voted', 'ResolutionController@changeVoted');
		Route::put('/{quotation}/status', 'ResolutionController@status');
		Route::get('/{quotation}/winner/{company_id}', 'ResolutionController@voteWinner');

		Route::post('/{quotation}/comment', 'ResolutionController@commentStore'); 
		Route::post('/{quotation}/comment/{comment_id}', 'ResolutionController@commentUpdate'); 
		Route::delete('/{quotation}/comment/{comment_id}', 'ResolutionController@commentDelete'); 
	});
	Route::resource('/resolution', 'API\Officer\ResolutionController');

	
	Route::group(['prefix'=>'/task','namespace'=>'API\Main'],function(){
		Route::post('/{task_id}/comment', 'TaskController@commentStore'); 
		Route::post('/{task_id}/viewer', 'TaskController@viewer'); 
		Route::put('/{task_id}/comment/{comment_id}', 'TaskController@commentUpdate'); 
		Route::delete('/{task_id}/comment/{comment_id}', 'TaskController@commentDelete');
		
		Route::post('/{task_id}/attachment', 'TaskController@attachmentStore');
		Route::delete('/{task_id}/attachment/{attach_id}', 'TaskController@attachmentDelete'); 
		Route::post('/{task_id}/category/{category_id}', 'TaskController@category'); 
		Route::post('/{task_id}/member/{user_id}', 'TaskController@member'); 
		Route::post('/{task_id}/checklist', 'TaskController@checklistStore'); 
		Route::delete('/{task_id}/checklist/{checklist_id}', 'TaskController@checklistDelete'); 
		Route::put('/{task_id}/checklist/{checklist_id}', 'TaskController@checklistUpdate'); 
		Route::post('/{task_id}/checklist/{checklist_id}/item', 'TaskController@checklistItem'); 
		Route::delete('/{task_id}/checklist/item/{checklist_item_id}', 'TaskController@checklistItemDelete'); 
		Route::put('/{task_id}/checklist/item/{checklist_item_id}', 'TaskController@checklistItemUpdate'); 


		Route::post('/filter', 'TaskController@filter'); 
		Route::post('/search/filter', 'TaskController@searchFilter'); 

	});
	Route::resource('/task', 'API\Main\TaskController'); 
	Route::resource('/user/suggest/system', 'API\User\SuggestController'); 


	Route::group(['prefix'=>'/suggest/system','namespace'=>'API\Admin'],function(){
		Route::post('/{task_id}/comment', 'SuggestController@commentStore'); 
		Route::put('/{task_id}/comment/{comment_id}', 'SuggestController@commentUpdate'); 
		Route::delete('/{task_id}/comment/{comment_id}', 'SuggestController@commentDelete');
		
		Route::post('/{task_id}/attachment', 'SuggestController@attachmentStore');
		Route::delete('/{task_id}/attachment/{attach_id}', 'SuggestController@attachmentDelete');

		Route::put('/{task_id}/status', 'SuggestController@status');

		Route::post('/{task_id}/category/{category_id}', 'SuggestController@category'); 

		Route::post('/filter', 'SuggestController@filter'); 
	});
	Route::resource('/suggest/system', 'API\Admin\SuggestController'); 




	
	Route::group(['prefix'=>'/user/task','namespace'=>'API\User'],function(){
		Route::post('/{task_id}/comment', 'TaskController@commentStore'); 
		Route::post('/{task_id}/viewer', 'TaskController@viewer'); 
		Route::put('/{task_id}/comment/{comment_id}', 'TaskController@commentUpdate'); 
		Route::delete('/{task_id}/comment/{comment_id}', 'TaskController@commentDelete');
		Route::put('/{task_id}/status', 'TaskController@status');
		Route::post('/{task_id}/attachment', 'TaskController@attachmentStore');
		Route::delete('/{task_id}/attachment/{attach_id}', 'TaskController@attachmentDelete'); 
		Route::post('/{task_id}/category/{category_id}', 'TaskController@category'); 
		Route::post('/{task_id}/member/{user_id}', 'TaskController@member'); 
		Route::post('/{task_id}/checklist', 'TaskController@checklistStore'); 
		Route::delete('/{task_id}/checklist/{checklist_id}', 'TaskController@checklistDelete'); 
		Route::post('/{task_id}/checklist/{checklist_id}/item', 'TaskController@checklistItem'); 
		Route::delete('/{task_id}/checklist/item/{checklist_item_id}', 'TaskController@checklistItemDelete'); 
		Route::put('/{task_id}/checklist/item/{checklist_item_id}', 'TaskController@checklistItemUpdate'); 
	});
	Route::resource('/user/{room_id}/task', 'API\User\TaskController'); 

	Route::resource('/officer/task', 'API\Officer\TaskController'); 

	Route::group(['prefix'=>'/routine','namespace'=>'API\Officer'],function(){
		Route::get('/view', 'RoutineController@view'); 
		
	});
	Route::resource('/routine', 'API\Officer\RoutineController'); 

	Route::get('/dashboard', 'API\Main\DashboardController@index' ) ;


	Route::group(['prefix'=>'/post','namespace'=>'API\Main'],function(){
	
		Route::post('/{task_id}/comment', 'PostController@commentStore'); 
		Route::post('/{task_id}/ban', 'PostController@ban'); 
		Route::delete('/{user_id}/unban', 'PostController@unBan'); 
		Route::post('/{task_id}/viewer', 'PostController@viewer'); 
		Route::put('/{task_id}/comment/{comment_id}', 'PostController@commentUpdate'); 
		Route::delete('/{task_id}/comment/{comment_id}', 'PostController@commentDelete');
		Route::put('/{task_id}/status', 'PostController@status');
		Route::post('/{task_id}/attachment', 'PostController@attachmentStore');
		Route::delete('/{task_id}/attachment/{attach_id}', 'PostController@attachmentDelete'); 
		Route::post('/{task_id}/category/{category_id}', 'PostController@category'); 
		Route::put('/{task_id}/like', 'PostController@like'); 
		Route::post('/{task_id}/checklist', 'PostController@checklistStore'); 
		Route::delete('/{task_id}/checklist/{checklist_id}', 'PostController@checklistDelete'); 
		Route::put('/{task_id}/checklist/{checklist_id}', 'PostController@checklistUpdate'); 
		Route::post('/{task_id}/checklist/{checklist_id}/item', 'PostController@checklistItem'); 
		Route::delete('/{task_id}/checklist/item/{checklist_item_id}', 'PostController@checklistItemDelete'); 
		Route::put('/{task_id}/checklist/item/{checklist_item_id}', 'PostController@checklistItemUpdate'); 


		Route::post('/filter', 'PostController@filter'); 
		Route::post('/search/filter', 'PostController@searchFilter'); 

	});
	Route::resource('/post', 'API\Main\PostController'); 
	Route::resource('/notice', 'API\Officer\NoticeController'); 
	Route::post('/e-sticker/{e_id}/log', 'API\Officer\EstickerController@logPrint'); 
	Route::resource('/e-sticker', 'API\Officer\EstickerController'); 

} );
