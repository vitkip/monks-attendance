<?php

namespace App\Http\Controllers;

use App\Models\Monk;
use Illuminate\Http\Request;

class PublicMonkController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');

        $monks = Monk::where('status', 1)
            ->when($type, fn($q) => $q->where('type', $type))
            ->orderBy('pansa', 'desc')
            ->orderBy('name')
            ->get();

        $monkGroup = $monks->where('type', 'monk')->values();
        $noviceGroup = $monks->where('type', 'novice')->values();

        $totalMonks = Monk::where('status', 1)->where('type', 'monk')->count();
        $totalNovices = Monk::where('status', 1)->where('type', 'novice')->count();

        return view('public.monks.index', compact('monkGroup', 'noviceGroup', 'totalMonks', 'totalNovices', 'type'));
    }
}
