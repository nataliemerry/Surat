import '../css/app.css'
import axios from 'axios'
import { createInertiaApp } from '@inertiajs/svelte'

axios.defaults.withCredentials = true
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

const token = document.querySelector('meta[name="csrf-token"]')

if (token) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content')
}

createInertiaApp({
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.svelte', { eager: true })
    return pages[`./Pages/${name}.svelte`]
  },
  setup({ el, App, props }) {
    new App({ target: el, props })
  },
})