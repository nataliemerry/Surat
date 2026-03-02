<script context="module">
    import Layout, { title } from '@/Shared/Layout.svelte';
    export const layout = Layout;
</script>

<script>
    import { inertia, useForm } from '@inertiajs/svelte';
    import LoadingButton from '@/Shared/LoadingButton.svelte';
    import SelectInput from '@/Shared/SelectInput.svelte';
    import TextInput from '@/Shared/TextInput.svelte';

    export let surat = [];
    export let kode = []; // List of all possible kode options

    let filteredKode = [];

    // Filter kode options based on firstSelect
    function filterByType(firstSelect) {
        filteredKode = kode.filter(item => item.value.startsWith(firstSelect));
    }

    $title = 'Edit Surat Tugas';

    // Initialize form with data from the database
    let form = useForm(`EditSuratTugas: ${surat.id}`, {
        kode: surat.kode,
        type: surat.type,
        perihal: surat.perihal,
        tujuan: surat.tujuan,
        nomor: surat.nomor,
        filepath: surat.filepath,
        firstSelect: surat.kode ? surat.kode.substring(0, 2) : null, // Extract the first 2 characters
    });

    // Update filteredKode options whenever firstSelect changes
    $: if ($form.firstSelect) {
        filterByType($form.firstSelect);
    }

    // Update form submission handler
    function update() {
        $form.put(`/surat-tugas/${surat.id}`);
    }
</script>

<h1 class="mb-8 text-3xl font-bold">
    <a use:inertia href="/" class="text-indigo-400 hover:text-indigo-600">Surat Tugas</a>
    <span class="font-medium text-indigo-400">/</span> Edit
</h1>

<div class="max-w-3xl overflow-hidden rounded-md bg-white shadow">
    <form on:submit|preventDefault={update}>
        <div class="-mb-8 -mr-6 flex flex-wrap p-8">
            <TextInput bind:value={$form.perihal} error={$form.errors.perihal} class="w-full pb-8 pr-6" label="Perihal:" />
            <TextInput bind:value={$form.tujuan} error={$form.errors.tujuan} class="w-full pb-8 pr-6" label="Tujuan:" />
            <TextInput bind:value={$form.nomor} error={$form.errors.nomor} class="w-full pb-8 pr-6" label="Tujuan:" />
            <!-- First Select: Kode Arsip Utama -->
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
        </div>

        <div class="flex items-center justify-end border-t border-gray-100 bg-gray-50 px-8 py-4">
            <LoadingButton loading={$form.processing} class="btn-indigo" type="submit">Ajukan Surat</LoadingButton>
        </div>
    </form>
</div>