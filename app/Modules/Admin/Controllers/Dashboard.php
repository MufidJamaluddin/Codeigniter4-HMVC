<?php namespace App\Modules\Admin\Controllers;

class Dashboard extends BaseController
{
	public function index()
	{
		$data = ['title' => 'Dashboard Page', 'view' => 'admin/dashboard'];
		return view('template/layout', $data);
	}

	//--------------------------------------------------------------------

}
