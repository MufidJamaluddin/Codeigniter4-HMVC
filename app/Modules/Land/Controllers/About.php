<?php namespace App\Modules\Land\Controllers;

class About extends BaseController
{
	public function index()
	{
		$data = ['title' => 'About Page', 'view' => 'land/about'];
		return view('template/layout', $data);
	}

	//--------------------------------------------------------------------

}
