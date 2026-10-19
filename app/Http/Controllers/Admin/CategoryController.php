<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('order')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.form', ['category' => new Category]);
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', compact('category'));
    }

    public function store(Request $request)
    {
        return $this->save($request, new Category);
    }

    public function update(Request $request, Category $category)
    {
        return $this->save($request, $category);
    }

    protected function save(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($category->id)],
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $category->fill([
            'name' => $data['name'],
            'order' => $data['order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);
        if ($category->isDirty('name')) {
            $category->slug = null; // se regenera sola
        }
        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'Categoría guardada correctamente.');
    }

    public function confirmDelete(Category $category)
    {
        return view('admin.categories.confirm-delete', compact('category'));
    }

    public function destroy(Category $category)
    {
        try {
            $category->delete();
        } catch (QueryException $e) {
            return redirect()->route('admin.categories.index')->with('error',
                "No se puede eliminar \"{$category->name}\" porque todavía tiene prendas asignadas. Muévelas a otra categoría primero."
            );
        }

        return redirect()->route('admin.categories.index')->with('success', "Se eliminó la categoría {$category->name}.");
    }
}
