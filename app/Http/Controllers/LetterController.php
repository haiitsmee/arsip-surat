<?php

namespace App\Http\Controllers;

use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use App\Models\Category;
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

        // If a specific type is requested, query that model and paginate normally
        if ($type === 'in') {
            $query = IncomingLetter::with('category', 'user');
            if ($search) $query = $query->search($search);
            $items = $query->latest('tanggal_surat')->paginate(10);

            $items->getCollection()->transform(function ($item) {
                $item->type = 'in';
                $item->tanggal = $item->tanggal_surat; // view expects `tanggal`
                $item->creator = $item->user;
                $item->category_id = $item->kategori_id ?? null;
                $item->penerima = $item->penerima ?? null;
                return $item;
            });

            return view('letters.index', ['letters' => $items]);
        }

        if ($type === 'out') {
            $query = OutgoingLetter::with('category', 'user');
            if ($search) $query = $query->search($search);
            $items = $query->latest('tanggal_surat')->paginate(10);

            $items->getCollection()->transform(function ($item) {
                $item->type = 'out';
                $item->tanggal = $item->tanggal_surat;
                $item->creator = $item->user;
                $item->category_id = $item->kategori_id ?? null;
                $item->penerima = $item->tujuan ?? null;
                return $item;
            });

            return view('letters.index', ['letters' => $items]);
        }

        // No type filter: fetch both incoming + outgoing, merge & paginate manually
        $inq = IncomingLetter::with('category', 'user');
        $out = OutgoingLetter::with('category', 'user');

        if ($search) {
            $inq = $inq->search($search);
            $out = $out->search($search);
        }

        $incoming = $inq->get()->map(function ($item) {
            $item->type = 'in';
            $item->tanggal = $item->tanggal_surat;
            $item->creator = $item->user;
            $item->category_id = $item->kategori_id ?? null;
            $item->penerima = $item->penerima ?? null;
            return $item;
        });

        $outgoing = $out->get()->map(function ($item) {
            $item->type = 'out';
            $item->tanggal = $item->tanggal_surat;
            $item->creator = $item->user;
            $item->category_id = $item->kategori_id ?? null;
            $item->penerima = $item->tujuan ?? null;
            return $item;
        });

        $all = $incoming->merge($outgoing)->sortByDesc(function ($i) {
            return $i->tanggal ?? $i->created_at;
        })->values();

        $page = $request->get('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $paged = new LengthAwarePaginator(
            $all->slice($offset, $perPage)->values(),
            $all->count(),
            $perPage,
            $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        return view('letters.index', ['letters' => $paged]);
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
        $request->validate([
            'type'        => 'required|in:in,out',
            'category_id' => 'required|exists:categories,id',
            'perihal'     => 'required',
            'tanggal'     => 'required|date',
            'file'        => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Upload file jika ada
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('letters', 'public');
        }

        if ($request->type === 'in') {
            IncomingLetter::create([
                'no_surat'      => $request->no_surat,
                'pengirim'      => $request->pengirim,
                'tanggal_surat' => $request->tanggal,
                'perihal'       => $request->perihal,
                'kategori_id'   => $request->category_id,
                'file_path'     => $filePath,
                'user_id'       => Auth::id(),
            ]);
        } else { // out
            OutgoingLetter::create([
                'no_surat'      => $request->no_surat,
                'tujuan'        => $request->penerima,
                'tanggal_surat' => $request->tanggal,
                'perihal'       => $request->perihal,
                'kategori_id'   => $request->category_id,
                'file_path'     => $filePath,
                'user_id'       => Auth::id(),
            ]);
        }

        return redirect()->route('letters.index')
                         ->with('success', 'Surat berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $model = IncomingLetter::with('category', 'user')->find($id);
        $type = 'in';
        if (!$model) {
            $model = OutgoingLetter::with('category', 'user')->find($id);
            $type = 'out';
        }

        if (!$model) {
            return back()->with('error', 'Surat tidak ditemukan.');
        }

        $model->type = $type;
        $model->tanggal = $model->tanggal_surat;
        $model->category_id = $model->kategori_id ?? null;
        $model->setRelation('creator', $model->user);
        if ($type === 'out') $model->penerima = $model->tujuan ?? null;

        return view('letters.show', ['letter' => $model]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $categories = Category::all();

        $model = IncomingLetter::with('category', 'user')->find($id);
        $type = 'in';
        if (!$model) {
            $model = OutgoingLetter::with('category', 'user')->find($id);
            $type = 'out';
        }

        if (!$model) return back()->with('error', 'Surat tidak ditemukan.');

        $model->type = $type;
        $model->tanggal = $model->tanggal_surat;
        $model->category_id = $model->kategori_id ?? null;
        $model->setRelation('creator', $model->user);
        if ($type === 'out') $model->penerima = $model->tujuan ?? null;

        return view('letters.edit', compact('letter', 'categories'))->with('letter', $model);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'type'        => 'required|in:in,out',
            'category_id' => 'required|exists:categories,id',
            'perihal'     => 'required',
            'tanggal'     => 'required|date',
            'file'        => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $model = IncomingLetter::find($id) ?? OutgoingLetter::find($id);
        if (!$model) return back()->with('error', 'Surat tidak ditemukan.');

        $filePath = $model->file_path;

        if ($request->hasFile('file')) {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('file')->store('letters', 'public');
        }

        if ($request->type === 'in' && $model instanceof IncomingLetter) {
            $model->update([
                'kategori_id'   => $request->category_id,
                'no_surat'      => $request->no_surat,
                'pengirim'      => $request->pengirim,
                'tanggal_surat' => $request->tanggal,
                'perihal'       => $request->perihal,
                'file_path'     => $filePath,
            ]);

        } elseif ($request->type === 'out' && $model instanceof OutgoingLetter) {
            $model->update([
                'kategori_id'   => $request->category_id,
                'no_surat'      => $request->no_surat,
                'tujuan'        => $request->penerima,
                'tanggal_surat' => $request->tanggal,
                'perihal'       => $request->perihal,
                'file_path'     => $filePath,
            ]);

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
        $model = IncomingLetter::find($id) ?? OutgoingLetter::find($id);
        if (!$model) return back()->with('error', 'Surat tidak ditemukan.');

        if ($model->file_path && Storage::disk('public')->exists($model->file_path)) {
            Storage::disk('public')->delete($model->file_path);
        }

        $model->delete();

        return redirect()->route('letters.index')
                         ->with('success', 'Surat berhasil dihapus.');
    }

    /**
     * Download file surat.
     */
    public function download($id)
    {
        $model = IncomingLetter::find($id) ?? OutgoingLetter::find($id);
        if (!$model || !$model->file_path || !Storage::disk('public')->exists($model->file_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        return response()->download(Storage::disk('public')->path($model->file_path), basename($model->file_path));
    }
}
