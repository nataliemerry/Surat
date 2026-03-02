<script context="module">
  import Layout, { title } from '@/Shared/Layout.svelte'
  import { inertia, page } from '@inertiajs/svelte'
  export const layout = Layout
</script>

<h1 class="mb-8 text-3xl font-bold">
  <a use:inertia href="/" class="text-indigo-400 hover:text-indigo-600"> Surat Tugas </a>
  <span class="font-medium text-indigo-400">/</span> Create
</h1>

<div class="flex space-x-4">
  <a use:inertia href="/surat-tugas/form">
    <div class="flex h-48 w-48 cursor-pointer flex-col items-center justify-center rounded-lg border border-gray-300 transition duration-200 hover:border-2 hover:border-indigo-500">
      <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-200">
        <svg class="h-8 w-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 5v14M5 12h14"></path>
        </svg>
      </div>
      <p class="text-center font-semibold text-gray-700">Buat Surat Tugas</p>
    </div>
  </a>

  <a use:inertia href="/surat-tugas/upload-surat">
    <div class="flex h-48 w-48 cursor-pointer flex-col items-center justify-center rounded-lg border border-gray-300 transition duration-200 hover:border-2 hover:border-indigo-500">
      <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-200">
        <svg class="h-8 w-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"></path>
        </svg>
      </div>
      <p class="text-center font-semibold text-gray-700">Upload Draft Surat Tugas</p>
    </div>
  </a>
</div>
