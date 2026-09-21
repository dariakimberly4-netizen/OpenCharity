<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    public function mount(): void
    {
        parent::mount();

        if (config('app.demo_mode')) {
            $this->form->fill([
                'email' => 'admin@test.com',
                'password' => '123456789',
            ]);
        }
    }

    public function getHeading(): string|Htmlable
    {
        return __('Government Assistance & Payout Hub');
    }

    public function getSubheading(): string|Htmlable|null
    {
        $message = '<div class="space-y-3 text-center">'
            .'<p class="text-sm text-gray-600 dark:text-gray-300">'
            .__('Authorized personnel only. Sign in to access Verification, Assessment, Approval, Payout, Released, Reports, and Audit Trail.')
            .'</p>';

        if (config('app.demo_mode')) {
            $message .= '<div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-400">'
                .'<span class="font-semibold">'.__('Demo Mode').'</span> — '
                .__('Demo credentials are pre-filled for testing.')
                .'</div>';
        }

        $message .= '</div>';

        return new HtmlString($message);
    }
}
