<script context="module">
  import Layout, { title } from '@/Shared/Layout.svelte'
  export const layout = Layout
</script>

<script>
  import { inertia, useForm } from '@inertiajs/svelte'
  import LoadingButton from '@/Shared/LoadingButton.svelte'
  import TextInput from '@/Shared/TextInput.svelte'

  $title = 'Tambah Surat Tugas'

  let form = useForm('CreateSurat', {
    nomor: null,
    file: null,
  })

  let selectedFileName = 'No file chosen'

  function handleFileChange(e) {
    const file = e.target.files[0]
    if (file) {
      $form.file = file
      selectedFileName = file.name
    } else {
      selectedFileName = 'No file chosen'
    }
  }

  function store() {
    $form.post('/surat-tugas/update', {
      forceFormData: true,
    })
  }
</script>

<h1 class="mb-8 text-3xl font-bold">
  <a use:inertia href="/" class="text-indigo-400 hover:text-indigo-600"> Surat Tugas </a>
  <span class="font-medium text-indigo-400">/</span> Create
</h1>

<div class="max-w-3xl overflow-hidden rounded-md bg-white shadow">
  <form on:submit|preventDefault={store}>
    <div class="-mb-8 -mr-6 flex flex-wrap p-8">
      <TextInput bind:value={$form.nomor} error={$form.errors.nomor} class="w-full pb-8 pr-6" label="Nomor Surat Tugas:" />
      <div class="w-full pb-8 pr-6">
        <label for="file" class="block text-sm font-medium text-gray-700">Upload File Draft Surat (.docx):</label>

        <!-- Fancy file upload button -->
        <div class="mt-3 flex items-center">
          <label for="file" class="inline-flex cursor-pointer items-center rounded-md border border-indigo-500 px-4 py-2 text-indigo-500 shadow-sm transition hover:bg-indigo-500 hover:text-white focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <svg class="mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"></path>
            </svg>
            Choose File
          </label>
          <input id="file" type="file" accept=".docx" on:input={handleFileChange} class="hidden" />
          <span class="ml-4 text-sm text-gray-500">{selectedFileName}</span>
        </div>

        {#if $form.errors.file}
          <p class="mt-2 text-sm text-red-600">{$form.errors.file}</p>
        {/if}
      </div>
    </div>
    <div class="flex items-center justify-end border-t border-gray-100 bg-gray-50 px-8 py-4">
      <LoadingButton loading={$form.processing} class="btn-indigo" type="submit">Upload Draft Surat Tugas</LoadingButton>
    </div>
  </form>
</div>
