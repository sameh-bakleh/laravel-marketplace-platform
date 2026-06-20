<?php

use App\Http\Controllers\Api\Mobile\AuthController as MobileAuthController;
use App\Http\Controllers\Api\Mobile\FavoriteController as MobileFavoriteController;
use App\Http\Controllers\Api\Mobile\ProductController as MobileProductController;
use App\Http\Controllers\Api\V1\Admin\CategoryAdminController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\FavoriteController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Api\V1\Seller\ProductImageController;
use App\Http\Controllers\Api\V1\SellerProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile client contract (ios-marketplace-product-app)
|--------------------------------------------------------------------------
| Unversioned /api/* routes expected by the SwiftUI portfolio client.
*/
Route::post('login', [MobileAuthController::class, 'login']);

Route::middleware('auth:api')->group(function (): void {
    Route::get('products', [MobileProductController::class, 'index']);
    Route::get('products/{product}', [MobileProductController::class, 'show']);

    Route::get('favorites', [MobileFavoriteController::class, 'index']);
    Route::post('favorites', [MobileFavoriteController::class, 'store']);
    Route::delete('favorites/{product}', [MobileFavoriteController::class, 'destroy']);
});

Route::prefix('v1')->group(function (): void {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{product}', [ProductController::class, 'show']);
    Route::get('sellers/{slug}', [SellerProfileController::class, 'show']);

    Route::middleware('auth:api')->group(function (): void {
        Route::post('auth/refresh', [AuthController::class, 'refresh']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::get('favorites', [FavoriteController::class, 'index']);
        Route::post('favorites', [FavoriteController::class, 'store']);
        Route::delete('favorites/{product}', [FavoriteController::class, 'destroy']);

        Route::get('cart', [CartController::class, 'show']);
        Route::post('cart/items', [CartController::class, 'store']);
        Route::patch('cart/items/{product}', [CartController::class, 'update']);
        Route::delete('cart/items/{product}', [CartController::class, 'destroy']);
        Route::post('cart/checkout', [CartController::class, 'checkout']);

        Route::get('orders', [OrderController::class, 'index']);
        Route::post('orders', [OrderController::class, 'store']);
        Route::get('orders/{order}', [OrderController::class, 'show']);
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']);

        Route::get('notifications', [NotificationController::class, 'index']);
        Route::post('notifications/read-all', [NotificationController::class, 'markAllRead']);
        Route::post('notifications/{notification}/read', [NotificationController::class, 'markRead']);

        Route::middleware('role:seller|admin')->group(function (): void {
            Route::get('seller/profile', [SellerProfileController::class, 'me']);
            Route::patch('seller/profile', [SellerProfileController::class, 'updateMe']);
            Route::get('seller/orders', [OrderController::class, 'sellerIndex']);
            Route::apiResource('seller/products', SellerProductController::class);
            Route::post('seller/products/{product}/images', [ProductImageController::class, 'store']);
            Route::delete('seller/products/{product}/images/{image}', [ProductImageController::class, 'destroy']);
        });

        Route::middleware('role:admin')->group(function (): void {
            Route::get('admin/categories', [CategoryAdminController::class, 'index']);
            Route::post('admin/categories', [CategoryAdminController::class, 'store']);
            Route::patch('admin/categories/{category}', [CategoryAdminController::class, 'update']);
            Route::delete('admin/categories/{category}', [CategoryAdminController::class, 'destroy']);
        });
    });
});
