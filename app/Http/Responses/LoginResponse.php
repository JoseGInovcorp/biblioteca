<?php

namespace App\Http\Responses;

use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // Só redireciona se o parâmetro 'redirect' existir e for interno
        if ($request->has('redirect')) {
            $target = $request->get('redirect');

            // Segurança: só permite redirects internos (evita open redirect)
            if (Str::startsWith($target, ['/'])) {
                return redirect()->to($target);
            }
        }

        // Caso contrário, mantém o comportamento padrão
        return redirect()->intended(config('fortify.home', '/dashboard'));
    }
}
