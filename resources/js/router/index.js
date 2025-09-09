import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        name: 'home',
        component: () => import('../views/home/Home.vue'),
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../views/auth/Login.vue'),
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('../views/auth/Register.vue'),
    },
    {
        path: '/policy',
        name: 'policy',
        component: () => import('../views/Policy.vue'),
    },
    {
        path: '/faq',
        name: 'faq',
        component: () => import('../views/Faq.vue'),
    },
    {
        path: '/more-routes',
        name: 'more-routes',
        component: () => import('../views/moreRoutes/MoreRoutes.vue'),
    },
    {
        path: '/admin',
        name: 'dashboard',
        component: () => import('../views/admin/Dashboard.vue'),
        meta: {
            requiresAuth: true,
            requiresAdmin: true
        }
    },
    {
        path: '/password/reset/:token',
        name: 'PasswordReset',
        component: () => import('../views/auth/PasswordReset.vue'),
        props: route => ({
            token: route.params.token,
            email: route.query.email,
            expires: route.query.expires,
            signature: route.query.signature,
        }),
    },
    {
        path: '/password/reset-request',
        name: 'passwordResetRequest',
        component: () => import('../views/auth/PasswordResetRequest.vue'),
    },
    {
        path: '/search',
        name: 'searchResults',
        component: () => import('../views/search/SearchResults.vue'),
    },
    {
        path: '/orders/booking-data',
        name: 'booking',
        component: () => import('../views/orders/booking/Booking.vue'),
    },
    {
        path: '/ticket/refund/:ticketUUID',
        name: 'refund',
        component: () => import('../views/tickets/Refund.vue'),
        props: route => ({
            ticketUUID: route.params.ticketUUID,  // передаем ticketUUID
            orderUUID: route.query.orderUUID,    // передаем orderUUID
        }),
    },
    {
        path: '/orders/:uuid',
        name: 'order-details',
        component: () => import('../views/orders/OrderDetails.vue'),
        props: route => ({
            uuid: route.params.uuid,
        }),
    },
    {
        path: '/verify-email',
        name: 'verify-email',
        component: () => import('../views/auth/VerifyEmail.vue'),
    },
    {
        path: '/email/verify/:id/:hash',
        name: 'EmailVerification',
        component: () => import('../views/auth/EmailVerification.vue'),
        props: route => ({
            id: route.params.id,
            hash: route.params.hash,
            expires: route.query.expires,
            signature: route.query.signature,
        }),
    },
    {
        path: "/account",
        name: "account",
        component: () => import('../views/user/Account.vue'),
        children: [
            {
                path: 'profile',
                name: 'UserProfile',
                component: () => import('../views/user/Profile.vue'),
            },
            {
                path: 'orders',
                name: 'orders',
                component: () => import('../views/user/MyOrders.vue'),
            },
            {
                path: 'docs',
                name: 'docs',
                component: () => import('../views/user/Docs.vue'),
            },
            {
                path: 'edit',
                name: 'edit',
                component: () => import('../views/user/Edit.vue'),
            },
        ]
    },
    {
        path: '/payment-info/:uuid',
        name: 'payment-info',
        component: () => import('../views/orders/PaymentInfo.vue'),
        props: route => ({
            uuid: route.params.uuid,
        }),
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
