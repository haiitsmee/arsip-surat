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

        // Recent letters — fetch latest up to 10 from each table and
        // combine them then pick the 10 most recent by timestamp (created_at)
        // This avoids the previous bias when one type dominated the top slots.
        $incoming = IncomingLetter::with('category', 'user')->latest('created_at')->take(10)->get()->map(function($i){
            $i->type = 'in'; $i->tanggal = $i->tanggal_surat; $i->creator = $i->user; $i->penerima = $i->penerima ?? null; return $i;
        });
        $outgoing = OutgoingLetter::with('category', 'user')->latest('created_at')->take(10)->get()->map(function($o){
            $o->type = 'out'; $o->tanggal = $o->tanggal_surat; $o->creator = $o->user; $o->penerima = $o->tujuan ?? null; return $o;
        });

        // Reindex both collections before combining to avoid key collisions
        // (Eloquent collections preserve model primary keys which can overlap
        // across different tables and cause items to be overwritten).
        // Sort by created_at timestamp to get true latest items across both
        // tables, then take the top 10.
        $recent = $incoming->values()->concat($outgoing->values())
            ->sortByDesc(fn($r) => $r->created_at->timestamp)
            ->values()
            ->slice(0, 10);

        return view('dashboard', compact('incomingCount','outgoingCount','categoryCount','userCount','recent'));
    }
}
