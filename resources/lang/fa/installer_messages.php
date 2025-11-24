<?php

return [

    /*
     *
     * Shared translations.
     *
     */
    'title' => config('installer.item_name') . ' نصب کننده',
    'next' => 'مرحله بعد',
    'back' => 'قبلی',
    'finish' => 'نصب',
    'forms' => [
        'errorTitle' => 'خطاهای زیر رخ داده است:',
    ],

    /*
     *
     * Home page translations.
     *
     */
    'welcome' => [
        'templateTitle' => 'خوش آمدید',
        'title'   => config('installer.item_name') . ' نصب کننده',
        'message' => 'به ویزارد نصب و راه اندازی آسان خوش آمدید.',
        'next'    => 'بررسی نیازمندی‌ها',
    ],

    /*
     *
     * Requirements page translations.
     *
     */
    'requirements' => [
        'templateTitle' => 'مرحله 1 | نیازمندی‌های سرور',
        'title' => 'نیازمندی‌های سرور',
        'next'    => 'بررسی دسترسی‌ها',
    ],

    /*
     *
     * Permissions page translations.
     *
     */
    'permissions' => [
        'templateTitle' => 'مرحله 2 | دسترسی‌ها',
        'title' => 'دسترسی‌ها',
        'next' => 'پیکربندی محیط',
    ],

    /*
     *
     * Environment page translations.
     *
     */
    'environment' => [
        'menu' => [
            'templateTitle' => 'مرحله 3 | تنظیمات محیط',
            'title' => 'تنظیمات محیط',
            'desc' => 'لطفاً نحوه پیکربندی فایل <code>.env</code> برنامه را انتخاب کنید.',
            'wizard-button' => 'راه‌اندازی فرم ویزارد',
            'classic-button' => 'ویرایشگر متن کلاسیک',
        ],
        'wizard' => [
            'templateTitle' => 'مرحله 3 | تنظیمات محیط | ویزارد هدایت شده',
            'title' => 'ویزارد هدایت شده <code>.env</code>',
            'tabs' => [
                'environment' => 'محیط',
                'database' => 'پایگاه داده',
                'application' => 'برنامه'
            ],
            'form' => [
                'name_required' => 'نام محیط الزامی است.',
                'app_name_label' => 'نام برنامه',
                'app_name_placeholder' => 'نام برنامه',
                'app_environment_label' => 'محیط برنامه',
                'app_environment_label_local' => 'محلی',
                'app_environment_label_developement' => 'توسعه',
                'app_environment_label_qa' => 'تست',
                'app_environment_label_production' => 'تولید',
                'app_environment_label_other' => 'سایر',
                'app_environment_placeholder_other' => 'محیط خود را وارد کنید...',
                'app_debug_label' => 'دیباگ برنامه',
                'app_debug_label_true' => 'فعال',
                'app_debug_label_false' => 'غیرفعال',
                'app_log_level_label' => 'سطح گزارش برنامه',
                'app_log_level_label_debug' => 'دیباگ',
                'app_log_level_label_info' => 'اطلاعات',
                'app_log_level_label_notice' => 'توجه',
                'app_log_level_label_warning' => 'هشدار',
                'app_log_level_label_error' => 'خطا',
                'app_log_level_label_critical' => 'بحرانی',
                'app_log_level_label_alert' => 'هشدار',
                'app_log_level_label_emergency' => 'اضطراری',
                'app_url_label' => 'آدرس برنامه',
                'app_url_placeholder' => 'آدرس برنامه',
                'db_connection_label' => 'اتصال پایگاه داده',
                'db_connection_label_mysql' => 'mysql',
                'db_connection_label_sqlite' => 'sqlite',
                'db_connection_label_pgsql' => 'pgsql',
                'db_connection_label_sqlsrv' => 'sqlsrv',
                'db_host_label' => 'هاست پایگاه داده',
                'db_host_placeholder' => 'هاست پایگاه داده',
                'db_port_label' => 'پورت پایگاه داده',
                'db_port_placeholder' => 'پورت پایگاه داده',
                'db_name_label' => 'نام پایگاه داده',
                'db_name_placeholder' => 'نام پایگاه داده',
                'db_username_label' => 'نام کاربری پایگاه داده',
                'db_username_placeholder' => 'نام کاربری پایگاه داده',
                'db_password_label' => 'رمز عبور پایگاه داده',
                'db_password_placeholder' => 'رمز عبور پایگاه داده',
                'app_tabs' => [
                    'more_info' => 'اطلاعات بیشتر',
                    'broadcasting_title' => 'پخش، ذخیره‌سازی، جلسه و صف',
                    'broadcasting_label' => 'درایور پخش',
                    'broadcasting_placeholder' => 'درایور پخش',
                    'cache_label' => 'درایور کش',
                    'cache_placeholder' => 'درایور کش',
                    'session_label' => 'درایور جلسه',
                    'session_placeholder' => 'درایور جلسه',
                    'queue_label' => 'درایور صف',
                    'queue_placeholder' => 'درایور صف',
                    'redis_label' => 'درایور Redis',
                    'redis_host' => 'هاست Redis',
                    'redis_password' => 'رمز عبور Redis',
                    'redis_port' => 'پورت Redis',
                    'mail_label' => 'میل',
                    'mail_driver_label' => 'درایور میل',
                    'mail_driver_placeholder' => 'درایور میل',
                    'mail_host_label' => 'هاست میل',
                    'mail_host_placeholder' => 'هاست میل',
                    'mail_port_label' => 'پورت میل',
                    'mail_port_placeholder' => 'پورت میل',
                    'mail_username_label' => 'نام کاربری میل',
                    'mail_username_placeholder' => 'نام کاربری میل',
                    'mail_password_label' => 'رمز عبور میل',
                    'mail_password_placeholder' => 'رمز عبور میل',
                    'mail_encryption_label' => 'رمزنگاری میل',
                    'mail_encryption_placeholder' => 'رمزنگاری میل',
                    'pusher_label' => 'Pusher',
                    'pusher_app_id_label' => 'شناسه برنامه Pusher',
                    'pusher_app_id_palceholder' => 'شناسه برنامه Pusher',
                    'pusher_app_key_label' => 'کلید برنامه Pusher',
                    'pusher_app_key_palceholder' => 'کلید برنامه Pusher',
                    'pusher_app_secret_label' => 'راز برنامه Pusher',
                    'pusher_app_secret_palceholder' => 'راز برنامه Pusher',
                ],
                'buttons' => [
                    'setup_database' => 'راه‌اندازی پایگاه داده',
                    'setup_application' => 'راه‌اندازی برنامه',
                    'install' => 'نصب'
                ],
            ],
        ],
        'classic' => [
            'templateTitle' => 'مرحله 3 | تنظیمات محیط | ویرایشگر کلاسیک',
            'title' => 'ویرایشگر محیط کلاسیک',
            'save' => 'ذخیره .env',
            'back' => 'استفاده از فرم ویزارد',
            'install' => 'ذخیره و نصب',
        ],
        'success' => 'تنظیمات فایل .env شما ذخیره شد.',
        'errors' => 'امکان ذخیره فایل .env وجود ندارد، لطفاً آن را به صورت دستی ایجاد کنید.',
    ],

    'install' => 'نصب',

    /*
     *
     * Installed Log translations.
     *
     */
    'installed' => [
        'success_log_message' => 'نصب کننده لاراول با موفقیت نصب شد در ',
    ],

    /*
     *
     * Final page translations.
     *
     */
    'final' => [
        'title' => 'نصب کامل شد',
        'templateTitle' => 'نصب کامل شد',
        'finished' => 'برنامه با موفقیت نصب شد.',
        'migration' => 'خروجی مهاجرت:',
        'console' => 'خروجی کنسول برنامه:',
        'log' => 'ورودی گزارش نصب:',
        'env' => 'فایل .env نهایی:',
        'exit' => 'برای خروج اینجا کلیک کنید',
    ],

    /*
     *
     * Update specific translations
     *
     */
    'updater' => [
        /*
         *
         * Shared translations.
         *
         */
        'title' => 'به‌روزرسانی لاراول',

        /*
         *
         * Welcome page translations for update feature.
         *
         */
        'welcome' => [
            'title'   => 'به به‌روزرسانی خوش آمدید',
            'message' => 'به ویزارد به‌روزرسانی خوش آمدید.',
        ],

        /*
         *
         * Welcome page translations for update feature.
         *
         */
        'overview' => [
            'title'   => 'بررسی اجمالی',
            'message' => 'یک به‌روزرسانی وجود دارد.|:number به‌روزرسانی وجود دارد.',
            'install_updates' => 'نصب به‌روزرسانی',
        ],

        /*
         *
         * Final page translations.
         *
         */
        'final' => [
            'title' => 'کامل شد',
            'finished' => 'پایگاه داده برنامه با موفقیت به‌روزرسانی شد.',
            'exit' => 'برای خروج اینجا کلیک کنید',
        ],

        'log' => [
            'success_message' => 'نصب کننده لاراول با موفقیت به‌روزرسانی شد در ',
        ],
    ],
]; 