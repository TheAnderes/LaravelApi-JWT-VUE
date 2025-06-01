// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import UserCrud from '../views/UserCrud.vue'

const routes = [
    { path: '/', component: LoginView },
    { path: '/register', component: RegisterView },
    { path: '/usuarios', component: UserCrud },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
