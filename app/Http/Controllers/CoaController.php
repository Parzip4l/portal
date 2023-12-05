<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CoaM;
use App\Accounttype;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class CoaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    
    public function generateAutomaticCode()
    {
        $latestCoa = CoaM::latest('id')->first();

        if ($latestCoa) {
            // Parse the latest code and increment it
            $latestCode = $latestCoa->code;
            $code = 'COA-' . str_pad((intval(substr($latestCode, -3)) + 1), 3, '0', STR_PAD_LEFT);
        } else {
            // If no previous COA exists, start with COA-001
            $code = 'COA-001';
        }

        return $code;
    }

    public function index()
    {
        $coa = CoaM::all();
        $at = Accounttype::all();
        $autoGeneratedCode = $this->generateAutomaticCode();

        $query2 = DB::table('coa')
            ->join('account_type', 'coa.type', '=', 'account_type.id')
            ->select('coa.*', 'account_type.name as nameofaccount');
        $data = $query2->get();
        return view ('pages.accounting.coa.index',compact('coa','at','data','autoGeneratedCode'));
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
        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'type' => 'required'
        ]);

        $uuid = Str::uuid()->toString();
        
        $coa = new CoaM();
        $coa->id = $uuid;
        $coa->name = $request->input('name');
        $coa->type = $request->input('type');
        $coa->code = $request->input('code');
        $coa->reconciliation = $request->input('reconciliation');
        $coa->save();

        return redirect()->route('coa.index')->with('success', 'Chart of Accounts Successfully Added');
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

    /**N
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
        try {
            $coa = CoaM::findOrFail($id);
            $coa->name = $request->name;
            $coa->code = $request->code;
            $coa->reconciliation = $request->reconciliation;
            $coa->save();
            return redirect()->route('coa.index')->with('success', 'Chart of Accounts Updated Successfully');
        } catch (ModelNotFoundException $e) {
            // Handle the case where the record with the specified $id is not found
            return redirect()->back()->withErrors('Data not found.');
        } catch (ValidationException $e) {
            // Handle the case where validation fails
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            // Handle other unexpected errors
            return redirect()->back()->withErrors('An error occurred while updating the data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $coa = CoaM::find($id);
        $coa->delete();
        return redirect()->route('coa.index')->with('success', 'Chart of Accounts Successfully Deleted');
    }
}
