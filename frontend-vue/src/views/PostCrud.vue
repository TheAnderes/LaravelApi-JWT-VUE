<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <div class="container">
    <h2>CRUD completo</h2>

    <!-- FORMULARIO -->
    <form @submit.prevent="submitForm">
      <input v-model="form.name" type="text" placeholder="Nombre" required />
      <input v-model="form.email" type="email" placeholder="Correo" required />
      <input v-model="form.password" type="password" placeholder="Contraseña" :required="!form.id"
        @input="validarPassword" />
      <!-- Mensaje de error si la contraseña es muy corta -->
      <p v-if="passwordError" style="color: red; font-size: 0.9em;">
        {{ passwordError }}
      </p>
      <button class="botonRegistrar" type="submit"><font-awesome-icon icon="clipboard" /> {{ form.id ? 'Actualizar' :
        'Registrar' }}</button>
      <button v-if="form.id" class="botonCancelar" type="button" @click="cancelEdit"><font-awesome-icon icon="times" />
        Cancelar
        edición</button>
    </form>

    <button @click="logout">Cerrar sesión</button>

    <!-- TABLA DE USUARIOS -->
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Correo</th>
          <th>Contraseña</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>{{ user.password }}</td>
          <td>
            <button class="botonEditar" @click="editarUsuario(user)"><font-awesome-icon icon="pen" /> Editar</button>
            <button class="botonEliminar" @click="borrarUsuario(user.id)"><font-awesome-icon icon="trash" />
              Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from 'axios'; // Importamos Axios para hacer peticiones HTTP
import { useRouter } from 'vue-router';

export default {
  // data() define las variables reactivas del componente
  data() {
    return {
      users: [], // Aquí se almacenará la lista de usuarios que vienen del backend
      form: {    // Este objeto se enlaza al formulario (v-model)
        id: null,       // Si hay un id, estamos editando; si es null, estamos creando
        name: '',       // Nombre del usuario (input)
        email: '',      // Correo del usuario (input)
        password: ''    // Contraseña (input)
      },
      passwordError: '' // Aquí se guarda el mensaje de error
    };
  },

  // mounted() se ejecuta automáticamente cuando el componente se monta en la vista
  mounted() {
    this.obtenerUsuarios(); // Llama a la función que obtiene la lista de usuarios
  },

  // Aquí definimos todos los métodos que el componente puede ejecutar
  methods: {

    // fetchUsers obtiene los usuarios desde la API (GET)
    async obtenerUsuarios() {
      const res = await axios.get('http://localhost:8000/api/users'); // Llamada HTTP a la API
      this.users = res.data; // Guarda la respuesta en la variable "users"
    },

    validarPassword() {
      // Solo validar si estamos creando (no editando)
      if (!this.form.id && this.form.password.length > 0 && this.form.password.length < 6) {
        this.passwordError = 'Contraseña demasiado corta (min: 6 dígitos)';
      } else {
        this.passwordError = '';
      }
    },

    // submitForm maneja tanto creación como edición de usuarios
    async submitForm() {
      // Validar contraseña solo al crear
      if (!this.form.id && this.form.password.length < 6) {
        this.passwordError = 'Contraseña demasiado corta (min: 6 dígitos)';
        return;
      }

      if (this.form.id) {
        // Si hay ID en el formulario, se trata de una edición (PUT)
        await axios.put(`http://localhost:8000/api/users/${this.form.id}`, {
          name: this.form.name,
          email: this.form.email,
          // Si password está vacío, no se envía (para no sobreescribir con vacío)
          password: this.form.password || undefined
        });
      } else {
        // Si no hay ID, se crea un nuevo usuario (POST)
        await axios.post('http://localhost:8000/api/users', this.form);
      }

      // Después de guardar (crear o actualizar):
      this.limpiarForm();     // Limpiamos el formulario
      this.obtenerUsuarios();    // Actualizamos la lista de usuarios
    },

    // editUser llena el formulario con los datos del usuario que se quiere editar
    editarUsuario(user) {
      // Copiamos los datos del usuario al formulario, pero limpiamos la contraseña
      this.form = { ...user, password: '' };
      this.passwordError = '';
    },

    // deleteUser elimina un usuario después de confirmar
    async borrarUsuario(id) {
      if (confirm('¿Estás seguro de eliminar este usuario?')) {
        await axios.delete(`http://localhost:8000/api/users/${id}`); // Llamada DELETE a la API
        this.obtenerUsuarios(); // Actualiza la lista después de eliminar
      }
    },

    // cancelEdicion permite cancelar la edición y volver al modo "crear"
    cancelarEdicion() {
      this.limpiarForm(); // Limpia el formulario
    },

    // limpiarForm limpia todos los campos del formulario y reinicia el modo "crear"
    limpiarForm() {
      this.form = { id: null, name: '', email: '', password: '' };
    }
  },
  setup() {
    const router = useRouter()
    const logout = () => {
      localStorage.removeItem('token')
      router.push('/')
    }
    return { logout }
  }

};
</script>

<style scoped>
.botonRegistrar {
  border-radius: 20px;
  padding: 10px 20px 10px 20px;
  border: none;
  box-shadow: 2px 0px 10px rgba(0, 0, 0, 0.3);
  background: #27AE60;
  color: white;
}

.botonCancelar {
  border-radius: 20px;
  padding: 10px 20px 10px 20px;
  border: none;
  box-shadow: 2px 0px 10px rgba(0, 0, 0, 0.3);
  background: red;
  color: white;
}

.botonEditar {
  border-radius: 20px;
  padding: 10px 20px 10px 20px;
  border: none;
  box-shadow: 2px 0px 10px rgba(0, 0, 0, 0.3);
  background: #F4D03F;
  color: white;
}

.botonEliminar {
  border-radius: 20px;
  padding: 10px 20px 10px 20px;
  border: none;
  box-shadow: 2px 0px 10px rgba(0, 0, 0, 0.3);
  background: red;
  color: white;
}

.container {
  max-width: 1500px;
  margin: auto;
  font-family: sans-serif;
  background-color: #212F3D;
  border-radius: 20px;
  padding: 50px;
}

.container input {
  border-radius: 20px;
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
