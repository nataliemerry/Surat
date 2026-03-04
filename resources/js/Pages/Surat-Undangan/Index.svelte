<script context="module">
    import Layout, { title } from '@/Shared/Layout.svelte'
    export const layout = Layout
  </script>
  
  <script>
    import { inertia, useForm } from '@inertiajs/svelte';
    import LoadingButton from '@/Shared/LoadingButton.svelte';
    import SelectInput from '@/Shared/SelectInput.svelte';
    import TextInput from '@/Shared/TextInput.svelte';

    export let kode = [];
    let filteredKode = [];
    
    function filterByType(firstSelect) {
        filteredKode = kode.filter(item => item.value.includes(firstSelect));
    }

    $title = 'Tambah Surat Undangan';

    let form = useForm('CreateSurat', {
        type: 2, 
        firstSelect:null,
        kode:null,
        isRuangan:null,
        isKonsumsi: null,
        isPengelolaan:null,
        perihal:null,
        tujuan:null,
        file: null,
        lnk: null
    });
    
    $: if ($form.firstSelect) {
        filterByType($form.firstSelect);
    }

    function store() {
        $form.post('/surat-undangan/create', {
        forceFormData: true 
    });
    }

</script>

<h1 class="mb-8 text-3xl font-bold">
    <a use:inertia href="/" class="text-indigo-400 hover:text-indigo-600"> Surat Undangan </a>
    <span class="font-medium text-indigo-400">/</span> Create
</h1>

<div class="max-w-3xl overflow-hidden bg-white rounded-md shadow">
    <form on:submit|preventDefault={store}>
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
            <TextInput bind:value={$form.perihal} error={$form.errors.perihal} class="w-full pb-8 pr-6" label="Perihal:" />
            <TextInput bind:value={$form.tujuan} error={$form.errors.tujuan} class="w-full pb-8 pr-6" label="Tujuan:" />
            <SelectInput bind:value={$form.isRuangan} error={$form.errors.isRuangan} class="w-full pb-8 pr-6" label="Apakah menggunakan Ruang Aula">
                <option value={null}>Silakan pilih salah satu opsi</option>
                <option value=1>Ya, Kegiatan menggunakan Ruang Aula</option>
                <option value=0>Tidak, Kegiatan tidak menggunakan Ruang Aula</option>
            </SelectInput>
            <SelectInput bind:value={$form.isKonsumsi} error={$form.errors.isKonsumsi} class="w-full pb-8 pr-6" label="Keperluan Konsumsi">
                <option value={null}>Silakan pilih salah satu opsi</option>
                <option value=1>Ya, Kegiatan membutuhkan konsumsi</option>
                <option value=0>Tidak, Kegiatan tidak membutuhkan konsumsi</option>
            </SelectInput>
            
            {#if $form.isKonsumsi == 1}
                <SelectInput bind:value={$form.isPengelolaan} error={$form.errors.isPengelolaan} class="w-full pb-8 pr-6" label="Siapa yang akan mengelola konsumsi?">
                    <option value={null}>Silakan pilih salah satu opsi</option>
                    <option value=1>Dikelola oleh Umum / TU</option>
                    <option value=0>Dikelola sendiri</option>
                </SelectInput>
            {/if}
            <div class="w-full pb-8 pr-6">
                <label for="file" class="block">Unggah File (.docx):</label>
                <input type="file" accept=".docx" on:input={e => $form.file = e.target.files[0]} class="block w-full mt-1">
                {#if $form.errors.file}
                    <p class="mt-2 text-sm text-red-600">{$form.errors.file}</p>
                {/if}
            </div>
        </div>
        <div class="flex items-center justify-end px-8 py-4 border-t border-gray-100 bg-gray-50">
            <LoadingButton loading={$form.processing} class="btn-indigo" type="submit">Ajukan Surat</LoadingButton>
        </div>
    </form>
</div>
