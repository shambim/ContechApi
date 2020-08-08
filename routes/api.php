<?php

use Illuminate\Http\Request;
//use Validator;
use Illuminate\Support\Facades\Route;
use App\Employee;

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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/


Route::POST('/add_employee', 'EmployeeController@store');
Route::GET('/employee_lists', 'EmployeeController@lists');
Route::GET('/employee_detail/{employee_id}', 'EmployeeController@detail');
Route::POST('/employee_update/{employee_id}', 'EmployeeController@update');
Route::DELETE('employee_delete/{employee_id}', 'EmployeeController@delete');
Route::GET('/top_paid_employee_lists', 'EmployeeController@top_paid_lists');
Route::GET('/average_salary_by_age_employee_lists', 'EmployeeController@average_salary_by_age_lists');




