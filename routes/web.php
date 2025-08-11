<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Mail;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/welcome', function(){
    return Inertia::render('Welcome');
});

Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/popular', [CategoryController::class, 'popular'])->name('popular');
    Route::get('/{slug}', [CategoryController::class, 'show'])->name('show');
});

Route::prefix('books')->name('books.')->group(function () {
    Route::get('/', [BookController::class, 'index'])->name('index');
    Route::get('/search', [BookController::class, 'search'])->name('search');
    Route::get('/featured', [BookController::class, 'featured'])->name('featured');
    Route::get('/bestsellers', [BookController::class, 'bestsellers'])->name('bestsellers');
    Route::get('/latest', [BookController::class, 'latest'])->name('latest');
    Route::get('/author/{author}', [BookController::class, 'byAuthor'])->name('by-author');
    Route::get('/{id}', [BookController::class, 'show'])->name('show');
});

Route::get('/faq', function () {
    return Inertia::render('FAQ');
});

Route::get('/contact', function(){
    return Inertia::render('Contact');
});

// Cart routes (authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::patch('/update/{itemId}', [CartController::class, 'update'])->name('update');
        Route::delete('/remove/{itemId}', [CartController::class, 'remove'])->name('remove');
        Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
        Route::get('/count', [CartController::class, 'count'])->name('count');
    });
});

// Authenticated user routes
Route::middleware(['auth'])->group(function () {

    // User addresses
    Route::prefix('addresses')->name('addresses.')->group(function () {
        Route::get('/', [AddressController::class, 'index'])->name('index');
        Route::get('/create', [AddressController::class, 'create'])->name('create');
        Route::post('/', [AddressController::class, 'store'])->name('store');
        Route::get('/{address}', [AddressController::class, 'show'])->name('show');
        Route::get('/{address}/edit', [AddressController::class, 'edit'])->name('edit');
        Route::patch('/{address}', [AddressController::class, 'update'])->name('update');
        Route::delete('/{address}', [AddressController::class, 'destroy'])->name('destroy');
        Route::patch('/{address}/set-default', [AddressController::class, 'setDefault'])->name('set-default');
        Route::get('/by-type', [AddressController::class, 'getByType'])->name('by-type');
        Route::get('/default', [AddressController::class, 'getDefault'])->name('default');
    });

    // User profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/show', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
    
    // User orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::post('/{order}/reorder', [OrderController::class, 'reorder'])->name('reorder');
        Route::get('/{order}/success', [OrderController::class, 'success'])->name('success');
        Route::get('/track/{orderNumber}', [OrderController::class, 'track'])->name('track');
    });
});

// Checkout and payment (verified users only), add verified later,
Route::middleware(['auth'])->group(function () {
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/', [CheckoutController::class, 'store'])->name('store');
        Route::post('/validate-stock', [CheckoutController::class, 'validateStock'])->name('validate-stock');
        Route::post('/calculate-shipping', [CheckoutController::class, 'calculateShipping'])->name('calculate-shipping');
    });
    
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/{order}/{payment}', [PaymentController::class, 'process'])->name('process');
        Route::post('/{order}', [PaymentController::class, 'confirm'])->name('confirm');
    });
    
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/success/{order}', [OrderController::class, 'success'])->name('success');
    });
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('dashboard');

    // Admin user management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::patch('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{user}/orders', [UserController::class, 'orders'])->name('orders');
        Route::get('/{user}/addresses', [UserController::class, 'addresses'])->name('addresses');
        Route::get('/export', [UserController::class, 'export'])->name('export');
    });
});

// Development/testing route
if (app()->isLocal()) {
    Route::get('/test-email', function () {
        $order = App\Models\Order::with('user', 'orderItems.book')->latest()->first();
        return new OrderConfirmation($order);
    });
}

// Include auth and settings routes
require __DIR__.'/auth.php';
require __DIR__.'/settings.php';