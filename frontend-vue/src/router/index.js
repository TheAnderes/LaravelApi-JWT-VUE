import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import UserCrud from '../views/PostCrud.vue'

const routes = [
    { path: '/', component: LoginView },
    {
        path: '/soloregistrar', component: RegisterView, meta: { requiresAuth: true } // Indica que requiere el Token
    },
    {
        path: '/usuarios', component: UserCrud, meta: { requiresAuth: true } // Indica que requiere el Token
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

// Revisa si existe un toquen de usuario en el localStorage, lo que hace que protega todas las rutas que tengan seguridad y rediriga a la principal si no tiene token
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token')
    if (to.meta.requiresAuth && !token) {
        // Si intenta acceder a una ruta protegida sin token, redirige al login
        return next('/')
    }
    // Si no requiere auth o sí hay token, deja pasar
    next()
})

export default router
