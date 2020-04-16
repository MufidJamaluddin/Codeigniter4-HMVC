<?php namespace App\Modules\Land\Controllers;

class Home extends BaseController
{
	public function index()
	{
		$data = ['title' => 'Home Page', 'view' => 'land/home'];
		return view('template/layout', $data);
	}

	//--------------------------------------------------------------------

}
