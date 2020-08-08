<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use App\Employee;
use DB;
class EmployeeController extends Controller
{
    public function store(Request $request){
        $validator =$this->validateEmployee($request);

        if($validator->fails()){
            $all_errors=$validator->errors();
            $res=$this->parse_error($all_errors);
            return response()->json($res, 422);
        }else{
            $last_emp_id=Employee::create([
                'name'=>$request->input('name'),
                'email'=>$request->input('email'),
                'age'=>$request->input('age'),
                'phone_number'=>$request->input('phone_number'),
                'department'=>$request->input('department'),
                'salary'=>$request->input('salary')
            ]);
            if($last_emp_id){
                $res=array();
                $res['messge']='Employee Information added successfully';
                return response()->json($res, 200);
            }
        }
    }

    public function update(Request $request,$employee_id){
        $validator =$this->validateEmployee($request);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }else{
            $employee=Employee::findOrFail($employee_id);
            $employee->name=$request->input('name');
            $employee->email=$request->input('email');
            $employee->phone_number=$request->input('phone_number');
            $employee->age=$request->input('age');
            $employee->department=$request->input('department');
            $employee->salary=$request->input('salary');

            $last_emp_id=$employee->save(); 
            $res=array();
            if($last_emp_id){
                $res['messge']='Employee Information updated successfully';
                return response()->json($res, 200);
            }else{
                $res['messge']='Employee not Updated';
                return response()->json($res, 402);
            }
        }
    }

    function detail($employee_id){
        $employee_detail=Employee::findOrFail($employee_id);
        $res=array();
        if(is_object($employee_detail)){
            $res['detail']=$employee_detail;
            return response()->json($res, 200);
        }else{
            $res['message']='No Record Found';
            return response()->json($res, 402);
        }

    }

    public function delete($employee_id){
      
        $employee=Employee::findOrFail($employee_id);
       
        $last_emp_id=$employee->delete(); 
        $res=array();
        if($last_emp_id){
            $res['messge']='Employee Information removed successfully';
            return response()->json($res, 200);
        }else{
            $res['messge']='Employee not Removed';
            return response()->json($res, 402);
        }
        
    }        

    public function lists(){
        $employee_lists=Employee::all();
        $res=array();
        if(isset($employee_lists) && count($employee_lists)>0){
            $res['result']=$employee_lists;
            return response()->json($res, 200);
        }else{
            $res['message']='No Record Found';
            return response()->json($res, 422);
        }
    }

    public function top_paid_lists(){
        $employee_lists=Employee::orderBy('id','desc')->take(5)->get();
        $res=array();
        if(isset($employee_lists) && count($employee_lists)>0){
            $top_employee_name=array();
            $top_employee_salary=array();

            for($i=0;$i<count($employee_lists);$i++){
                $top_employee_name[$i]=$employee_lists[$i]->name;
                $top_employee_salary[$i]=$employee_lists[$i]->salary;
            }
            $top_employees=array();
            $top_employees['names']=$top_employee_name;
            $top_employees['salaries']=$top_employee_salary;

            $res['result']=$top_employees;
            return response()->json($res, 200);
        }else{
            $res['message']='No Record Found';
            return response()->json($res, 422);
        }
    }


    public function average_salary_by_age_lists(){
        $employee_lists=Employee::all();

        $age_ranges=array('20-24','25-39','30-34','35-39','40-49','50-65');

        $s=0;
        $all_age_ranges=array();
        $average_salaries=array();
        foreach($age_ranges as $age_range){
            $arr_age_range=explode('-',$age_range);
           
            $start_age=$arr_age_range[0];
            $end_age=$arr_age_range[1];
          
            $employee_by_age = DB::table('employees')
            ->select(DB::raw('sum(`salary`)/count(*) as avg_salary'))
            ->WhereBetween('age',[$start_age,$end_age])
            ->get();
          
            
            if(isset($employee_by_age[0]) && $employee_by_age[0]->avg_salary!=NULL){
                $average_salaries[$s]=$employee_by_age[0]->avg_salary;
                $all_age_ranges[$s]=$age_range;
                $s++;
            }
        }

        $employees_average_salary_by_age=array();
        $employees_average_salary_by_age['average_salaries']=$average_salaries;
        $employees_average_salary_by_age['all_age_ranges']=$all_age_ranges;
        if(count($average_salaries)>0){
            $res['result']=$employees_average_salary_by_age;
            return response()->json($res, 200);
        }else{
            $res['message']='No Record Found';
            return response()->json($res, 422);
        }
    }


    


    protected function validateEmployee($request){
        $rules=[
            'name' => 'required',
            'email' => 'required|unique:employees|max:255',
            'phone_number' => 'required',
            'age' => 'required',
            'department' => 'required',
            'salary' => 'required'
        ];

        return Validator::make($request->all(), $rules);
    }

    protected function parse_error($all_errors){
        $res=array();
            
            $strError='';
            if(is_object($all_errors)){
                $all_errors=json_decode($all_errors,true);
            }
            
            if(isset($all_errors['name']) && $all_errors['name']!='') $strError.=$all_errors['name'][0].'<br/>';
            if(isset($all_errors['email']) && $all_errors['email']!='') $strError.=$all_errors['email'][0].'<br/>';
            if(isset($all_errors['age']) && $all_errors['age']!='') $strError.=$all_errors['age'][0].'<br/>';
            if(isset($all_errors['phone_number']) && $all_errors['phone_number']!='') $strError.=$all_errors['phone_number'][0].'<br/>';
            if(isset($all_errors['department']) && $all_errors['department']!='') $strError.=$all_errors['department'][0].'<br/>';
            if(isset($all_errors['salary']) && $all_errors['salary']!='') $strError.=$all_errors['salary'][0].'<br/>';

            $res=array('error'=>$strError);
            return $res;
    }
}
