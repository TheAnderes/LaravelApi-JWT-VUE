<template>
    <div>
        <h2>Iniciar Sesión</h2>
        <form @submit.prevent="login">
            <input v-model="email" type="email" placeholder="Correo" required />
            <input v-model="password" type="password" placeholder="Contraseña" required />
            <button type="submit">Ingresar</button>
        </form>
        <p v-if="error" class="error">{{ error }}</p>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const email = ref('')
const password = ref('')
const error = ref('')
const router = useRouter()

const login = async () => {
    try {
        const res = await api.post('/login', { email: email.value, password: password.value })
        localStorage.setItem('token', res.data.token)
        error.value = ''
        router.push('/usuarios')
    } catch (err) {
        error.value = 'Credenciales incorrectas'
    }
}
</script>

<style scoped>
.error {
    color: red;
}
</style>
