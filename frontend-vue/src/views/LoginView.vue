<template>
    <div>
        <h2>Iniciar Sesión</h2>
        <form @submit.prevent="login">
            <input v-model="email" type="email" placeholder="Correo" required />
            <input v-model="password" type="password" placeholder="Contraseña" required />
            <button type="submit" :disabled="loading">
                {{ loading ? 'Ingresando...' : 'Ingresar' }}
            </button>
        </form>
        <p v-if="error" class="error">{{ error }}</p>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api' // Dirección de donde nos conectamos a la API de Laravel siempre apuntando a la dirección de donde provee

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)
const router = useRouter()

const login = async () => {
    loading.value = true
    error.value = ''

    try {
        const { data } = await api.post('/login', {
            email: email.value,
            password: password.value
        })

        if (data.token) {
            // Guarda el token
            localStorage.setItem('token', data.token)

            // Configura el token para futuras peticiones, realiza la obtención de datos
            api.defaults.headers.common['Authorization'] = `Bearer ${data.token}`

            // Obtiene los datos del usuario autenticado
            const me = await api.get('/me')

            // Accede al nombre y correo
            const nombre = me.data.name
            const correo = me.data.email
            
            // Se guarda el usuario o los datos del usuarioo en el localStorage
            localStorage.setItem('usuario', JSON.stringify(me.data))

            console.log('Nombre:', nombre)
            console.log('Correo:', correo)
            router.push('/usuarios')
        } else {
            error.value = 'No se recibió un token válido del servidor.'
        }
    } catch (err) {
        console.log('Error completo:', err);
        if (err.response) {
            if (err.response.status === 401) {
                error.value = 'Credenciales incorrectas.'
            } else if (err.response.status === 422) {
                error.value = 'Datos inválidos: revisa los campos ingresados.'
            } else {
                error.value = `Error del servidor: ${err.response.status}`
            }
        } else {
            error.value = 'No se pudo conectar con el servidor.'
        }
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
.error {
    color: red;
    margin-top: 10px;
}
</style>
