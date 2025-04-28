<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignUpRequest;
use App\Marketplace\Utility\Captcha;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class RegisterController extends Controller
{
    public function showSignUp($refid = null): View
    {
        return view('auth.signup')->with([
            'refid' => $refid,
            'captcha' => Captcha::Build(),
        ]);
    }

    /**
     * @param SignUpRequest $request
     * @return RedirectResponse
     * Try to complete SignUpRequest if success redirects to mnemonic
     * if fail redirects back
     */

    public function signUpPost(SignUpRequest $request): RedirectResponse
    {
        try {
            $request->persist();
            return redirect()->route('auth.mnemonic');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * If there is mnemonic_key in session, show it to User if not
     * redirect to signin page
     *
     */
    public function showMnemonic(): \Illuminate\Contracts\View\View|Application|Factory|RedirectResponse
    {
        if (!session()->has('mnemonic_key'))
            return redirect()->route('auth.signin');
        return view('auth.mnemonic')->with([
            'mnemonic',
            session()->get('mnemonic_key')
        ]);
    }
}
