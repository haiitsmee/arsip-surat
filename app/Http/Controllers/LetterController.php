<?php

namespace App\Http\Controllers;

use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use App\Models\Category;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class LetterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->type;
        $search = $request->search;
        $perPage = 10;
        $page = $request->get('page', 1);

        // Filter by specific type
        if ($type === 'in') {
            $query = IncomingLetter::with('category', 'user');
            
            if ($search) {
                $query->search($search);
            }
            
            $items = $query->latest('tanggal_surat')->paginate($perPage);

            $items->getCollection()->transform(function ($item) {
                return $this->transformIncoming($item);
            });

            return view('letters.index', ['letters' => $items]);
        }

        if ($type === 'out') {
            $query = OutgoingLetter::with('category', 'user');
            
            if ($search) {
                $query->search($search);
            }
            
            $items = $query->latest('tanggal_surat')->paginate($perPage);

            $items->getCollection()->transform(function ($item) {
                return $this->transformOutgoing($item);
            });

            return view('letters.index', ['letters' => $items]);
        }

        // No type filter: fetch both incoming + outgoing
        $incomingQuery = IncomingLetter::with('category', 'user');
        $outgoingQuery = OutgoingLetter::with('category', 'user');

        if ($search) {
            $incomingQuery->search($search);
            $outgoingQuery->search($search);
        }

        // Get all data and transform
        $incoming = $incomingQuery->get()->map(function ($item) {
            return $this->transformIncoming($item);
        });

        $outgoing = $outgoingQuery->get()->map(function ($item) {
            return $this->transformOutgoing($item);
        });

        // Reindex both collections to avoid primary-key collisions when merging
        // (Eloquent collections are keyed by model primary keys so merging
        // without reindexing can overwrite items when IDs overlap).
        $all = $incoming->values()->concat($outgoing->values())
            ->sortByDesc(function($item) {
                return $item->created_at->timestamp;
            })
            ->values();


        // Manual pagination
        $offset = ($page - 1) * $perPage;
        $items = $all->slice($offset, $perPage)->values();
        
        $paged = new LengthAwarePaginator(
            $items,
            $all->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );
        
        $paged->appends($request->query());

        return view('letters.index', ['letters' => $paged]);
    }

    /**
     * Transform incoming letter for view
     */
    private function transformIncoming($item)
    {
        $item->type = 'in';
        $item->tanggal = $item->tanggal_surat;
        $item->creator = $item->user;
        $item->category_id = $item->kategori_id;
        // Incoming letter has 'pengirim', but view also checks 'penerima' for display
        $item->penerima = null; // incoming doesn't have penerima
        return $item;
    }

    /**
     * Transform outgoing letter for view
     */
    private function transformOutgoing($item)
    {
        $item->type = 'out';
        $item->tanggal = $item->tanggal_surat;
        $item->creator = $item->user;
        $item->category_id = $item->kategori_id;
        $item->penerima = $item->tujuan; // outgoing has tujuan -> map to penerima
        $item->pengirim = null; // outgoing doesn't have pengirim
        return $item;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('letters.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'        => 'required|in:in,out',
            'category_id' => 'required|exists:categories,id',
            'perihal'     => 'required',
            'tanggal'     => 'required|date',
            'no_surat'    => 'nullable|string|max:100',
            'pengirim'    => 'nullable|string|max:255',
            'penerima'    => 'nullable|string|max:255',
            'file'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Upload file jika ada
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('letters', 'public');
        }

        if ($validated['type'] === 'in') {
            $letter = IncomingLetter::create([
                'no_surat'      => $validated['no_surat'],
                'pengirim'      => $validated['pengirim'],
                'tanggal_surat' => $validated['tanggal'],
                'perihal'       => $validated['perihal'],
                'kategori_id'   => $validated['category_id'],
                'file_path'     => $filePath,
                'user_id'       => Auth::id(),
            ]);

            // Log activity
            Log::createLog(Auth::id(), 'created', 'incoming', $letter->id);

        } else { // out
            $letter = OutgoingLetter::create([
                'no_surat'      => $validated['no_surat'],
                'tujuan'        => $validated['penerima'], // penerima -> tujuan
                'tanggal_surat' => $validated['tanggal'],
                'perihal'       => $validated['perihal'],
                'kategori_id'   => $validated['category_id'],
                'file_path'     => $filePath,
                'user_id'       => Auth::id(),
            ]);

            // Log activity
            Log::createLog(Auth::id(), 'created', 'outgoing', $letter->id);
        }

        return redirect()->route('letters.index')
                         ->with('success', 'Surat berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Try incoming first
        $model = IncomingLetter::with('category', 'user')->find($id);
        
        if ($model) {
            $model = $this->transformIncoming($model);
            return view('letters.show', ['letter' => $model]);
        }

        // Try outgoing
        $model = OutgoingLetter::with('category', 'user')->find($id);
        
        if ($model) {
            $model = $this->transformOutgoing($model);
            return view('letters.show', ['letter' => $model]);
        }

        return back()->with('error', 'Surat tidak ditemukan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $categories = Category::all();

        // Try incoming first
        $model = IncomingLetter::with('category', 'user')->find($id);
        
        if ($model) {
            $model = $this->transformIncoming($model);
            return view('letters.edit', ['letter' => $model, 'categories' => $categories]);
        }

        // Try outgoing
        $model = OutgoingLetter::with('category', 'user')->find($id);
        
        if ($model) {
            $model = $this->transformOutgoing($model);
            return view('letters.edit', ['letter' => $model, 'categories' => $categories]);
        }

        return back()->with('error', 'Surat tidak ditemukan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'type'        => 'required|in:in,out',
            'category_id' => 'required|exists:categories,id',
            'perihal'     => 'required',
            'tanggal'     => 'required|date',
            'no_surat'    => 'nullable|string|max:100',
            'pengirim'    => 'nullable|string|max:255',
            'penerima'    => 'nullable|string|max:255',
            'file'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Find the letter
        $model = IncomingLetter::find($id);
        $isIncoming = true;
        
        if (!$model) {
            $model = OutgoingLetter::find($id);
            $isIncoming = false;
        }

        if (!$model) {
            return back()->with('error', 'Surat tidak ditemukan.');
        }

        $filePath = $model->file_path;

        // Handle file upload
        if ($request->hasFile('file')) {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('file')->store('letters', 'public');
        }

        // Update based on type
        if ($validated['type'] === 'in' && $isIncoming) {
            $model->update([
                'kategori_id'   => $validated['category_id'],
                'no_surat'      => $validated['no_surat'],
                'pengirim'      => $validated['pengirim'],
                'tanggal_surat' => $validated['tanggal'],
                'perihal'       => $validated['perihal'],
                'file_path'     => $filePath,
            ]);

            Log::createLog(Auth::id(), 'updated', 'incoming', $model->id);

        } elseif ($validated['type'] === 'out' && !$isIncoming) {
            $model->update([
                'kategori_id'   => $validated['category_id'],
                'no_surat'      => $validated['no_surat'],
                'tujuan'        => $validated['penerima'],
                'tanggal_surat' => $validated['tanggal'],
                'perihal'       => $validated['perihal'],
                'file_path'     => $filePath,
            ]);

            Log::createLog(Auth::id(), 'updated', 'outgoing', $model->id);

        } else {
            return back()->with('error', 'Tipe surat tidak sesuai dengan data yang ada.');
        }

        return redirect()->route('letters.index')
                         ->with('success', 'Surat berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $model = IncomingLetter::find($id);
        $isIncoming = true;
        
        if (!$model) {
            $model = OutgoingLetter::find($id);
            $isIncoming = false;
        }

        if (!$model) {
            return back()->with('error', 'Surat tidak ditemukan.');
        }

        // Delete file if exists
        if ($model->file_path && Storage::disk('public')->exists($model->file_path)) {
            Storage::disk('public')->delete($model->file_path);
        }

        // Log before delete
        Log::createLog(
            Auth::id(), 
            'deleted', 
            $isIncoming ? 'incoming' : 'outgoing', 
            $model->id
        );

        $model->delete();

        return redirect()->route('letters.index')
                         ->with('success', 'Surat berhasil dihapus.');
    }

    /**
     * Download file surat.
     */
    public function download($id)
    {
        $model = IncomingLetter::find($id);
        $isIncoming = true;
        
        if (!$model) {
            $model = OutgoingLetter::find($id);
            $isIncoming = false;
        }

        if (!$model || !$model->file_path || !Storage::disk('public')->exists($model->file_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // Log download
        Log::createLog(
            Auth::id(), 
            'downloaded', 
            $isIncoming ? 'incoming' : 'outgoing', 
            $model->id
        );

        return response()->download(
            Storage::disk('public')->path($model->file_path),
            basename($model->file_path)
        );
    }
}