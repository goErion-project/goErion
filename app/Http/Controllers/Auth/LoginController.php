<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\RequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignInRequest;
use App\Http\Requests\Auth\VerifySinginRequest;
use App\Marketplace\Utility\Captcha;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class LoginController extends Controller
{
    public function showSignIn(): View
    {
        return view('auth.signin')->with([
            'captcha'=> Captcha::Build()
        ]);
    }

    /**
     * @throws \Exception
     */
    public function postSignIn(SignInRequest $request): RedirectResponse
    {
        try {
            return $request->persist();
        }catch (RequestException $e)
        {
            session()->flash('errormessage', $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * @return RedirectResponse
     */
    public function postSignOut(): RedirectResponse
    {
        auth()->logout();
        session()->flush();
        return redirect()->route('home')
            ->with('success_message', 'You have been logged out');
    }

    public function showVerify(): \Illuminate\Contracts\View\View|Application|Factory
    {
        return view('auth.verify');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function postVerify(VerifySinginRequest $request): RedirectResponse
    {
        try {
            return $request->persist();
        }   catch (RequestException $exception)
        {
            session()->flash('errormessage', $exception->getMessage());
            return redirect()->back();
        }
    }
}
