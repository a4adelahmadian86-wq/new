@extends('layouts.app')

@section('content')
<div class="farast-admin" dir="rtl">
    <aside class="farast-admin-nav">
        <div class="farast-admin-brand">
            <span class="farast-admin-mark" aria-hidden="true"><i></i><i></i><i></i><i></i><b></b></span>
            <div><strong>فراست</strong><small>مرکز عملیات</small></div>
        </div>
        <div class="farast-admin-nav-label">کنسول مدیریت</div>
        <nav>
            <a href="{{ route('admin.index') }}"><i class="fa-solid fa-grid-2"></i><span>نمای کلی</span></a>
            <a href="{{ route('admin.finance') }}"><i class="fa-solid fa-wallet"></i><span>مالی</span></a>
            <a class="is-active" href="{{ route('admin.emails') }}"><i class="fa-solid fa-envelope"></i><span>ایمیل</span></a>
            <a href="{{ route('admin.social') }}"><i class="fa-solid fa-share-nodes"></i><span>شبکه‌های اجتماعی</span></a>
        </nav>
        <div class="farast-admin-nav-bottom">
            <a href="{{ route('admin.index') }}"><i class="fa-solid fa-arrow-right"></i><span>بازگشت به مرکز</span></a>
        </div>
        <div class="farast-admin-live"><span></span><div><b>سامانه عملیاتی</b><small>کنترل ایمیل فعال است</small></div></div>
    </aside>

    <main class="farast-admin-main">
        <header class="farast-admin-topbar">
            <div class="farast-admin-heading">
                <div class="farast-admin-kicker"><span></span> EMAIL SYSTEM</div>
                <h1>سیستم ایمیل حرفه‌ای</h1>
                <p>اولویت: ارسال از خود سرور (رایگان). کنترل انواع ایمیل، تست و لاگ از یک پنل.</p>
            </div>
            <div class="farast-admin-top-actions">
                <div class="farast-admin-user">
                    <span>م</span>
                    <div><b>مدیر اصلی</b><small>{{ auth()->user()->mobile }}</small></div>
                    <form method="post" action="{{ route('logout') }}">@csrf
                        <button type="submit" aria-label="خروج"><i class="fa-solid fa-right-from-bracket"></i></button>
                    </form>
                </div>
            </div>
        </header>

        @if(session('status'))
            <div class="farast-admin-toast"><i class="fa-solid fa-circle-check"></i><span>{{ session('status') }}</span></div>
        @endif
        @if($errors->any())
            <div class="farast-admin-toast" style="background:#fef2f2;border-color:#fecaca;color:#b91c1c">
                <i class="fa-solid fa-circle-exclamation"></i><span>{{ $errors->first() }}</span>
            </div>
        @endif

        <section class="admin-panel admin-panel-wide">
            <div class="panel-heading-row">
                <div>
                    <h2><i class="fa-solid fa-server"></i> سرویس‌دهنده ایمیل</h2>
                    <p>اولویت: ارسال از <b>خود سرور</b> (Sendmail یا SMTP محلی) — بدون ثبت‌نام بیرونی.</p>
                </div>
                <span class="status-chip {{ $providerConfigured ? 'ok' : 'warn' }}">
                    {{ $providers[$mailer] ?? $mailer }} · {{ $providerConfigured ? 'آماده' : 'نیاز به تنظیم' }}
                </span>
            </div>

            <form method="post" action="{{ route('admin.emails.settings') }}" class="email-settings-form">
                @csrf
                <div class="email-settings-grid">
                    <label class="email-field">
                        <span>سرویس‌دهنده</span>
                        <select name="mail_provider">
                            @foreach($providers as $key => $label)
                                <option value="{{ $key }}" @selected($mailer === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <small>Sendmail / SMTP محلی رایگان روی خود سرور — بقیه اختیاری</small>
                    </label>
                    <label class="email-field">
                        <span>آدرس فرستنده (From)</span>
                        <input type="email" name="mail_from_address" value="{{ $mailFrom }}" placeholder="noreply@localhost">
                    </label>
                    <label class="email-field">
                        <span>نام فرستنده</span>
                        <input name="mail_from_name" value="{{ $mailFromName }}" placeholder="FARAST">
                    </label>
                    <label class="email-field">
                        <span>SMTP Host</span>
                        <input name="mail_host" value="{{ $mailHost }}" placeholder="127.0.0.1">
                    </label>
                    <label class="email-field">
                        <span>SMTP Port</span>
                        <input type="number" name="mail_port" value="{{ $mailPort }}" min="1" placeholder="25">
                    </label>
                    <label class="email-field">
                        <span>SMTP Username</span>
                        <input name="mail_username" value="{{ $mailUsername }}" autocomplete="off">
                    </label>
                    <label class="email-field">
                        <span>SMTP Password</span>
                        <input type="password" name="mail_password" placeholder="{{ ($hasSmtpPassword ?? false) ? 'تنظیم شده' : 'رمز SMTP' }}" autocomplete="new-password">
                    </label>
                    <label class="email-field">
                        <span>Encryption</span>
                        <select name="mail_encryption">
                            <option value="tls" @selected(($mailEncryption ?: 'tls') === 'tls')>TLS</option>
                            <option value="ssl" @selected($mailEncryption === 'ssl')>SSL</option>
                            <option value="null" @selected($mailEncryption === '' || $mailEncryption === null)>بدون</option>
                        </select>
                    </label>
                    <label class="email-field">
                        <span>کلید Resend (اختیاری)</span>
                        <input type="password" name="resend_api_key" placeholder="{{ ($hasResendKey ?? false) ? 'تنظیم شده' : 're_...' }}" autocomplete="new-password">
                    </label>
                    <label class="email-field">
                        <span>کلید Brevo (اختیاری)</span>
                        <input type="password" name="brevo_api_key" placeholder="{{ ($hasBrevoKey ?? false) ? 'تنظیم شده' : 'کلید Brevo' }}" autocomplete="new-password">
                    </label>
                </div>

                <div class="email-toggles">
                    <label class="check-line">
                        <input type="hidden" name="email_enabled" value="0">
                        <input type="checkbox" name="email_enabled" value="1" {{ $flags['email_enabled'] ? 'checked' : '' }}>
                        <span>فعال بودن کل سیستم ایمیل</span>
                    </label>
                    @foreach($types as $key => $label)
                        <label class="check-line">
                            <input type="hidden" name="email_{{ $key }}_enabled" value="0">
                            <input type="checkbox" name="email_{{ $key }}_enabled" value="1" {{ ($flags[$key] ?? true) ? 'checked' : '' }}>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                    <label class="check-line">
                        <input type="hidden" name="email_sync" value="0">
                        <input type="checkbox" name="email_sync" value="1" {{ filter_var(\App\Models\SiteSetting::read('email_sync', true), FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                        <span>ارسال همزمان (بدون Queue)</span>
                    </label>
                </div>

                <div class="email-actions">
                    <button type="submit" class="admin-primary"><i class="fa-solid fa-floppy-disk"></i> ذخیره تنظیمات ایمیل</button>
                </div>
            </form>
        </section>

        <section class="admin-panel">
            <div class="panel-heading-row">
                <div>
                    <h2><i class="fa-solid fa-paper-plane"></i> ارسال ایمیل آزمایشی</h2>
                    <p>بعد از ذخیره سرویس‌دهنده، اینجا تست کنید.</p>
                </div>
            </div>
            <form method="post" action="{{ route('admin.emails.test') }}" class="email-test-form">
                @csrf
                <label class="email-field">
                    <span>آدرس ایمیل مقصد</span>
                    <input type="email" name="test_email" required placeholder="you@example.com" value="{{ old('test_email') }}">
                </label>
                <button type="submit" class="admin-outline"><i class="fa-solid fa-flask"></i> ارسال تست</button>
            </form>
        </section>

        <section class="admin-panel admin-panel-wide">
            <div class="panel-heading-row">
                <div>
                    <h2><i class="fa-solid fa-headset"></i> پاسخ سریع به تیکت‌ها</h2>
                    <p>پاسخ شما برای کاربر ایمیل می‌شود.</p>
                </div>
                <span class="count-chip">{{ $tickets->count() }} تیکت</span>
            </div>
            <div class="email-ticket-list">
                @forelse($tickets as $ticket)
                    <article class="email-ticket-card">
                        <div class="email-ticket-head">
                            <div>
                                <b>#{{ $ticket->id }} — {{ $ticket->subject }}</b>
                                <small>{{ $ticket->user?->name }} · {{ $ticket->user?->email ?: 'بدون ایمیل' }} · {{ $ticket->status }}</small>
                            </div>
                            <span class="count-chip">{{ $ticket->created_at?->format('Y/m/d H:i') }}</span>
                        </div>
                        <form method="post" action="{{ route('admin.tickets.reply', $ticket) }}" class="email-ticket-reply">
                            @csrf
                            <textarea name="body" rows="3" required maxlength="5000" placeholder="متن پاسخ مدیر..."></textarea>
                            <div class="email-ticket-actions">
                                <select name="status">
                                    <option value="answered">پاسخ‌داده‌شده</option>
                                    <option value="open">باز</option>
                                    <option value="closed">بسته</option>
                                </select>
                                <button type="submit" class="admin-primary"><i class="fa-solid fa-reply"></i> ارسال پاسخ + ایمیل</button>
                            </div>
                        </form>
                    </article>
                @empty
                    <div class="admin-empty">تیکتی ثبت نشده است.</div>
                @endforelse
            </div>
        </section>

        <section class="admin-panel admin-panel-wide">
            <div class="panel-heading-row">
                <div>
                    <h2><i class="fa-solid fa-clock-rotate-left"></i> لاگ ایمیل‌ها</h2>
                    <p>آخرین ارسال‌ها، وضعیت و خطاها.</p>
                </div>
                <span class="count-chip">{{ $logs->count() }} مورد</span>
            </div>
            <div class="email-log-list">
                @forelse($logs as $log)
                    <article class="email-log-row">
                        <div>
                            <b>{{ $log->subject }}</b>
                            <small>{{ $log->to_email }} · {{ $types[$log->type] ?? $log->type }} · {{ $log->meta['provider'] ?? '-' }}</small>
                        </div>
                        <div class="email-log-meta">
                            <span class="status-chip {{ $log->status === 'sent' ? 'ok' : ($log->status === 'failed' ? 'warn' : '') }}">{{ $log->status }}</span>
                            <span class="count-chip">{{ $log->created_at?->format('Y/m/d H:i') }}</span>
                        </div>
                        @if($log->error)
                            <div class="email-log-error">{{ \Illuminate\Support\Str::limit($log->error, 180) }}</div>
                        @endif
                    </article>
                @empty
                    <div class="admin-empty">هنوز لاگی ثبت نشده است.</div>
                @endforelse
            </div>
        </section>
    </main>
</div>
@endsection
