<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    // ... seu código existente ...

    use AuthenticatesUsers;

    // ... seu código existente ...

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ], [
            'required' => 'O campo de :attribute é obrigatório.',
            'string' => 'O campo de :attribute deve ser uma string.',
            'email' => 'O campo de :attribute deve ser um endereço de e-mail válido.'
        ]);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        // Sobrescrevendo o comportamento padrão para uma mensagem personalizada
        throw ValidationException::withMessages([
            $this->username() => ['O e-mail ou a senha estão incorretos.'],
        ]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Se o login for bem-sucedido...
        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }

            return $this->sendLoginResponse($request);
        }

        // Se o login falhar...
        return $this->sendFailedLoginResponse($request);
    }
}
