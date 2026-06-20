<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryAdminController extends Controller
{
    public function __construct(
        private readonly CategoryService $categories,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection($this->categories->all());
    }

    public function store(Request $request): CategoryResource
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $category = $this->categories->create($data);

        return new CategoryResource($category);
    }

    public function update(Request $request, int $category): CategoryResource
    {
        $model = Category::query()->whereKey($category)->first();
        if (! $model) {
            throw new NotFoundHttpException;
        }

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:categories,slug,'.$model->id],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        return new CategoryResource($this->categories->update($model, $data));
    }

    public function destroy(int $category): Response
    {
        $model = Category::query()->whereKey($category)->first();
        if (! $model) {
            throw new NotFoundHttpException;
        }

        $this->categories->delete($model);

        return response()->noContent();
    }
}
