<template>
    <div>
        <h2>Registro</h2>
        <form @submit.prevent="register">
            <input v-model="name" type="text" placeholder="Nombre" required />
            <input v-model="email" type="email" placeholder="Correo" required />
            <input v-model="password" type="password" placeholder="Contraseña" required />
            <button type="submit">Registrarse</button>
        </form>
        <p v-if="error" class="error">{{ error }}</p>
        <p v-if="success" class="success">{{ success }}</p>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '../services/api'

const name = ref('')
const email = ref('')
const password = ref('')
const error = ref('')
const success = ref('')

const register = async () => {
    try {
        await api.post('/register', {
            name: name.value,
            email: email.value,
            password: password.value
        })
        success.value = 'Registro exitoso. Ahora puedes iniciar sesión.'
        error.value = ''
    } catch (err) {
        error.value = 'Error al registrar usuario'
        success.value = ''
    }
}
</script>

<style scoped>
.error {
    color: red;
}

.success {
    color: green;
}
</style>
