<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MusicianController;

Route::get('/', [MusicianController::class, 'index']);
Route::get('/add_musician', [MusicianController::class, 'add']);
Route::post('/add_musician', [MusicianController::class, 'store']);
Route::post('/delete_musician/{musician:id}', [MusicianController::class, 'destroy']);
Route::get('/edit_musician/{musician:id}', [MusicianController::class, 'edit']);
Route::post('/delete_musician_detail', [MusicianController::class, 'destroy_musician_detail']);

Route::post('/update_musician/{musician:id}', [MusicianController::class, 'update']);
Route::post('/remove_musician_instrument', [MusicianController::class, 'removeMusicianInstrument']);
Route::post('/add_musician_instrument', [MusicianController::class, 'addMusicianInstrument']);

Route::post('/get_profile/{profile:id}', [MusicianController::class, 'getProfile']);

Route::get('/pdf', [MusicianController::class, 'index'])->defaults('pdf', true);


