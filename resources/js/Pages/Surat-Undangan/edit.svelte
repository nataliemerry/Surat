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
    filteredKode = kode.filter((item) => item.value.includes(firstSelect))
  }

  $title = 'Edit Surat Undangan'

  let form = useForm(`EditSuratUndangan: ${surat.id}`, {
    _method: 'put',
    type: surat.type,
    firstSelect: surat.kode ? surat.kode.substring(0, 2) : null,
    kode: surat.kode,
    isRahasia: surat.isRahasia !== null ? String(surat.isRahasia) : '0',
    perihal: surat.perihal,
    tujuan: surat.tujuan,
    isRuangan: surat.isRuangan !== null ? String(surat.isRuangan) : null,
    isKonsumsi: surat.isKonsumsi !== null ? String(surat.isKonsumsi) : null,
    isPengelolaan: surat.isPengelolaan !== null ? String(surat.isPengelolaan) : null,
    tanggal_pelaksanaan: surat.tanggal_pelaksanaan || null,
    filepath: surat.filepath,
    link: surat.link,
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
    $form.post(`/surat-undangan/${surat.id}`, {
      forceFormData: true,
    })
  }
</script>

<h1 class="mb-8 text-3xl font-bold">
  <a use:inertia href="/?type=2" class="text-indigo-400 hover:text-indigo-600"> Surat Undangan </a>
  <span class="font-medium text-indigo-400">/</span> Edit
</h1>

<div class="max-w-3xl overflow-hidden bg-white rounded-md shadow">
  <form on:submit|preventDefault={update}>
    <div class="flex flex-wrap p-8 -mb-8 -mr-6">
      <SelectInput bind:value={$form.firstSelect} error={$form.errors.firstSelect} class="w-full pb-8 pr-6" label="Kode Arsip Utama">
        <option value={null}>Silakan pilih salah satu opsi</option>
        <option value="PS">PS - Perumusan Kebijakan di Bidang Statistik</option>
        <option value="SS">SS - Sensus Penduduk, Sensus Pertanian dan Sensus Ekonomi</option>
        <option value="VS">VS - Survei</option>
        <option value="KS">KS - Konsolidasi Data Statistik</option>
        <option value="ES">ES - Evaluasi dan Pelaporan Sensus, Survei dan Konsolidasi Data</option>
        <option value="KU">KU - Keuangan</option>
        <option value="KP">KP - Kepegawaian</option>
        <option value="PR">PR - Perencanaan</option>
        <option value="HK">HK - Hukum</option>
        <option value="OT">OT - Organisasi dan Tata Laksana</option>
        <option value="HM">HM - Hubungan Masyarakat</option>
        <option value="KA">KA - Kearsipan</option>
        <option value="RT">RT - Kerumahtanggaan</option>
        <option value="PL">PL - Perlengkapan</option>
        <option value="DL">DL - Pendidikan dan Pelatihan</option>
        <option value="PK">PK - Kepustakaan</option>
        <option value="IF">IF - Informatika</option>
        <option value="PW">PW - Pengawasan</option>
        <option value="TS">TS - Transformasi Statistik</option>
      </SelectInput>
      {#if $form.firstSelect}
        <SelectInput bind:value={$form.kode} error={$form.errors.kode} class="w-full pb-8 pr-6" label="Data Berdasarkan Kegiatan:">
          <option value={null}>Silakan pilih salah satu opsi</option>
          {#each filteredKode as option}
            <option value={option.value}>{option.text}</option>
          {/each}
        </SelectInput>
      {/if}
      <SelectInput bind:value={$form.isRahasia} error={$form.errors.isRahasia} class="w-full pb-8 pr-6" label="Sifat Surat:">
        <option value="0">Biasa</option>
        <option value="1">Rahasia</option>
      </SelectInput>
      <TextInput bind:value={$form.perihal} error={$form.errors.perihal} class="w-full pb-8 pr-6" label="Perihal:" />
      <TextInput bind:value={$form.tujuan} error={$form.errors.tujuan} class="w-full pb-8 pr-6" label="Tujuan:" />
      <div class="w-full pb-8 pr-6">
        <label for="tanggal_pelaksanaan" class="form-label">Tanggal Pelaksanaan:</label>
        <input id="tanggal_pelaksanaan" type="date" bind:value={$form.tanggal_pelaksanaan} class="form-input" />
        {#if $form.errors.tanggal_pelaksanaan}
          <div class="form-error">{$form.errors.tanggal_pelaksanaan}</div>
        {/if}
      </div>
      <SelectInput bind:value={$form.isRuangan} error={$form.errors.isRuangan} class="w-full pb-8 pr-6" label="Apakah menggunakan Ruang Aula?">
        <option value={null}>Silakan pilih salah satu opsi</option>
        <option value="1">Ya, Kegiatan menggunakan Ruang Aula</option>
        <option value="0">Tidak, Kegiatan tidak menggunakan Ruang Aula</option>
      </SelectInput>
      <SelectInput bind:value={$form.isKonsumsi} error={$form.errors.isKonsumsi} class="w-full pb-8 pr-6" label="Keperluan Konsumsi">
        <option value={null}>Silakan pilih salah satu opsi</option>
        <option value="1">Ya, Kegiatan membutuhkan konsumsi</option>
        <option value="0">Tidak, Kegiatan tidak membutuhkan konsumsi</option>
      </SelectInput>
      {#if $form.isKonsumsi == 1}
        <SelectInput bind:value={$form.isPengelolaan} error={$form.errors.isPengelolaan} class="w-full pb-8 pr-6" label="Siapa yang akan mengelola konsumsi?">
          <option value={null}>Silakan pilih salah satu opsi</option>
          <option value="1">Dikelola oleh Umum / TU</option>
          <option value="0">Dikelola sendiri</option>
        </SelectInput>
      {/if}
      <div class="w-full pb-8 pr-6">
        <label for="file" class="block text-sm font-medium text-gray-700">File (.docx, .pdf):</label>

        <!-- Fancy file upload button -->
        <div class="flex items-center mt-3">
          <label for="file" class="inline-flex items-center px-4 py-2 text-indigo-500 transition border border-indigo-500 rounded-md shadow-sm cursor-pointer hover:bg-indigo-500 hover:text-white focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
    <div class="flex items-center justify-end px-8 py-4 border-t border-gray-100 bg-gray-50">
      <LoadingButton loading={$form.processing} class="btn-indigo hover:bg-indigo-700" type="submit">Simpan Perubahan</LoadingButton>
    </div>
  </form>
</div>
