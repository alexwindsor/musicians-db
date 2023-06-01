<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MusicianController;

Route::get('/', [MusicianController::class, 'index']);
Route::get('add_musician', [MusicianController::class, 'add']);
Route::post('/add_musician', [MusicianController::class, 'store']);
Route::get('edit_musician/{musician:id}', [MusicianController::class, 'edit']);
Route::put('update_musician/{musician:id}', [MusicianController::class, 'update']);
Route::delete('/delete_musician/{musician:id}', [MusicianController::class, 'destroy']);
// Route::get('/pdf', [MusicianController::class, 'index'])->defaults('pdf', true);

// // ajax routes
Route::post('/get_profile/{profile:id}', [MusicianController::class, 'getProfile']);
Route::delete('/delete_musician_detail/{musician_details_id}', [MusicianController::class, 'destroy_musician_detail']);


