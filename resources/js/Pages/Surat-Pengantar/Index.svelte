<!-- flashcard yang jadi -->
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


    $title = 'Tambah Surat Pengantar';

    let form = useForm('CreateSurat', {
        type: 1, 
        firstSelect:null,
        kode:null,
        isRuangan:null,
        isKonsumsi: null,
        isPengelolaan:null,
        perihal:null,
        tujuan:null,
        file: null
    });
    
    $: if ($form.firstSelect) {
        filterByType($form.firstSelect);
    }

    function store() {
        $form.post('/surat-pengantar/create', {
        forceFormData: true 
    });
    }

</script>

<h1 class="mb-8 text-3xl font-bold">
    <a use:inertia href="/" class="text-indigo-400 hover:text-indigo-600"> Surat Pengantar </a>
    <span class="font-medium text-indigo-400">/</span> Create
</h1>

<div class="max-w-3xl overflow-hidden rounded-md bg-white shadow">
    <form on:submit|preventDefault={store}>
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
                <label for="file" class="block">Upload File (.docx):</label>
                <input type="file" accept=".docx" on:input={e => $form.file = e.target.files[0]} class="mt-1 block w-full">
                {#if $form.errors.file}
                    <p class="mt-2 text-sm text-red-600">{$form.errors.file}</p>
                {/if}
            </div>
        </div>
        <div class="flex items-center justify-end border-t border-gray-100 bg-gray-50 px-8 py-4">
            <LoadingButton loading={$form.processing} class="btn-indigo" type="submit">Ajukan Surat</LoadingButton>
        </div>
    </form>
</div>
