<?php

use App\Http\Controllers\Api\V1\ProjectController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
	->as('api.v1.')
	->group(function (): void {
		Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
		Route::get('/projects/{project:slug}', [ProjectController::class, 'show'])->name('projects.show');
	});
