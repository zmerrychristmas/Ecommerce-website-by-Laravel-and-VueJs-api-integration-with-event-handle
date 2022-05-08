import Vue from 'vue'
import VueRouter from 'vue-router'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faCheckCircle, faExclamationCircle, fas } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(faCheckCircle, faExclamationCircle, fas)

Vue.component('font-awesome-icon', FontAwesomeIcon)

Vue.use(VueRouter)

import App from './views/App'
import Home from './views/Home'
import Login from './views/Login'
import Register from './views/Register'
import SingleProduct from './views/SingleProduct'
import Checkout from './views/Checkout'
import Confirmation from './views/Confirmation'
import UserBoard from './views/UserBoard'
import Admin from './views/Admin'
import FlashSale from './views/FlashSale'

const router = new VueRouter({
    mode: 'history',
    routes: [{
            path: '/',
            name: 'home',
            component: Home
        },
        {
            path: '/login',
            name: 'login',
            component: Login
        },
        {
            path: '/register',
            name: 'register',
            component: Register
        },
        {
            path: '/products/:id',
            name: 'single-products',
            component: SingleProduct
        },
        {
            path: '/confirmation',
            name: 'confirmation',
            component: Confirmation
        },
        {
            path: '/checkout',
            name: 'checkout',
            component: Checkout,
            props: (route) => ({ pid: route.query.pid })
        },
        {
            path: '/flashsale',
            name: 'flashsale',
            component: FlashSale,
            meta: {
                is_user: true
            },
            props: (route) => ({ pid: route.query })
        },
        {
            path: '/dashboard',
            name: 'userboard',
            component: UserBoard,
            meta: {
                requiresAuth: true,
                is_user: true
            }
        },
        {
            path: '/admin/:page',
            name: 'admin-pages',
            component: Admin,
            meta: {
                requiresAuth: true,
                is_admin: true
            }
        },
        {
            path: '/admin',
            name: 'admin',
            component: Admin,
            meta: {
                requiresAuth: true,
                is_admin: true
            }
        },
    ],
})

router.beforeEach((to, from, next) => {
    console.log(to, next);
    if (to.matched.some(record => record.meta.requiresAuth)) {
        console.log(1)
        if (localStorage.getItem('l-ecommerce.jwt') == null) {
            console.log(2)
            next({
                path: '/login',
                params: { nextUrl: to.fullPath }
            })
        } else {
            console.log(3)
            let user = JSON.parse(localStorage.getItem('l-ecommerce.user'))
            console.log(user)
            if (to.matched.some(record => record.meta.is_admin)) {
                console.log(4)
                if (user.is_admin == 1) {
                    console.log(5)
                    next()
                } else {
                    console.log(6)
                    next({ name: 'userboard' })
                }
            } else if (to.matched.some(record => record.meta.is_user)) {
                var role = user.is_admin != undefined ? user.is_admin : 0
                console.log(7)
                if (role == 0) {
                    console.log(8)
                    next()
                } else {
                    console.log(9)
                    next({ name: 'admin' })
                }
            }
            next()
        }
    } else {
        next()
    }
})

const app = new Vue({
    el: '#app',
    components: { App },
    router,
});