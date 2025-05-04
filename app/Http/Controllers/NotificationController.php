<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Now many notifications to display per page
     *
     * @var int
     */
    private int $notificationsPerPage = 35;

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * View notifications of a user
     *
     * @return Factory|View
     */
    public function viewNotifications(): Factory|View
    {
        $notifications = auth()->user()->notifications()->orderBy('created_at','desc')->paginate($this->notificationsPerPage);
        foreach ($notifications->where('read',0) as $notification){
            $notification->markAsRead();
        }
        return view('profile.notifications')->with([
            'notifications' => $notifications
        ]);
    }

    public function deleteNotifications(): RedirectResponse
    {
        $notifications = auth()->user()->notifications()->delete();
        session()->flash('success','Notifications deleted successfully');
        return redirect()->back();
    }
}
