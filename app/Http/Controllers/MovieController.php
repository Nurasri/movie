<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class MovieController extends Controller
{
    public function index()
    {
        $query = Movie::latest();

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%$search%")
                  ->orWhere('sinopsis', 'like', "%$search%");
            });
        }

        $movies = $query->paginate(6)->withQueryString();
        return view('homepage', compact('movies'));
    }

    public function detail($id)
    {
        $movie = Movie::findOrFail($id);
        return view('detail', compact('movie'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('input', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->validateRequest($request, true);

        $fileName = $this->handleFileUpload($request);

        Movie::create(array_merge($request->except('foto_sampul'), [
            'foto_sampul' => $fileName,
        ]));

        return redirect('/')->with('success', 'Data berhasil disimpan');
    }

    public function data()
    {
        $movies = Movie::latest()->paginate(10);
        return view('data-movies', compact('movies'));
    }

    public function form_edit($id)
    {
        $movie = Movie::findOrFail($id);
        $categories = Category::all();
        return view('form-edit', compact('movie', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $this->validateRequest($request);

        $movie = Movie::findOrFail($id);
        $data = $request->except('foto_sampul');

        if ($request->hasFile('foto_sampul')) {
            $this->deleteOldImage($movie->foto_sampul);
            $data['foto_sampul'] = $this->handleFileUpload($request);
        }

        $movie->update($data);

        return redirect('/movies/data')->with('success', 'Data berhasil diperbarui');
    }

    public function delete($id)
    {
        $movie = Movie::findOrFail($id);
        $this->deleteOldImage($movie->foto_sampul);
        $movie->delete();

        return redirect('/movies/data')->with('success', 'Data berhasil dihapus');
    }

    // ========== Helper Function ==========

    private function validateRequest(Request $request, $isCreate = false)
    {
        $rules = [
            'judul' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'sinopsis' => 'required|string',
            'tahun' => 'required|integer',
            'pemain' => 'required|string',
        ];

        if ($isCreate) {
            $rules['id'] = ['required', 'string', 'max:255', Rule::unique('movies', 'id')];
            $rules['foto_sampul'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        } else {
            $rules['foto_sampul'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        $request->validate($rules);
    }

    private function handleFileUpload(Request $request): string
    {
        $file = $request->file('foto_sampul');
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('images'), $fileName);
        return $fileName;
    }

    private function deleteOldImage($fileName)
    {
        $path = public_path('images/' . $fileName);
        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
