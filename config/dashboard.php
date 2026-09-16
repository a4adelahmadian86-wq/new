<?php

return [
    'navigation' => [
        [
            'key' => 'workspace', 'label' => 'میز کار', 'icon' => 'fa-table-cells-large',
            'items' => [
                ['key' => 'workspace.overview', 'label' => 'نمای کلی', 'icon' => 'fa-grid-2', 'route' => 'dashboard'],
                ['key' => 'workspace.recent', 'label' => 'فعالیت‌های اخیر', 'icon' => 'fa-clock-rotate-left', 'route' => 'workspace.recent'],
                ['key' => 'workspace.tasks', 'label' => 'وظایف من', 'icon' => 'fa-list-check', 'route' => 'workspace.tasks'],
                ['key' => 'workspace.action', 'label' => 'موارد نیازمند اقدام', 'icon' => 'fa-bell', 'route' => 'workspace.actions'],
                ['key' => 'workspace.shortcuts', 'label' => 'میانبرها', 'icon' => 'fa-bolt', 'route' => 'workspace.shortcuts'],
                ['key' => 'workspace.wallet', 'label' => 'کیف پول', 'icon' => 'fa-wallet', 'route' => 'wallet'],
                ['key' => 'workspace.status', 'label' => 'وضعیت سیستم', 'icon' => 'fa-heart-pulse', 'route' => 'admin.index', 'admin_only' => true],
            ],
        ],
        [
            'key' => 'documents', 'label' => 'اسناد و فایل‌ها', 'icon' => 'fa-folder-open',
            'items' => [
                ['key' => 'documents.all', 'label' => 'همه اسناد', 'icon' => 'fa-files', 'route' => 'documents.all', 'admin_only' => true],
                ['key' => 'documents.mine', 'label' => 'اسناد من', 'icon' => 'fa-file-lines', 'route' => 'documents.mine', 'permission' => 'documents.view', 'scope' => 'own'],
                ['key' => 'documents.recent', 'label' => 'اسناد اخیر', 'icon' => 'fa-clock-rotate-left', 'route' => 'documents.recent', 'permission' => 'documents.view', 'scope' => 'own'],
                ['key' => 'documents.files', 'label' => 'فایل‌ها', 'icon' => 'fa-folder', 'route' => 'library', 'permission' => 'documents.view', 'scope' => 'own'],
                ['key' => 'documents.deleted', 'label' => 'موارد حذف‌شده', 'icon' => 'fa-trash-can', 'route' => 'documents.deleted', 'permission' => 'documents.view', 'scope' => 'own'],
            ],
        ],
        [
            'key' => 'editor', 'label' => 'ویرایشگر', 'icon' => 'fa-pen-ruler',
            'items' => [
                ['key' => 'editor.open', 'label' => 'باز کردن ویرایشگر', 'icon' => 'fa-pen-to-square', 'route' => 'editor', 'permission' => 'editor.use', 'scope' => 'own'],
            ],
        ],
        [
            'key' => 'ai', 'label' => 'هوش مصنوعی', 'icon' => 'fa-wand-magic-sparkles',
            'items' => [
                ['key' => 'ai.history', 'label' => 'تاریخچه AI', 'icon' => 'fa-clock-rotate-left', 'route' => 'ai.history', 'permission' => 'ai.use', 'scope' => 'own'],
                ['key' => 'ai.quota', 'label' => 'سهمیه AI', 'icon' => 'fa-gauge-high', 'route' => 'ai.quota', 'permission' => 'ai.use', 'scope' => 'own'],
            ],
        ],
        [
            'key' => 'notifications', 'label' => 'اعلان‌ها و پیام‌ها', 'icon' => 'fa-bell',
            'items' => [
                ['key' => 'notifications.list', 'label' => 'اعلان‌ها', 'icon' => 'fa-bullhorn', 'route' => 'announcements'],
                ['key' => 'notifications.support', 'label' => 'پشتیبانی', 'icon' => 'fa-headset', 'route' => 'support', 'permission' => 'support.use', 'scope' => 'own'],
            ],
        ],
        [
            'key' => 'users', 'label' => 'مدیریت کاربران و دسترسی‌ها', 'icon' => 'fa-users-gear',
            'items' => [
                ['key' => 'users.admin', 'label' => 'پنل مدیریت', 'icon' => 'fa-user-shield', 'route' => 'admin.index', 'admin_only' => true],
                ['key' => 'users.finance', 'label' => 'مالی', 'icon' => 'fa-wallet', 'route' => 'admin.finance', 'admin_only' => true],
                ['key' => 'users.emails', 'label' => 'سیستم ایمیل', 'icon' => 'fa-envelope', 'route' => 'admin.emails', 'admin_only' => true],
            ],
        ],
        [
            'key' => 'settings', 'label' => 'تنظیمات و پشتیبانی',
            'icon' => 'fa-gear',
            'items' => [
                ['key' => 'settings.account', 'label' => 'تنظیمات حساب', 'icon' => 'fa-user-gear', 'route' => 'account.show'],
                ['key' => 'settings.site', 'label' => 'بازگشت به سایت', 'icon' => 'fa-house', 'route' => 'home'],
            ],
        ],
    ],
];
