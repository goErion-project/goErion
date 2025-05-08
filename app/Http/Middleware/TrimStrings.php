<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;
use Symfony\Component\HttpFoundation\Response;

class TrimStrings extends Middleware
{
    /**
     * The names of the attributes that should not be trimmed.
     *
     * @var array $except
     */
    public $except = [
        'password',
        'password_confirmation',
    ];

}

