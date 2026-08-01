<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class DesignController extends Controller
{
    public function customWebpageDesign(): View
    {
        return view('designs.custom-webpage-design');
    }

    public function tmsDashboard(): View
    {
        return view('designs.tms-dashboard');
    }

    public function tmsMonkManagement(): View
    {
        return view('designs.tms-monk-management');
    }

    public function tmsPublicNews(): View
    {
        return view('designs.tms-public-news');
    }

    public function tmsNewsAnnouncement(): View
    {
        return view('designs.tms-news-announcement');
    }
}
