<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Salary;
use App\Models\SalaryMaster;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use DateTime;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public $title = null;

    function __construct(Request $request)
    {
        $param = $request->segment(2);
        $title_str = ["view_all" => "Employees", "add" => "Add Employee", "view" => "View Employee", "edit" => "Edit Employee"];
        $this->title = $title_str[$param == null || !array_key_exists($param, $title_str) ? "view_all" : $param];
    }

    public function index(Request $request)
    {
        if (!Gate::allows('view_employee')) {
            return redirect()->back();
        }

        if ($request->ajax()) {
            $unlink_users = $this->getUser();
            $search = $request->input("search.value");
            $ord_type = $request->input("order.0.dir");
            $ord_col_num = $request->input("order.0.column");
            $orderdata = array(0 => 'employee_id', 1 => 'employee_eid', 2 => 'first_name', 3 => 'email', 4 => 'join_date');
            $ord_col = $orderdata[$ord_col_num];
            
            $data = Employee::select('employee_id', DB::raw("CONCAT(employees.first_name,' ',employees.last_name) as employee_name"), 'email', 'join_date', DB::raw('DATE_FORMAT(join_date,"%d/%m/%Y") as join_date_formatted'), 'employee_eid', 'user_id')
                ->when($search, function ($query, $search) {
                    return $query->orWhere('first_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('email', 'LIKE', '%' . $search . '%')
                        ->orWhere('join_date', 'LIKE', '%' . $search . '%')
                        ->orWhere('employee_eid', 'LIKE', '%' . $search . '%');
                })
                ->orderBy($ord_col, $ord_type)
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    if (empty($row->user_id)) {
                        $btn = $btn . ' ' . '<a data-toggle="modal" data-target="#link_emp" data-id="' . $row->employee_id . '" class="btn btn-info btn-xs"><i class="fas fa-link" title="Link Employee"></i></a>';
                    }
                    if (Gate::allows('view_employee')) {
                        $btn = $btn . ' ' . '<a href="employees/view/' . $row->employee_id . '" class="btn btn-primary btn-xs"><i class="fas fa-eye" title="View Employee"></i></a>';
                    }
                    if (Gate::allows('edit_employee')) {
                        $btn = $btn . ' ' . '<a href="employees/edit/' . $row->employee_id . '" class="btn btn-success btn-xs"><i class="fas fa-pencil-alt" title="Edit Employee"></i></a>';
                    }
                    if (Gate::allows('delete_employee')) {
                        $btn = $btn . ' ' . '<a data-toggle="modal" data-target="#exampleModalCenter" id="del_emp" data-id="' . $row->employee_id . '" class="btn btn-danger btn-xs"><i class="fas fa-trash" title="Delete Employee"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $unlink_users = $this->getUser();
        $title = $this->title;

        return view('employee', compact('unlink_users', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Gate::allows('add_employee')) {
            return redirect()->back();
        }

        $request->validate([
            'employee_eid' => 'unique:employees|required',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'unique:employees|required',
            'birth_date' => 'required',
            'join_date' => 'required'
        ]);

        //update group_id in user table for employee
        if ($request->user_id != null) {
            User::where('id', $request->user_id)->update(array('group_id' => '2'));
        }

        $emp = Employee::where('employee_eid', $request->employee_eid)->get();
        if (empty($emp[0]->employee_eid)) {
            $emp = new Employee();
            $emp->employee_eid = $request->employee_eid;
            $emp->user_id = $request->user_id;
            $emp->first_name = ucfirst($request->first_name);
            $emp->last_name = ucfirst($request->last_name);
            $emp->email = $request->email;
            $emp->birth_date = DateTime::createFromFormat("d/m/Y", $request->birth_date)->format("Y-m-d");
            $emp->join_date = DateTime::createFromFormat("d/m/Y", $request->join_date)->format("Y-m-d");
            $emp->pan = $request->pan;
            $emp->aadhar = $request->aadhar;
            $emp->address = $request->address;
            $emp->save();
            return redirect('/employees')->with('success', 'Employee added successfully.');
        } else {
            return redirect('/employees')->with('error', 'EmployeeId already exist.');
        }
    }

    public function link_employee(Request $req)
    {
        Employee::where('employee_id', $req->employee_id)->update(array('user_id' => $req->user_id));
        User::where('id', $req->user_id)->update(array('group_id' => 2));

        session()->flash('success', 'Employee linked successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($employee_id)
    {
        if (!Gate::allows('view_employee')) {
            return redirect()->back();
        }

        $user = Employee::where('employee_id', $employee_id)->first();
        $title = $this->title;

        return view('view_employee', compact('user', 'title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($employee_id)
    {
        if (!Gate::allows('edit_employee')) {
            return redirect()->back();
        }

        $user = Employee::where('employee_id', $employee_id)->first();
        $title = $this->title;

        return view('edit_employee', compact('user', 'title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $employee_id)
    {
        if (!Gate::allows('edit_employee')) {
            return redirect()->back();
        }

        $request->validate([
            'employee_eid' => 'required',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|unique:employees',
            'birth_date' => 'required',
            'join_date' => 'required',
        ]);

        $edit_emp = Employee::where('employee_id', $employee_id)->firstOrFail();
        $edit_emp->employee_eid = $request->employee_eid;
        $edit_emp->first_name = ucfirst($request->first_name);
        $edit_emp->last_name = ucfirst($request->last_name);
        $edit_emp->email = $request->email;
        $edit_emp->birth_date =  DateTime::createFromFormat('Y-m-d', $request->birth_date);
        $edit_emp->join_date =  DateTime::createFromFormat('Y-m-d', $request->join_date);
        $edit_emp->pan = $request->pan;
        $edit_emp->aadhar = $request->aadhar;
        $edit_emp->address = $request->address;
        $edit_emp->update();

        return redirect('/employees')->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (!Gate::allows('delete_employee')) {
            return redirect()->back();
        }

        SalaryMaster::where('employee_id', $request->employee_id)->delete();
        Salary::where('employee_id', $request->employee_id)->update(array('deleted_by' => Auth::user()->id));
        Salary::where('employee_id', $request->employee_id)->delete();

        $employee = Employee::leftJoin("project_teams", "employees.employee_id", "=", "project_teams.employee_id")
            ->leftJoin("task_users", "employees.employee_id", "=", "task_users.employee_id")
            ->select('employees.employee_id')
            ->where("project_teams.employee_id", "=", $request->employee_id)
            ->orWhere("task_users.employee_id", "=", $request->employee_id)
            ->first();

        if (empty($employee->employee_id)) {
            Employee::where('employee_id', $request->employee_id)->update(array('deleted_by' => Auth::user()->id));
            Employee::where('employee_id', $request->employee_id)->delete();
        } else {
            return session()->flash('error', "Can't delete this employee until he/she is assigned to a task");
        }
    }

    public function getEmail(Request $req)
    {
        $user = User::select('email')->where('id', $req->user_id)->get();
        return response()->json($user[0]->email);
    }

    public function createEmpId()
    {
        $emp_id = Employee::select('employee_eid')->orderBy('employee_id', 'desc')->withTrashed()->get();

        if (isset($emp_id[0]->employee_eid)) {
            $emp_eid = (int) ltrim($emp_id[0]->employee_eid, config('app.emp_prefix'));
            return config('app.emp_prefix') . str_pad((++$emp_eid), 3, "0", STR_PAD_LEFT);
        } else {
            return config('app.emp_prefix') . str_pad((1), 3, "0", STR_PAD_LEFT);
        }
    }

    public function getUser()
    {
        $uid = Employee::select('user_id')->where('user_id', '<>', '')->get();
        $data = array();
        foreach ($uid as $i) {
            array_push($data, $i->user_id);
        }
        $users = User::whereNotIn('group_id', [1])->whereNotIn('id', $data)->get();

        return $users;
    }

    public function empAdd()
    {
        if (!Gate::allows('add_employee')) {
            return redirect()->back();
        }

        $emp_id = $this->createEmpId();
        $users = $this->getUser();
        $title = $this->title;
        
        return view('add_employee', compact('emp_id', 'users', 'title'));
    }
}
