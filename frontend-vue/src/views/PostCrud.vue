<template>
  <div class="container">
    <h2>Gestión de Usuarios</h2>

    <!-- FORMULARIO -->
    <form @submit.prevent="submitForm">
      <input v-model="form.name" type="text" placeholder="Nombre" required />
      <input v-model="form.email" type="email" placeholder="Correo" required />
      <input v-model="form.password" type="password" placeholder="Contraseña" :required="!form.id" />
      <button type="submit">{{ form.id ? 'Actualizar' : 'Registrar' }}</button>
      <button v-if="form.id" type="button" @click="cancelEdit">Cancelar</button>
    </form>

    <!-- TABLA DE USUARIOS -->
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Correo</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>
            <button @click="editUser(user)">Editar</button>
            <button @click="deleteUser(user.id)">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      users: [],
      form: {
        id: null,
        name: '',
        email: '',
        password: ''
      }
    };
  },
  mounted() {
    this.fetchUsers();
  },
  methods: {
    async fetchUsers() {
      const res = await axios.get('http://localhost:8000/api/users');
      this.users = res.data;
    },
    async submitForm() {
      if (this.form.id) {
        // UPDATE
        await axios.put(`http://localhost:8000/api/users/${this.form.id}`, {
          name: this.form.name,
          email: this.form.email,
          password: this.form.password || undefined,
        });
      } else {
        // CREATE
        await axios.post('http://localhost:8000/api/users', this.form);
      }
      this.resetForm();
      this.fetchUsers();
    },
    editUser(user) {
      this.form = { ...user, password: '' };
    },
    async deleteUser(id) {
      if (confirm('¿Estás seguro de eliminar este usuario?')) {
        await axios.delete(`http://localhost:8000/api/users/${id}`);
        this.fetchUsers();
      }
    },
    cancelEdit() {
      this.resetForm();
    },
    resetForm() {
      this.form = { id: null, name: '', email: '', password: '' };
    }
  }
};
</script>

<style>
.container {
  max-width: 800px;
  margin: auto;
  font-family: sans-serif;
}

form {
  margin-bottom: 20px;
}

input {
  display: block;
  width: 100%;
  margin: 8px 0;
  padding: 8px;
}

button {
  margin: 4px 4px 10px 0;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th,
td {
  padding: 8px;
  border: 1px solid #ccc;
  text-align: left;
}
</style>
