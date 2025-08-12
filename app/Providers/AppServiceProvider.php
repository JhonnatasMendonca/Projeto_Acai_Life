<?php

namespace App\Providers;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
// use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Permissao;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
	{
		// Registra dinamicamente todas as permissões do banco de dados
		try {
			foreach (Permissao::all() as $permissao) {
				Gate::define($permissao->nome, function ($user) use ($permissao) {
					Log::info('Verificando permissão', [
						'usuario_id' => $user->id ?? null,
						'perfil_id' => $user->perfil->id ?? null,
						'permissao_id' => $permissao->id,
						'tem_permissao' => $user->perfil && $user->perfil->permissoes->contains('id', $permissao->id)
					]);
					return $user->perfil && $user->perfil->permissoes->contains('id', $permissao->id);
				});
			}
		} catch (\Exception $e) {
			// Evita erro durante migrations ou seeders
		}
	}
}
