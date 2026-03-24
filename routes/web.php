<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\DepartmentListMiddleware;
use App\Http\Middleware\DepartmentMiddleware;
use App\Http\Middleware\IndicatorListMiddleware;
use App\Http\Middleware\IndicatorMiddleware;
use App\Http\Middleware\UserListMiddleware;
use App\Http\Middleware\UserMiddleware;
use Illuminate\Support\Facades\Route;

// Rotas apenas para usuários não autenticados
Route::middleware(['guest'])->group(function () {

    // Login
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginForm'])->name('loginForm');

    // Email sent
    Route::get('/email_enviado', [AuthController::class, 'emailSent'])->name('emailSent');

    // New user confirmation
    Route::get('/confirmar_cadastro/{token}', [AuthController::class, 'newUserConfirmation'])->name('user.confirmation');

    // Forgot password
    Route::get('/esqueci_a_senha', [AuthController::class, 'forgotPassword'])->name('forgotPassword');
    Route::post('/esqueci_a_senha', [AuthController::class, 'forgotPasswordForm'])->name('forgotPasswordForm');

    // Reset password
    Route::get('/redefinir_senha/{token}', [AuthController::class, 'resetPassword'])->name('resetPassword');
    Route::post('/redefinir_senha', [AuthController::class, 'resetPasswordForm'])->name('resetPasswordForm');
});

// Rotas apenas para usuários autenticados
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [AppController::class, 'index'])->name('home');

    // Departamentos
    Route::middleware([DepartmentMiddleware::class])->group(function () {
        Route::get('/departamentos', [DepartmentController::class, 'index'])->name('departments');
    });

    Route::middleware([DepartmentListMiddleware::class])->group(function () {
        Route::get('/departamentos/lista', [DepartmentController::class, 'list_all'])->name('departments.list_all');
        Route::post('/departamentos', [DepartmentController::class, 'store'])->name('departments.store');
        Route::get('/departamentos/{id}/editar', [DepartmentController::class, 'edit'])->name('departments.edit'); // JSON para o modal
        Route::put('/departamentos/{id}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departamentos/{id}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
        Route::patch('/departamentos/{id}/mudarStatus', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle');
    });

    // Usuarios
    Route::middleware([UserMiddleware::class])->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('users');
    });

    Route::middleware([UserListMiddleware::class])->group(function () {
        Route::get('/usuarios/lista', [UserController::class, 'list_all'])->name('users.list_all');
        Route::post('/usuarios', [UserController::class, 'store'])->name('users.store');
        Route::get('/usuarios/{id}/editar', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/usuarios/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/usuarios/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/usuarios/{id}/mudarStatus', [UserController::class, 'toggleStatus'])->name('users.toggle');
        Route::patch('/usuarios/{id}/restaurar', [UserController::class, 'restore'])->name('users.restore');
        Route::get('/usuarios/{id}/listar', [UserController::class, 'list'])->name('users.list');
    });

    // Usuários -> Permissões
    // Permissões
    Route::get('/usuarios/{id}/permissoes', [PermissionController::class, 'getUserPermissions'])->name('users.permissions.get');
    Route::post('/usuarios/{id}/permissoes', [PermissionController::class, 'updateUserPermissions'])->name('users.permissions.update');

    Route::middleware([IndicatorMiddleware::class])->group(function () {
        Route::get('/indicadores/{id}', [IndicatorController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('indicators.show');
    });

    Route::middleware([IndicatorListMiddleware::class])->group(function () {
        Route::get('/indicadores/lista', [IndicatorController::class, 'list_all'])->name('indicators.list_all');
        Route::get('/indicadores/{id}/listar', [IndicatorController::class, 'list'])->name('indicators.list');
        Route::get('/indicadores/{id}/stats', [IndicatorController::class, 'getStats'])->name('indicators.stats');
        Route::get('/indicadores/{id}/chart-data', [IndicatorController::class, 'getChartData'])->name('indicators.chart-data');
        Route::post('/indicadores', [IndicatorController::class, 'store'])->name('indicators.store');
        Route::get('/indicadores/{id}/editar', [IndicatorController::class, 'edit'])->name('indicators.edit');
        Route::put('/indicadores/{id}', [IndicatorController::class, 'update'])->name('indicators.update');
        Route::delete('/indicadores/{id}', [IndicatorController::class, 'destroy'])->name('indicators.destroy');
        Route::patch('/indicadores/{id}/mudarStatus', [IndicatorController::class, 'toggleStatus'])->name('indicators.toggle');

        // Indicadores -> Valores
        Route::get('/indicadores/valores/{id}/lista', [IndicatorController::class, 'list_all_values'])->name('indicators.values.list_all');
        Route::post('/indicadores/{id}/valores', [IndicatorController::class, 'storeValue'])->name('indicators.values.store');
        Route::get('/indicadores/valores/{id}/editar', [IndicatorController::class, 'editValue'])->name('indicators.values.edit');
        Route::put('/indicadores/valores/{id}', [IndicatorController::class, 'updateValue'])->name('indicators.values.update');
        Route::delete('/indicadores/valores/{id}', [IndicatorController::class, 'destroyValue'])->name('indicators.values.destroy');

        // Indicadores -> Análises
        Route::get('/indicadores/analises/{id}/lista', [IndicatorController::class, 'list_all_analyses'])->name('indicators.analyses.list_all');
        Route::post('/indicadores/{id}/analises', [IndicatorController::class, 'storeAnalysis'])->name('indicators.analyses.store');
        Route::get('/indicadores/analises/{id}/editar', [IndicatorController::class, 'editAnalysis'])->name('indicators.analyses.edit');
        Route::put('/indicadores/analises/{id}', [IndicatorController::class, 'updateAnalysis'])->name('indicators.analyses.update');
        Route::delete('/indicadores/analises/{id}', [IndicatorController::class, 'destroyAnalysis'])->name('indicators.analyses.destroy');

        // Indicadores -> Análises -> Planos de Ação
        Route::get('/indicadores/analises/{id}/planos', [IndicatorController::class, 'listActionPlans'])->name('indicators.actionPlans.list');
        Route::post('/indicadores/analises/{id}/planos', [IndicatorController::class, 'storeActionPlan'])->name('indicators.actionPlans.store');
        Route::get('/indicadores/planos/{id}/editar', [IndicatorController::class, 'editActionPlan'])->name('indicators.actionPlans.edit');
        Route::put('/indicadores/planos/{id}', [IndicatorController::class, 'updateActionPlan'])->name('indicators.actionPlans.update');
        Route::delete('/indicadores/planos/{id}', [IndicatorController::class, 'destroyActionPlan'])->name('indicators.actionPlans.destroy');
    });

    // Profile
    Route::get('/perfil', [ProfileController::class, 'profile'])->name('profile');
    Route::post('/perfil', [ProfileController::class, 'profileForm'])->name('profileForm');
    Route::post('/deletar_perfil', [ProfileController::class, 'deleteProfileForm'])->name('deleteProfileForm');

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});


// Fallback de 404
// Route::fallback(function () {
//     return response()
//         ->view('errors.404', [
//             'title' => '404'
//         ], 404);
// });
