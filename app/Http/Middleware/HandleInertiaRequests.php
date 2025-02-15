<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'notif_status'  => fn() => $request->session()->get('notif_status'), //success / error
                'notif_message' => fn() => $request->session()->get('notif_message'), //isi notifikasi
                'is_kriteria'   => fn() => $request->session()->get('notif_message'),
                'is_login'      => fn() => $request->session()->get('is_login'),
                'is_logout'     => fn() => $request->session()->get('is_logout')
            ],
            'ziggy' => fn() => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ];
    }
}
