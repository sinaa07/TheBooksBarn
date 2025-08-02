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

use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Mail;

Route:: get('/',[HomeController::class,'index'])->name('home');

Route::get('/welcome',function(){
    return Inertia::render('/user/welcome');
});


Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');           // Categories/Index.tsx
    Route::get('/popular', [CategoryController::class, 'popular'])->name('popular'); // Categories/Popular.tsx
    Route::get('/{slug}', [CategoryController::class, 'show'])->name('show');       // Categories/Show.tsx
});

Route::prefix('books')->name('books.')->group(function () {
    Route::get('/', [BookController::class, 'index'])->name('index');           // Books/Index.tsx
    Route::get('/search', [BookController::class, 'search'])->name('search');   // Books/Search.tsx
    Route::get('/featured', [BookController::class, 'featured'])->name('featured'); // Books/Featured.tsx
    Route::get('/bestsellers', [BookController::class, 'bestsellers'])->name('bestsellers'); // Books/Bestsellers.tsx
    Route::get('/latest', [BookController::class, 'latest'])->name('latest');   // Books/Latest.tsx
    Route::get('/author/{author}', [BookController::class, 'byAuthor'])->name('by-author'); // Books/ByAuthor.tsx
    Route::get('/{id}', [BookController::class, 'show'])->name('show');         // Books/Show.tsx
});

Route::get('/faq', function () {
    return Inertia::render('faq');
});

Route::get('/contact', function(){
    return Inertia::render('contact');
});

Route::prefix('cart')->name('cart.')->group(function () {
    Route::patch('/{book}', [CartController::class, 'update'])->name('update');
    Route::post('/{book}', [CartController::class, 'store'])->name('store');
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::delete('/{book}', [CartController::class, 'destroy'])->name('destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/', [CheckoutController::class, 'store'])->name('store');
    });
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/{order}', [PaymentController::class, 'index'])->name('index');
        Route::post('/{order}', [PaymentController::class, 'confirm'])->name('confirm');
    });
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::get('/', [OrderController::class, 'index'])->name('index');
    });
});


Route::prefix('user/address')->name('user.address.')->group(function () {
    Route::get('/', [AddressController::class, 'index'])->name('index');
    Route::get('/create', [AddressController::class, 'create'])->name('create');
    Route::post('/store', [AddressController::class, 'store'])->name('store');
    Route::patch('/{address}', [AddressController::class, 'update'])->name('update');
    Route::delete('/{address}', [AddressController::class, 'destroy'])->name('destroy');
    Route::get('/{address}/edit', [AddressController::class, 'edit'])->name('edit');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/user/dashboard', [UserController::class, 'index'])->name('user.dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
});



if (app()->isLocal()) {
    Route::get('/test-email', function () {
        $order = App\Models\Order::with('user', 'orderItems.book')->latest()->first();
        return new OrderConfirmation($order);
    });
}



require __DIR__.'/settings.php';
require __DIR__.'/auth.php';