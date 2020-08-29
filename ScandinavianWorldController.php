<?php

namespace App\Http\Controllers;

use DB;
use Log;
use Session;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScandinavianWorldController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
    	$files = DB::table('scandinavian')
    		->select(
    			'id',
    			'file_name'
    		)
    		->orderBy('id', 'asc')
    		->paginate(20)
    	;

    	foreach ($files as $row) {
    		$row->file_name = str_split($row->file_name, 30);
    	}

        $list = [
        	'files' => $files
        ];

        return view('test/index', $list);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
    	if ($request->hasfile('file')) {
    		try {
    			$id = DB::table('scandinavian')
	    			->orderBy('id', 'desc')
	    			->value('id')
    			;
    			$file_name_db = $id == null ? md5(1) : md5($id+1);
    			$format = explode(".", $request->file('file')->getClientOriginalName());
    			$format = strtolower(array_pop($format));

    			if ($format != 'pdf') {
    				Session::flash("message_error", __('scandinavian.msg_empty'));
    				return redirect()->back();
    			}

    			$file_name_db = $file_name_db.'.'.$format;
    			$request->file('file')->storeAs('test', $file_name_db);

    			DB::table('scandinavian')
    				->insert([
    					'file_name' => $request->file('file')->getClientOriginalName(),
    					'file_name_db' => $file_name_db,
    					'created_at' => Carbon::now()
    				])
    			;

			Session::flash("message_success", __('scandinavian.msg_success_file_upload'));
		} catch (\Exception $e) {
			Session::flash("message_error", __('scandinavian.msg_error_db'));
			report($e);
		}
    	} else {
		Session::flash("message_error", __('application.msg_empty'));
	}

    	return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
    	$file = DB::table('scandinavian')
	    	->where('id', $id)
	    	->value('file_name_db')
    	;

    	$fileUrl = storage_path('app/test/'.$file);
    	$list['url'] = file_exists($fileUrl) ? \App::make('url')->to('surveys/storage/app/test/'.$file) : 'error';

    	return $list;
    }
}
