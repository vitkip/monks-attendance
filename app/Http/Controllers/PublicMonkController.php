<?php

namespace App\Http\Controllers;

use App\Models\Monk;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicMonkController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');

        $monks = Monk::where('status', 'active')
            ->select(['id', 'name', 'surname', 'type', 'photo', 'pansa', 'birth_date', 'temple'])
            ->when($type, fn($q) => $q->where('type', $type))
            ->orderBy('pansa', 'desc')
            ->orderBy('name')
            ->get();

        $mapMonk = fn (Monk $monk) => [
            'id'         => $monk->id,
            'full_name'  => $monk->full_name,
            'type'       => $monk->type,
            'type_label' => $monk->type_label,
            'photo_url'  => $monk->photo_url,
            'pansa'      => $monk->pansa,
            'age'        => $monk->age,
            'temple'     => $monk->temple,
        ];

        $monkGroup = $monks->where('type', 'monk')->values()->map($mapMonk)->all();
        $noviceGroup = $monks->where('type', 'novice')->values()->map($mapMonk)->all();
        $nunGroup = $monks->where('type', 'nun')->values()->map($mapMonk)->all();

        if (! $type) {
            $totalMonks = count($monkGroup);
            $totalNovices = count($noviceGroup);
            $totalNuns = count($nunGroup);
        } else {
            $counts = Monk::where('status', 'active')
                ->selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type');
            $totalMonks = (int) ($counts['monk'] ?? 0);
            $totalNovices = (int) ($counts['novice'] ?? 0);
            $totalNuns = (int) ($counts['nun'] ?? 0);
        }

        return Inertia::render('Public/Monks/Index', compact('monkGroup', 'noviceGroup', 'nunGroup', 'totalMonks', 'totalNovices', 'totalNuns', 'type'));
    }
}
