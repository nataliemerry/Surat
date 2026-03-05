<script context="module">
  import Layout, { title } from '@/Shared/Layout.svelte'
  export const layout = Layout
</script>

<script>
  import { inertia, useForm } from '@inertiajs/svelte'
  import LoadingButton from '@/Shared/LoadingButton.svelte'
  import SelectInput from '@/Shared/SelectInput.svelte'
  import TextInput from '@/Shared/TextInput.svelte'

  export let surat = {}
  export let kode = []

  let filteredKode = []

  function filterByType(firstSelect) {
    filteredKode = kode.filter((item) => item.value.startsWith(firstSelect))
  }

  $title = 'Edit Surat Tugas'

  let form = useForm(`EditSuratTugas: ${surat.id}`, {
    _method: 'put',
    type: surat.type,
    kode: surat.kode,
    perihal: surat.perihal,
    tujuan: surat.tujuan,
    nomor: surat.nomor,
    filepath: surat.filepath,
    firstSelect: surat.kode ? surat.kode.substring(0, 2) : null,
    file: null,
  })

  let selectedFileName = surat.original_filename || (surat.filepath ? surat.filepath.split('/').pop() : 'No file chosen')

  function handleFileChange(e) {
    const file = e.target.files[0]
    if (file) {
      $form.file = file
      selectedFileName = file.name
    } else {
      selectedFileName = surat.original_filename || (surat.filepath ? surat.filepath.split('/').pop() : 'No file chosen')
      $form.file = null
    }
  }

  $: if ($form.firstSelect) {
    filterByType($form.firstSelect)
  }

  function update() {
    $form.post(`/surat-tugas/${surat.id}`, {
      forceFormData: true,
    })
  }
</script>

<h1 class="mb-8 text-3xl font-bold">
  <a use:inertia href="/" class="text-indigo-400 hover:text-indigo-600">Surat Tugas</a>
  <span class="font-medium text-indigo-400">/</span> Edit
</h1>

<div class="max-w-3xl overflow-hidden rounded-md bg-white shadow">
  <form on:submit|preventDefault={update}>
    <div class="-mb-8 -mr-6 flex flex-wrap p-8">
      <SelectInput bind:value={$form.firstSelect} error={$form.errors.firstSelect} class="w-full pb-8 pr-6" label="Kode Arsip Utama">
        <option value={null}>Select an option</option>
        <option value="PS">PS - Pengkajian dan Pengusulan Kebijakan</option>
        <option value="SS">SS - Perencanaan</option>
        <option value="VS">VS - Perencanaan</option>
        <option value="KS">KS - Kompilasi data</option>
        <option value="KU">KU - Pelaksanaan Anggaran</option>
        <option value="KP">KP - Formasi Pegawai</option>
        <option value="PR">PR - Pokok-Pokok Kebijakan dan Strategi Pembangunan</option>
        <option value="HK">HK - Program Legislasi</option>
        <option value="OT">OT - Organisasi</option>
        <option value="HM">HM - Keprotokolan</option>
        <option value="KA">KA - Pencetakan</option>
        <option value="RT">RT - Administrasi penggunaan / langganan peralatan telekomunikasi</option>
        <option value="PL">PL - Rencana Kebutuhan Barang dan Jasa</option>
        <option value="DL">DL - Perencanaan Diklat</option>
        <option value="PK">PK - Penyimpanan Deposit Bahan Pustaka</option>
        <option value="IF">IF - Rencana Strategis Masterplan Pembangunan Sistem Informasi</option>
        <option value="PW">PW - Rencana Pengawasan</option>
        <option value="TS">TS - Penyusunan Rencana Kegiatan Bidang Transformasi Statistik ( TOR )</option>
      </SelectInput>

      {#if $form.firstSelect}
        <SelectInput bind:value={$form.kode} error={$form.errors.kode} class="w-full pb-8 pr-6" label="Data Berdasarkan Kegiatan:">
          <option value={null}>Select an option</option>
          {#each filteredKode as option}
            <option value={option.value}>{option.text}</option>
          {/each}
        </SelectInput>
      {/if}
      <TextInput bind:value={$form.perihal} error={$form.errors.perihal} class="w-full pb-8 pr-6" label="Perihal:" />
      <TextInput bind:value={$form.tujuan} error={$form.errors.tujuan} class="w-full pb-8 pr-6" label="Tujuan:" />
      <div class="w-full pb-8 pr-6">
        <label for="file" class="block text-sm font-medium text-gray-700">File (.docx,.pdf):</label>
        <div class="mt-3 flex items-center">
          <label for="file" class="inline-flex cursor-pointer items-center rounded-md border border-indigo-500 px-4 py-2 text-indigo-500 shadow-sm transition hover:bg-indigo-500 hover:text-white focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <svg class="mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"></path>
            </svg>
            Choose File
          </label>
          <input id="file" type="file" accept=".docx,.pdf" on:input={handleFileChange} class="hidden" />
          <span class="ml-4 text-sm text-gray-500">{selectedFileName}</span>
        </div>
        {#if $form.errors.file}
          <p class="mt-2 text-sm text-red-600">{$form.errors.file}</p>
        {/if}
      </div>
    </div>
    <div class="flex items-center justify-end border-t border-gray-100 bg-gray-50 px-8 py-4">
      <LoadingButton loading={$form.processing} class="btn-indigo hover:bg-indigo-700" type="submit">Simpan Perubahan</LoadingButton>
    </div>
  </form>
</div>
