import './assets/main.css'
// Font Awesome core
import { library } from '@fortawesome/fontawesome-svg-core'
// Iconos que vas a usar
import { faTrash, faPen, faClipboard, faCheck, faTimes } from '@fortawesome/free-solid-svg-icons'
// Componente de FontAwesome
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { createApp } from 'vue'
import router from './router'
import App from './App.vue'

// Agregar iconos a la librería a utilizar
library.add(faTrash, faPen, faClipboard, faCheck, faTimes)
const app = createApp(App)

// Registra el componente globalmente
app.component('font-awesome-icon', FontAwesomeIcon)
app.use(router)
app.mount('#app')