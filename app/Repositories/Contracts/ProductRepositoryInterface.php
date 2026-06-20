<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function paginatePublished(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function paginateForSeller(User $seller, array $filters, int $perPage = 15): LengthAwarePaginator;

    public function findPublished(int $id): ?Product;

    public function findForSeller(int $id, User $seller): ?Product;

    public function create(array $data): Product;

    public function update(Product $product, array $data): Product;

    public function delete(Product $product): void;

    /** @return Collection<int, Product> */
    public function findByIdsForOrder(array $ids): Collection;
}
