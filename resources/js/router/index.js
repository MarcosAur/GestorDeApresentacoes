import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
    {
        path: '/',
        name: 'home',
        component: () => import('../views/Home.vue'),
        beforeEnter: (to, from, next) => {
            const auth = useAuthStore();
            if (auth.isLoggedIn) next({ name: 'dashboard' });
            else next({ name: 'login' });
        }
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../views/Login.vue'),
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('../views/Register.vue'),
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('../views/Dashboard.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/events',
        name: 'events.index',
        component: () => import('../views/events/Index.vue'),
        meta: { requiresAuth: true, role: 'admin' }
    },
    {
        path: '/contests',
        name: 'contests.index',
        component: () => import('../views/contests/Index.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/jurors',
        name: 'jurors.index',
        component: () => import('../views/jurors/Index.vue'),
        meta: { requiresAuth: true, role: 'admin' }
    },
    {
        path: '/contests/:contestId/stage',
        name: 'admin.stage',
        component: () => import('../views/admin/Stage.vue'),
        meta: { requiresAuth: true, role: 'admin' }
    },
    {
        path: '/checkin',
        name: 'admin.checkin',
        component: () => import('../views/admin/Checkin.vue'),
        meta: { requiresAuth: true, role: 'admin' }
    },
    {
        path: '/analyzer',
        name: 'admin.analyzer',
        component: () => import('../views/admin/Analyzer.vue'),
        meta: { requiresAuth: true, role: 'admin' }
    },
    {
        path: '/evaluate/:contestId',
        name: 'juror.evaluation',
        component: () => import('../views/juror/Evaluation.vue'),
        meta: { requiresAuth: true, role: 'jurado' }
    },
    {
        path: '/public/stage/:contestId',
        name: 'public.stage',
        component: () => import('../views/public/StageViewer.vue'),
    },
    {
        path: '/rankings',
        name: 'public.rankings',
        component: () => import('../views/public/RankingsList.vue'),
    },
    {
        path: '/rankings/:contestId',
        name: 'public.ranking.detail',
        component: () => import('../views/public/RankingDetail.vue'),
    },
    {
        path: '/enrollment',
        name: 'competitor.enrollment',
        component: () => import('../views/competitor/Enrollment.vue'),
        meta: { requiresAuth: true, role: 'competidor' }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    const auth = useAuthStore();
    
    if (to.meta.requiresAuth && !auth.isLoggedIn) {
        await auth.fetchUser();
        if (!auth.isLoggedIn) {
            return next({ name: 'login' });
        }
    }

    if (to.meta.role && auth.user?.role?.slug !== to.meta.role) {
        return next({ name: 'dashboard' });
    }
    
    next();
});

export default router;
