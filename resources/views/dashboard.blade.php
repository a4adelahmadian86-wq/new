@extends('layouts.app')

@section('content')
{{-- FARAST Workspace Home v2 — داشبورد قدیمی حذف شده است --}}
<div class="workspace-home" dir="rtl" data-dashboard-version="v2">
    <header class="workspace-home__hero">
        <div class="workspace-home__hero-copy">
            <span class="workspace-home__kicker"><i class="fa-solid fa-layer-group"></i> فضای کاری فراست</span>
            <h1>سلام{{ auth()->user()->name ? '، '.auth()->user()->name : '' }}</h1>
            <p>اسناد، ظرفیت حساب و مسیرهای کاری از همین‌جا در دسترس‌اند. منوی کناری دسترسی کامل را نشان می‌دهد.</p>
        </div>
        <div class="workspace-home__hero-actions">
            @if(($capabilities['active'] ?? false) && ($capabilities['can_type'] ?? false))
                <a class="workspace-btn workspace-btn--primary" href="{{ route('editor') }}">
                    <i class="fa-solid fa-plus"></i> تایپ جدید
                </a>
            @endif
            <a class="workspace-btn workspace-btn--ghost" href="{{ route('wallet') }}">
                <i class="fa-solid fa-wallet"></i> کیف پول
            </a>
        </div>
    </header>

    <section class="workspace-metrics" aria-label="خلاصه حساب">
        <article class="workspace-metric">
            <span class="workspace-metric__icon"><i class="fa-solid fa-file-lines"></i></span>
            <div>
                <b>{{ number_format($documents->count()) }}</b>
                <small>سندهای من</small>
            </div>
        </article>
        <article class="workspace-metric">
            <span class="workspace-metric__icon workspace-metric__icon--green"><i class="fa-solid fa-gift"></i></span>
            <div>
                <b>{{ ($capabilities['unlimited'] ?? false) ? 'نامحدود' : number_format($capabilities['weekly_free_pages'] ?? 0) }}</b>
                <small>صفحه رایگان هفتگی</small>
            </div>
        </article>
        <article class="workspace-metric">
            <span class="workspace-metric__icon workspace-metric__icon--violet"><i class="fa-solid fa-wand-magic-sparkles"></i></span>
            <div>
                <b>{{ ($capabilities['unlimited'] ?? false) ? 'نامحدود' : number_format($capabilities['daily_ai_requests'] ?? 0) }}</b>
                <small>سقف AI روزانه</small>
            </div>
        </article>
        <article class="workspace-metric">
            <span class="workspace-metric__icon workspace-metric__icon--cyan"><i class="fa-solid fa-file-arrow-up"></i></span>
            <div>
                <b>{{ ($capabilities['unlimited'] ?? false) ? 'نامحدود' : number_format($capabilities['max_file_mb'] ?? 0).' MB' }}</b>
                <small>سقف فایل</small>
            </div>
        </article>
    </section>

    <div class="workspace-grid">
        <section class="workspace-panel">
            <div class="workspace-panel__head">
                <div>
                    <h2>قابلیت‌های حساب</h2>
                    <p>دسترسی‌ها از سمت سرور محاسبه می‌شوند.</p>
                </div>
            </div>
            <div class="workspace-caps">
                @foreach([
                    'can_type' => 'تایپ و ویرایش',
                    'can_ai' => 'هوش مصنوعی',
                    'can_voice' => 'تایپ صوتی',
                    'can_export_docx' => 'خروجی Word',
                    'can_export_pdf' => 'خروجی PDF',
                    'can_feedback' => 'بازخورد',
                    'can_support' => 'پشتیبانی',
                ] as $key => $label)
                    <span class="workspace-cap {{ ($capabilities[$key] ?? false) ? 'is-on' : 'is-off' }}">
                        <i class="fa-solid {{ ($capabilities[$key] ?? false) ? 'fa-check' : 'fa-lock' }}"></i>
                        {{ $label }}
                    </span>
                @endforeach
            </div>
        </section>

        <section class="workspace-panel">
            <div class="workspace-panel__head">
                <div>
                    <h2>وضعیت حساب</h2>
                    <p>{{ auth()->user()->mobile }}</p>
                </div>
            </div>
            <div class="workspace-account">
                <div class="workspace-account__state">
                    <span class="workspace-account__badge {{ ($capabilities['active'] ?? false) ? 'is-on' : 'is-off' }}">
                        <i class="fa-solid {{ ($capabilities['active'] ?? false) ? 'fa-user-check' : 'fa-user-lock' }}"></i>
                        {{ ($capabilities['active'] ?? false) ? 'حساب فعال' : 'دسترسی محدود' }}
                    </span>
                    <small>{{ auth()->user()->is_verified ? 'تأییدشده' : 'هنوز تأیید نشده' }}</small>
                </div>
                <div class="workspace-account__links">
                    @if($capabilities['can_support'] ?? false)
                        <a href="{{ route('support') }}"><i class="fa-solid fa-headset"></i> پشتیبانی</a>
                    @endif
                    <a href="{{ route('wallet') }}"><i class="fa-solid fa-wallet"></i> کیف پول</a>
                    <a href="{{ route('account.show') }}"><i class="fa-solid fa-user-gear"></i> تنظیمات</a>
                    <a href="{{ route('workspace.actions') }}"><i class="fa-solid fa-bell"></i> اقدام‌ها</a>
                </div>
            </div>
        </section>
    </div>

    <section class="workspace-panel workspace-panel--wide">
        <div class="workspace-panel__head">
            <div>
                <h2>اسناد اخیر</h2>
                <p>فقط اسناد متعلق به حساب شما نمایش داده می‌شود.</p>
            </div>
            <span class="workspace-count">{{ number_format($documents->count()) }}</span>
        </div>
        <div class="workspace-docs">
            @forelse($documents as $d)
                <article class="workspace-doc">
                    <div class="workspace-doc__title">
                        <i class="fa-regular fa-file-lines"></i>
                        <span>{{ $d->title }}</span>
                    </div>
                    <span>{{ number_format($d->page_count) }} صفحه</span>
                    <span>{{ number_format($d->price_rials) }} ریال</span>
                    <a href="{{ route('editor.show', $d) }}" class="workspace-doc__open">باز کردن</a>
                </article>
            @empty
                <div class="workspace-empty">
                    <i class="fa-regular fa-folder-open"></i>
                    <p>هنوز سندی ندارید. از «تایپ جدید» شروع کنید.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
