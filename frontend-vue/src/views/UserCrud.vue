<template>
    <div>
        <h2>CRUD de Usuarios</h2>

        <form @submit.prevent="guardar">
            <input v-model="form.name" placeholder="Nombre" required />
            <input v-model="form.email" placeholder="Correo" required />
            <input v-model="form.password" type="password" placeholder="Contraseña" v-if="!form.id" required />
            <button type="submit">{{ form.id ? 'Actualizar' : 'Crear' }}</button>
            <button v-if="form.id" type="button" @click="cancelar">Cancelar</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="u in usuarios" :key="u.id">
                    <td>{{ u.name }}</td>
                    <td>{{ u.email }}</td>
                    <td>
                        <button @click="editar(u)">✏️</button>
                        <button @click="eliminar(u.id)">🗑️</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'

const usuarios = ref([])
const form = ref({ id: null, name: '', email: '', password: '' })

const obtenerUsuarios = async () => {
    const res = await api.get('/users')
    usuarios.value = res.data
}

const guardar = async () => {
    if (form.value.id) {
        await api.put(`/users/${form.value.id}`, {
            name: form.value.name,
            email: form.value.email
        })
    } else {
        await api.post('/users', {
            name: form.value.name,
            email: form.value.email,
            password: form.value.password
        })
    }
    limpiar()
    obtenerUsuarios()
}

const editar = (user) => {
    form.value = { ...user, password: '' }
}

const eliminar = async (id) => {
    if (confirm('¿Eliminar usuario?')) {
        await api.delete(`/users/${id}`)
        obtenerUsuarios()
    }
}

const cancelar = () => limpiar()

const limpiar = () => {
    form.value = { id: null, name: '', email: '', password: '' }
}

onMounted(obtenerUsuarios)
</script>
