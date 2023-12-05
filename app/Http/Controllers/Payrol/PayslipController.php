<?php

namespace App\Http\Controllers\Payrol;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Employee;
use App\Payrol;
use App\PayrolCM;
use App\Payrollns;

class PayslipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Payrol::all();
        $datans = Payrollns::all();
        return view ('pages.hc.payrol.payslip', compact('data','datans'));
    }

    public function payslipuser()
    {
        $employeeCode = auth()->user()->employee_code;

        // Ambil semua payslip berdasarkan employee_code
        $dataKaryawan = Employee::where('nik', $employeeCode)->first();
        $karyawan = json_decode($dataKaryawan, true);
        if($karyawan['organisasi'] === 'Management Leaders') {
            $payslips = Payrol::where('employee_code', $employeeCode)->get();
        }else{
            $payslips = Payrollns::where('employee_code', $employeeCode)->get();
        }

        return view('pages.hc.payrol.payslip-user', compact('payslips'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        $payslip = Payrol::findOrFail($id);

        $dataPayslip = Payrol::where('id', $id)->get();
        return view('pages.hc.payrol.payslip-file', compact('dataPayslip'));
    }

    public function showns($id)
    {
        $payslip = Payrollns::findOrFail($id);

        $dataPayslip = Payrollns::where('id', $id)->get();

        $totalallowence = $dataPayslip[0]['total_daily'] + $dataPayslip[0]['total_lembur'] + $dataPayslip[0]['uang_makan'] + $dataPayslip[0]['uang_kerajinan'];
        $totalDeductions = $dataPayslip[0]['potongan_hutang'] + $dataPayslip[0]['potongan_mess'] + $dataPayslip[0]['potongan_lain'];
    
        return view('pages.hc.payrol.ns.payslip', compact('dataPayslip','totalallowence','totalDeductions'));
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
