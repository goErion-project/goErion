<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\RequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BanUserRequest;
use App\Http\Requests\Admin\ChangeBasicInfoRequest;
use App\Http\Requests\Admin\ChangeUserGroupRequest;
use App\Http\Requests\Admin\DisplayUsersRequest;
use App\Models\Ban;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct() {
        $this->middleware('admin_panel_access');
    }

    /**
     * Checks the gate and returns 403 if not
     */
    private function checkGate(): void
    {
        if(Gate::denies('has-access', 'users'))
            abort(403);
    }

    public function users(DisplayUsersRequest $request): View
    {
        $this -> checkGate();

        $request->persist();
        $users = $request->getUsers();
        return view('admin.users')->with([
            'users' => $users
        ]);
    }

    public function usersPost(Request $request): RedirectResponse
    {
        $this -> checkGate();


        return redirect()->route('admin.users',[
            'order_by' => $request->get('order_by'),
            'display_group' => $request->get('display_group'),
            'username' => $request -> get('username')
        ]);
    }

    public function userView(User $user = null): View
    {
        $this -> checkGate();

        return view('admin.user')->with([
            'user' => $user
        ]);
    }

    /**
     * @throws RequestException
     * @throws \Throwable
     */
    public function editUserGroup(User $user,ChangeUserGroupRequest $request): RedirectResponse
    {
        $this -> checkGate();

        try{
            $request->persist($user);
        } catch(RequestException $e){
            session()->flash('error',$e->getMessage());
            return redirect()->back();
        }
        return redirect()->back();
    }

    public function editBasicInfo(User $user,ChangeBasicInfoRequest $request): RedirectResponse
    {
        $this -> checkGate();

        try{
            $request->persist($user);
        } catch(RequestException $e){
            session()->flash('error',$e->getMessage());
            return redirect()->back();
        }
        return redirect()->back();
    }

    /**
     * POST ban request
     *
     * @param User $user
     * @param BanUserRequest $request
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function banUser(User $user, BanUserRequest $request): RedirectResponse
    {
        $this->checkGate();

        try{
            throw_if(auth()->user()->id==$user->id, new RequestException('You cannot ban yourself!'));
            throw_if($user->isAdmin(), new RequestException('You cannot ban admin!'));

            $user -> ban($request -> get('days'));
            session()->flash('success', "You have successfully banned $user->username!");
        }
        catch (RequestException $e){
            session()->flash('errormessage',$e->getMessage());
            return redirect()->back();
        }
        return redirect()->back();

    }

    /**
     * Get a request for removing a ban
     *
     * @param Ban $ban
     * @return RedirectResponse
     * @throws \Exception
     */
    public function removeBan(Ban $ban): RedirectResponse
    {
        $this -> checkGate();

        $ban -> delete();
        session()->flash('success', "You have successfully removed ban!");

        return redirect() -> back();
    }
}
