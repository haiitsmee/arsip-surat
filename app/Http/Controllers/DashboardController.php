<?php

namespace App\Http\Controllers;

use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Basic stats
        $incomingCount = IncomingLetter::count();
        $outgoingCount = OutgoingLetter::count();
        $categoryCount = Category::count();
        $userCount = User::count();

        // Recent letters — combine both incoming and outgoing and take latest 6
        $incoming = IncomingLetter::with('category', 'user')->latest('tanggal_surat')->take(6)->get()->map(function($i){
            $i->type = 'in'; $i->tanggal = $i->tanggal_surat; $i->creator = $i->user; $i->penerima = $i->penerima ?? null; return $i;
        });
        $outgoing = OutgoingLetter::with('category', 'user')->latest('tanggal_surat')->take(6)->get()->map(function($o){
            $o->type = 'out'; $o->tanggal = $o->tanggal_surat; $o->creator = $o->user; $o->penerima = $o->tujuan ?? null; return $o;
        });

        $recent = $incoming->merge($outgoing)
            ->sortByDesc(fn($r) => $r->tanggal ?? $r->created_at)
            ->values()
            ->slice(0, 6);

        return view('dashboard', compact('incomingCount','outgoingCount','categoryCount','userCount','recent'));
    }
}
