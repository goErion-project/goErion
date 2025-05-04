<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LogController extends Controller
{
    /**
     * For how many minutes to cache the logs
     *
     *
     * @var int
     */
    private int $logsCacheTimeMinutes = 0;
    /**
     * How many logs to display per page
     *
     * @var int
     */
    private int $logsPerPage = 35;

    public function __construct(){
        $this -> middleware('admin_panel_access');
    }

    private function checkLogs(): void
    {
        if(Gate::denies('has-access', 'logs'))
            abort(403);
    }

    public function showLog(): View
    {
        $this -> checkLogs();

        $logs = Cache::remember('logs', $this->logsCacheTimeMinutes, function () {
            return Log::with('user')->orderBy('created_at','desc')->paginate($this->logsPerPage);
        });
        return view('admin.log')->with([
            'cacheMinutes' => $this->logsCacheTimeMinutes,
            'logs' => $logs
        ]);
    }
}
