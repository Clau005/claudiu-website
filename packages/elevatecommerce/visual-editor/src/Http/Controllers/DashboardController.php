<?php

namespace ElevateCommerce\VisualEditor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $navigation = app('visual-editor.navigation')->all();
        $dashboard = app('visual-editor.dashboard');
        
        // Get components that the current user can view
        $components = $dashboard->forUser($request->user('admin'));

        return view('visual-editor::admin.dashboard', [
            'admin' => $request->user('admin'),
            'navigation' => $navigation,
            'components' => $components,
        ]);
    }
}
