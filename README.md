php artisan queue:work --queue=default,verification,verify-resend,password-reset,tickets-send,verification-code,welcome,refund,refunded
php artisan queue:retry all
php artisan inertia:start-ssr
