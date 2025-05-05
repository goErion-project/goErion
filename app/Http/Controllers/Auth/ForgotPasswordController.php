<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\RequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RecoverPasswordMnemonicRequest;
use App\Http\Requests\Auth\RecoverPasswordPgpRequest;
use App\Http\Requests\Auth\ResetPasswordPgpRequest;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ForgotPasswordController extends Controller
{
    public function showForget(): View
    {
        return view('auth.forgotpassword.forgotpassword');
    }

    public function showMnemonic(): View
    {
        return view('auth.forgotpassword.mnemonicpassword');
    }

    public function showPGP(): View
    {
        return view('auth.forgotpassword.pgppassword');
    }

    /**
     * @throws \SodiumException
     * @throws EnvironmentIsBrokenException
     */
    public function resetMnemonic(RecoverPasswordMnemonicRequest $request): RedirectResponse
    {
        try{
            return $request -> persist();
        } catch (RequestException $e){
            session() -> flash('errormessage', $e -> getMessage());
            return redirect()->back();
        }
    }

    public function sendVerify(RecoverPasswordPgpRequest $request): RedirectResponse
    {
        try{
            return $request -> persist();
        } catch (RequestException $e){
            session() -> flash('errormessage', $e -> getMessage());
            return redirect()->back();
        }
    }

    public function showVerify(): View
    {
        return view('auth.forgotpassword.pgppasswordverify');
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws \SodiumException
     * @throws EnvironmentIsBrokenException
     */
    public function resetPgp(ResetPasswordPgpRequest $request): ?RedirectResponse
    {
        try{
            return $request -> persist();
        } catch (RequestException $e){
            session() -> flash('errormessage', $e -> getMessage());
            return redirect()->back();
        }
    }

}
