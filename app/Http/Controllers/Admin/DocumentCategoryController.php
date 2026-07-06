<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentCategoryController extends Controller
{
    public function index()
    {
        $categories = DocumentCategory::orderBy('nama')->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:document_categories',
            'deskripsi' => 'nullable|string',
            'warna' => 'nullable|string|max:7',
            'ikon' => 'nullable|string|max:50',
        ]);

        DocumentCategory::create([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
            'deskripsi' => $request->deskripsi,
            'warna' => $request->warna ?? '#6B7280',
            'ikon' => $request->ikon ?? 'file',
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(DocumentCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, DocumentCategory $category)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:document_categories,nama,' . $category->id,
            'deskripsi' => 'nullable|string',
            'warna' => 'nullable|string|max:7',
            'ikon' => 'nullable|string|max:50',
        ]);

        $category->update([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
            'deskripsi' => $request->deskripsi,
            'warna' => $request->warna ?? '#6B7280',
            'ikon' => $request->ikon ?? 'file',
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(DocumentCategory $category)
    {
        if ($category->documents()->exists()) {
            return back()->with('error', 'Cannot delete category as it is associated with documents.');
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
