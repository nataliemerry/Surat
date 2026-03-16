<script context="module">
  import Layout, { title } from '@/Shared/Layout.svelte'
  import { inertia, page } from '@inertiajs/svelte'
  export const layout = Layout
</script>

<h1 class="mb-8 text-2xl font-bold sm:text-3xl">
  <a use:inertia href="/surat?type=1" class="text-indigo-400 hover:text-indigo-600"> Surat Tugas </a>
</h1>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
  <a use:inertia href="/surat/tugas/form" class="w-full">
    <div class="flex items-center justify-start w-full h-32 p-4 transition duration-200 bg-white border border-gray-200 cursor-pointer rounded-xl hover:border-indigo-500 hover:shadow-md sm:h-48 sm:flex-col sm:justify-center">
      <div class="flex items-center justify-center mr-4 text-indigo-600 rounded-full h-14 w-14 shrink-0 bg-indigo-50 sm:mb-4 sm:mr-0 sm:h-16 sm:w-16">
        <svg class="h-7 w-7 sm:h-8 sm:w-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 5v14M5 12h14"></path>
        </svg>
      </div>
      <p class="text-sm font-semibold text-left text-gray-700 sm:text-center sm:text-base">Buat Surat Tugas</p>
    </div>
  </a>

  <a use:inertia href="/surat/tugas/upload" class="w-full">
    <div class="flex items-center justify-start w-full h-32 p-4 transition duration-200 bg-white border border-gray-200 cursor-pointer rounded-xl hover:border-indigo-500 hover:shadow-md sm:h-48 sm:flex-col sm:justify-center">
      <div class="flex items-center justify-center mr-4 text-indigo-600 rounded-full h-14 w-14 shrink-0 bg-indigo-50 sm:mb-4 sm:mr-0 sm:h-16 sm:w-16">
        <svg class="h-7 w-7 sm:h-8 sm:w-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"></path>
        </svg>
      </div>
      <p class="text-sm font-semibold text-left text-gray-700 sm:text-center sm:text-base">Upload Dokumen Surat Tugas</p>
    </div>
  </a>
</div>
