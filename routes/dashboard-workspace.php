<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AiWorkspaceController;
use App\Http\Controllers\DocumentsWorkspaceController;
use App\Http\Controllers\ModulePageController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/workspace/recent', [WorkspaceController::class, 'recent'])->name('workspace.recent');
    Route::get('/workspace/tasks', [WorkspaceController::class, 'tasks'])->name('workspace.tasks');
    Route::get('/workspace/actions', [WorkspaceController::class, 'actions'])->name('workspace.actions');
    Route::get('/workspace/shortcuts', [WorkspaceController::class, 'shortcuts'])->name('workspace.shortcuts');

    Route::get('/documents/mine', [DocumentsWorkspaceController::class, 'mine'])->name('documents.mine');
    Route::get('/documents/recent', [DocumentsWorkspaceController::class, 'recent'])->name('documents.recent');
    Route::get('/documents/drafts', [DocumentsWorkspaceController::class, 'drafts'])->name('documents.drafts');
    Route::get('/documents/deleted', [DocumentsWorkspaceController::class, 'deleted'])->name('documents.deleted');
    Route::get('/documents/all', [DocumentsWorkspaceController::class, 'all'])->middleware('admin')->name('documents.all');

    Route::get('/account', [AccountController::class, 'show'])->name('account.show');
    Route::put('/account', [AccountController::class, 'update'])->middleware('throttle:20,10')->name('account.update');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->middleware('throttle:10,10')->name('account.password');

    Route::get('/ai/history', [AiWorkspaceController::class, 'history'])->middleware('capability:can_ai')->name('ai.history');
    Route::get('/ai/quota', [AiWorkspaceController::class, 'quota'])->middleware('capability:can_ai')->name('ai.quota');

    Route::get('/modules/{slug}', [ModulePageController::class, 'show'])
        ->where('slug', '[a-z0-9\-]+')
        ->name('modules.show');
});
