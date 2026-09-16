<?php

namespace App\Http\Controllers;

use App\Models\AiInteraction;
use App\Models\Announcement;
use App\Models\EmailLog;
use App\Models\PricingRule;
use App\Models\SiteSetting;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserCapability;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\CapabilityService;
use App\Services\EmailService;
use App\Services\MailConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function index(CapabilityService $capabilities)
    {
        $users = User::latest()->limit(100)->get();
        $settings = [];
        foreach (['weekly_free_pages', 'max_file_mb', 'daily_ai_requests', 'can_type', 'can_ai', 'can_voice', 'can_export_docx', 'can_export_pdf', 'can_feedback', 'can_support', 'gemini_model'] as $key) {
            $settings[$key] = SiteSetting::read($key, CapabilityService::DEFAULTS[$key] ?? 'gemini-3.8-flash');
        }

        return view('admin.index', [
            'rules' => PricingRule::orderBy('id')->get(),
            'users' => $users,
            'announcements' => Announcement::latest()->limit(20)->get(),
            'stats' => [
                'users' => User::count(),
                'documents' => \App\Models\TypingDocument::count(),
                'ai' => AiInteraction::count(),
                'ai_today' => AiInteraction::whereDate('created_at', today())->count(),
            ],
            'settings' => $settings,
            'secretStatus' => [
                'gemini' => (bool) (SiteSetting::read('gemini_api_key') ?: config('services.gemini.key')),
                'files' => (bool) (SiteSetting::read('files_api_key') ?: env('FILES_API_KEY')),
            ],
            'capabilities' => $capabilities,
        ]);
    }

    public function finance()
    {
        return view('admin.finance', [
            'taxEnabled' => filter_var(SiteSetting::read('tax_enabled', true), FILTER_VALIDATE_BOOLEAN),
            'taxRate' => (float) SiteSetting::read('tax_rate_percent', 10),
            'gateway' => (string) SiteSetting::read('payment_gateway', 'none'),
            'merchantConfigured' => (bool) SiteSetting::read('gateway_merchant_id'),
            'users' => User::orderBy('name')->limit(100)->get(),
        ]);
    }

    public function updateFinance(Request $r)
    {
        $d = $r->validate([
            'tax_enabled' => ['nullable', 'boolean'],
            'tax_rate_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'payment_gateway' => ['required', 'in:none,zarinpal,idpay'],
            'gateway_merchant_id' => ['nullable', 'string', 'max:200'],
            'wallet_user_id' => ['nullable', 'integer'],
            'wallet_amount' => ['nullable', 'integer', 'min:1'],
            'wallet_note' => ['nullable', 'string', 'max:200'],
        ]);

        SiteSetting::write('tax_enabled', ($d['tax_enabled'] ?? false) ? '1' : '0');
        SiteSetting::write('tax_rate_percent', (string) $d['tax_rate_percent']);
        SiteSetting::write('payment_gateway', $d['payment_gateway']);
        if (filled($d['gateway_merchant_id'] ?? null)) {
            SiteSetting::write('gateway_merchant_id', trim($d['gateway_merchant_id']), true);
        }

        if (! empty($d['wallet_user_id']) && ! empty($d['wallet_amount'])) {
            $user = User::findOrFail($d['wallet_user_id']);
            $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance_rials' => 0]);
            DB::transaction(function () use ($wallet, $d) {
                $wallet->balance_rials += (int) $d['wallet_amount'];
                $wallet->save();
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'amount_rials' => (int) $d['wallet_amount'],
                    'type' => 'credit',
                    'note' => $d['wallet_note'] ?? 'شارژ دستی ادمین',
                ]);
            });
        }

        return back()->with('status', 'تنظیمات مالی ذخیره شد.');
    }

    public function updatePricing(Request $r)
    {
        $d = $r->validate([
            'rules' => ['required', 'array'],
            'rules.*.id' => ['required', 'integer'],
            'rules.*.price_rials' => ['required', 'integer', 'min:0'],
            'rules.*.active' => ['nullable', 'boolean'],
        ]);
        foreach ($d['rules'] as $row) {
            PricingRule::where('id', $row['id'])->update([
                'price_rials' => $row['price_rials'],
                'active' => (bool) ($row['active'] ?? false),
            ]);
        }

        return back()->with('status', 'قیمت‌ها به‌روز شد.');
    }

    public function updateDefaults(Request $r)
    {
        $keys = ['weekly_free_pages', 'max_file_mb', 'daily_ai_requests', 'can_type', 'can_ai', 'can_voice', 'can_export_docx', 'can_export_pdf', 'can_feedback', 'can_support', 'gemini_model'];
        foreach ($keys as $key) {
            if ($r->has($key)) {
                SiteSetting::write($key, (string) $r->input($key));
            }
        }
        if ($r->filled('gemini_api_key')) {
            SiteSetting::write('gemini_api_key', trim($r->input('gemini_api_key')), true);
        }
        if ($r->filled('files_api_key')) {
            SiteSetting::write('files_api_key', trim($r->input('files_api_key')), true);
        }

        return back()->with('status', 'پیش‌فرض‌ها ذخیره شد.');
    }

    public function updateUser(Request $r, User $user)
    {
        $d = $r->validate([
            'role' => ['required', 'in:user,admin'],
            'is_verified' => ['nullable', 'boolean'],
            'weekly_free_pages' => ['nullable', 'integer', 'min:0'],
            'max_file_mb' => ['nullable', 'integer', 'min:1'],
            'daily_ai_requests' => ['nullable', 'integer', 'min:0'],
            'can_type' => ['nullable', 'boolean'],
            'can_ai' => ['nullable', 'boolean'],
            'can_voice' => ['nullable', 'boolean'],
            'can_export_docx' => ['nullable', 'boolean'],
            'can_export_pdf' => ['nullable', 'boolean'],
            'can_feedback' => ['nullable', 'boolean'],
            'can_support' => ['nullable', 'boolean'],
        ]);
        $user->role = $d['role'];
        $user->is_verified = (bool) ($d['is_verified'] ?? false);
        $user->save();

        $cap = UserCapability::firstOrCreate(['user_id' => $user->id]);
        foreach (['weekly_free_pages', 'max_file_mb', 'daily_ai_requests', 'can_type', 'can_ai', 'can_voice', 'can_export_docx', 'can_export_pdf', 'can_feedback', 'can_support'] as $k) {
            if (array_key_exists($k, $d)) {
                $cap->{$k} = $d[$k];
            }
        }
        $cap->save();

        return back()->with('status', 'کاربر به‌روز شد.');
    }

    public function emails(MailConfigService $mailConfig)
    {
        $flags = [];
        foreach (array_keys(EmailService::TYPES) as $type) {
            $flags[$type] = filter_var(SiteSetting::read('email_'.$type.'_enabled', true), FILTER_VALIDATE_BOOLEAN);
        }
        $flags['email_enabled'] = filter_var(SiteSetting::read('email_enabled', true), FILTER_VALIDATE_BOOLEAN);

        $logs = Schema::hasTable('email_logs')
            ? EmailLog::latest()->limit(80)->get()
            : collect();

        $tickets = Schema::hasTable('tickets')
            ? Ticket::with('user')->latest()->limit(30)->get()
            : collect();

        $provider = $mailConfig->currentProvider();

        return view('admin.emails', [
            'flags' => $flags,
            'types' => EmailService::TYPES,
            'logs' => $logs,
            'tickets' => $tickets,
            'mailFrom' => SiteSetting::read('mail_from_address', config('mail.from.address')),
            'mailFromName' => SiteSetting::read('mail_from_name', config('mail.from.name')),
            'mailer' => $provider,
            'providers' => MailConfigService::PROVIDERS,
            'providerConfigured' => $mailConfig->providerConfigured($provider),
            'mailHost' => SiteSetting::read('mail_host', ''),
            'mailPort' => SiteSetting::read('mail_port', ''),
            'mailUsername' => SiteSetting::read('mail_username', ''),
            'mailEncryption' => SiteSetting::read('mail_encryption', 'tls'),
        ]);
    }

    public function updateEmailSettings(Request $request)
    {
        SiteSetting::write('email_enabled', $request->boolean('email_enabled') ? '1' : '0');

        foreach (array_keys(EmailService::TYPES) as $type) {
            $key = 'email_'.$type.'_enabled';
            SiteSetting::write($key, $request->boolean($key) ? '1' : '0');
        }

        SiteSetting::write('email_sync', $request->boolean('email_sync') ? '1' : '0');

        $data = $request->validate([
            'mail_provider' => ['required', 'in:sendmail,local,log,smtp,mailtrap,brevo,resend'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:120'],
            'mail_host' => ['nullable', 'string', 'max:200'],
            'mail_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'mail_username' => ['nullable', 'string', 'max:200'],
            'mail_password' => ['nullable', 'string', 'max:500'],
            'mail_encryption' => ['nullable', 'in:tls,ssl,null'],
            'resend_api_key' => ['nullable', 'string', 'max:500'],
            'brevo_api_key' => ['nullable', 'string', 'max:500'],
        ]);

        SiteSetting::write('mail_provider', $data['mail_provider']);
        if (filled($data['mail_from_address'] ?? null)) {
            SiteSetting::write('mail_from_address', trim($data['mail_from_address']));
        }
        if (filled($data['mail_from_name'] ?? null)) {
            SiteSetting::write('mail_from_name', trim($data['mail_from_name']));
        }
        if (array_key_exists('mail_host', $data) && $data['mail_host'] !== null) {
            SiteSetting::write('mail_host', trim((string) $data['mail_host']));
        }
        if (array_key_exists('mail_port', $data) && $data['mail_port'] !== null) {
            SiteSetting::write('mail_port', (string) $data['mail_port']);
        }
        if (array_key_exists('mail_username', $data) && $data['mail_username'] !== null) {
            SiteSetting::write('mail_username', trim((string) $data['mail_username']));
        }
        if (filled($data['mail_password'] ?? null)) {
            SiteSetting::write('mail_password', trim($data['mail_password']), true);
        }
        if (array_key_exists('mail_encryption', $data)) {
            $enc = $data['mail_encryption'] === 'null' ? '' : ($data['mail_encryption'] ?? 'tls');
            SiteSetting::write('mail_encryption', $enc);
        }
        if (filled($data['resend_api_key'] ?? null)) {
            SiteSetting::write('resend_api_key', trim($data['resend_api_key']), true);
        }
        if (filled($data['brevo_api_key'] ?? null)) {
            SiteSetting::write('brevo_api_key', trim($data['brevo_api_key']), true);
        }

        if ($data['mail_provider'] === 'mailtrap' && empty($data['mail_host'])) {
            SiteSetting::write('mail_host', 'sandbox.smtp.mailtrap.io');
            SiteSetting::write('mail_port', '2525');
        }
        if ($data['mail_provider'] === 'brevo' && empty($data['mail_host'])) {
            SiteSetting::write('mail_host', 'smtp-relay.brevo.com');
            SiteSetting::write('mail_port', '587');
            SiteSetting::write('mail_encryption', 'tls');
        }
        if ($data['mail_provider'] === 'local') {
            SiteSetting::write('mail_host', $data['mail_host'] ?: '127.0.0.1');
            SiteSetting::write('mail_port', (string) ($data['mail_port'] ?: 25));
        }

        return back()->with('status', 'تنظیمات ایمیل و سرویس‌دهنده ذخیره شد.');
    }

    public function sendTestEmail(Request $request, EmailService $emailService)
    {
        $data = $request->validate([
            'test_email' => ['required', 'email', 'max:255'],
        ]);

        try {
            $emailService->sendTest($data['test_email']);

            return back()->with('status', 'ایمیل آزمایشی به '.$data['test_email'].' ارسال شد.');
        } catch (\Throwable $e) {
            return back()->withErrors(['test_email' => 'ارسال ناموفق: '.$e->getMessage()]);
        }
    }
}
