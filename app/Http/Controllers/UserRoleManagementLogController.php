<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Admin\UserRoleManagementLog;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;


class UserRoleManagementLogController extends Controller
{
    public function __construct()
    {   
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.user_management.logs');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $data = UserRoleManagementLog::leftJoin('admins as changed_by', 'user_role_management_logs.changed_by_id', '=', 'changed_by.id')
        ->leftJoin('admins as changed_in', 'user_role_management_logs.changed_in_record_id', '=','changed_in.id')
        ->leftJoin('admin_roles as changed_in_role', 'user_role_management_logs.changed_in_record_id', '=','changed_in_role.id')
        ->leftJoin('admin_departments as d', 'd.id' , '=', 'changed_in_role.department_id')
        ->select('user_role_management_logs.*','changed_by.name as changed_by_name','changed_by.trax_id as changed_by_trax_id','changed_in.name as changed_in_name','changed_in_role.name as changed_in_role_name','changed_in.trax_id as trax_id', 'd.name as dept_name')->orderBy('user_role_management_logs.created_at', 'desc');

        $datatable = Datatables::of($data)
        ->editColumn('changed_in_name', function ($data) {
            if($data->screen_name == 'User Management') {
                return $data->changed_in_name ;
            } else if ($data->screen_name == 'Role Management') {
                return $data->changed_in_role_name;
            }
        })
        ->filterColumn('changed_by_name',function ($query,$keyword){

            if ($keyword != '') {
                $query->where('changed_by.name', $keyword);
            }
            else {
                $query->whereRaw('false');
            }
        })
        ->filterColumn('changed_in_name',function ($query,$keyword){

            if ($keyword != '') {
                $query->where('changed_in.name', $keyword);
            }
            else {
                $query->whereRaw('false');
            }
        })
        ->filterColumn('changed_by_trax_id',function ($query,$keyword){

            if ($keyword != '') {
                $query->where('changed_by.trax_id', $keyword);
            }
            else {
                $query->whereRaw('false');
            }
        })
        ->filterColumn('trax_id',function ($query,$keyword){

            if ($keyword != '') {
                $query->where('changed_in.trax_id', $keyword);
            }
            else {
                $query->whereRaw('false');
            }
        });
        return $datatable->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
