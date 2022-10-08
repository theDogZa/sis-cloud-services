<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Example Routes
// Route::view('/', 'landing');
// Route::view('/examples/plugin', 'examples.plugin');
// Route::view('/examples/blank', 'examples.blank');

Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout')->name('logout'); //view
// Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'role:developer'], function () {

    Route::resource('/permissions', 'PermissionsController');
    Route::resource('/users_permissions', 'UsersPermissionsController');
});


Route::group(['middleware' => 'auth'], function () {
    
    Route::view('/', 'dashboard');

    Route::match(['get', 'post'], '/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::match(['get', 'post'], '/home', function () {
        return view('dashboard');
    })->name('home');

    // Route::group(['middleware' => 'role:create.users'], function () {
    //     Route::get('/users/create', 'UsersController@create')->name('users.create');
    // });

    Route::resource('/users', 'UsersController')->names([
        'index' => 'users.index',
        'show' => 'users.show'
    ]);
  
    Route::resource('/api_users', 'ApiUsersController')->names([
        'index' => 'api_users.index',
        'show' => 'api_users.show'
    ]);
    Route::get('/edge-users-map/{id}', 'UserMapController@listByApiUser')->name('api_users.listUser');
    Route::Post('/update-edge-users', 'UserMapController@storeApiUser')->name('userMap.storeApiUser');


    Route::resource('/roles', 'RolesController')->names([
        'index' => 'roles.index',
        'show' => 'roles.show'
    ]);

    Route::resource('/config', 'ApiConfigController');

    Route::resource('/examples', 'ExamplesController');
    Route::resource('/roles', 'RolesController');
    Route::resource('/roles_permissions', 'RolesPermissionsController');
   
    Route::resource('/users_roles', 'UserRolesController');
    
    Route::resource('/log_api', 'LogApiController')->names([
        'index' => 'log_api.index',
        'show' => 'log_api.show',
        'destroy' => 'log_api.del'
    ]);

    Route::get('/profile', 'Auth\ProfilesController@index')->name('profiles.index');
    Route::Post('/profile', 'Auth\ProfilesController@store')->name('profiles.store');
    Route::get('/change-password', 'Auth\ChangePasswordController@index')->name('changePasswords.index');
    Route::Post('/change-password', 'Auth\ChangePasswordController@store')->name('changePasswords.store');
   
    Route::get('/EDGECluster', 'OpenTenantController@index')->name('openTenants.index');
    // Route::get('/logApi', 'LogApiController@index')->name('logApi.index');
    // Route::get('/logApi/{id}', 'LogApiController@show')->name('logApi.show');

    Route::get('/roles-permissions/{id}', 'RolesPermissionsController@listByRoles')->name('roles.update_permissions_roles');
    Route::Post('/store-roles', 'RolesPermissionsController@storeRoles')->name('rolesPermission.storeRoles');
    Route::get('/roles-users/{id}', 'UserRolesController@listByRoles')->name('roles.update_user_roles');
    Route::Post('/update-roles-users', 'UserRolesController@storeRoles')->name('userRoles.storeRoles');

    Route::get('/users-permissions/{id}', 'UsersPermissionsController@listByUser')->name('users.list_permissions');
    //Route::get('/update-permissions/{id}', 'UserRolesController@listByRoles')->name('roles.update_user_roles');

    Route::get('/view-logs', 'ViewLogsController@sysLogsUser')->name('view_logs.sysLogs');
    Route::post('/view-logs', 'ViewLogsController@sysLogsUser');

    Route::fallback(function () {
        return view('errors.404');
    });
    
});


// Route::group(['middleware' => 'role:admin'], function () {
//     Route::get('/admin', function () {
//         return 'Welcome Admin';
//     });
// });
