<script context="module">
  import Layout, { title } from '@/Shared/Layout.svelte';
  export const layout = Layout;
</script>

<script>
  import Pagination from '@/Shared/Pagination.svelte';
  export let surat = [];
  export let auth;

  let filteredSurat = surat.filter(item => item.type === 1);
  let paginatedSurat = [];
  let currentPage = 1;
  let itemsPerPage = 10;
  let totalPages = 1;
  let activeType = 1;

  $title = 'Dashboard';

  function filterByType(type) {
    activeType = type;
    filteredSurat = surat.filter(item => item.type === type);
    currentPage = 1;
    updatePagination();
  }

  function updatePagination() {
    totalPages = Math.ceil(filteredSurat.length / itemsPerPage);
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    paginatedSurat = filteredSurat.slice(start, end);
  }

  function changePage(page) {
    currentPage = page;
    updatePagination();
  }

  $: updatePagination();

  function downloadFile(filepath) {
    const link = document.createElement('a');
    link.href = filepath;
    link.download = 'Draft Surat';
    link.click();
  }

  function getEditUrl(type, id) {
    switch (type) {
      case 1:
        return `/surat-tugas/${id}/edit`;
      case 2:
        return `/surat-undangan/${id}/edit`;
      case 3:
        return `/surat-permohonan/${id}/edit`;
      default:
        return '#';
    }
  }
</script>

<h1 class="mb-4 text-3xl font-bold">Riwayat Surat yang Diajukan</h1>
<div class="mb-4 flex gap-2">
  <button
    class={`px-4 py-2 rounded focus:outline-none ${activeType === 1 ? 'text-white bg-[#5661B3] hover:bg-[#2F365F] font-bold' : 'text-[#374151] bg-white hover:bg-gray-100'}`}
    on:click={() => filterByType(1)}>
    Surat Tugas
  </button>
  <button
    class={`px-4 py-2 rounded focus:outline-none ${activeType === 2 ? 'text-white bg-[#5661B3] hover:bg-[#2F365F] font-bold' : 'text-[#374151] bg-white hover:bg-gray-100'}`}
    on:click={() => filterByType(2)}>
    Surat Undangan
  </button>
  <button
    class={`px-4 py-2 rounded focus:outline-none ${activeType === 3 ? 'text-white bg-[#5661B3] hover:bg-[#2F365F] font-bold' : 'text-[#374151] bg-white hover:bg-gray-100'}`}
    on:click={() => filterByType(3)}>
    Surat Permohonan
  </button>
</div>
<div class="overflow-x-auto rounded-md bg-white shadow">
  <table class="w-full whitespace-nowrap">
    <thead>
      <tr class="text-left font-bold">
        <th class="px-6 pb-4 pt-6">Tanggal</th>
        <th class="px-6 pb-4 pt-6">Kode Arsip</th>
        {#if activeType === 1}
          <th class="px-6 pb-4 pt-6">Untuk (Kegiatan)</th>
          <th class="px-6 pb-4 pt-6">Kepada</th>
          <th class="px-6 pb-4 pt-6">Nomor Surat</th>
        {:else if activeType === 2}
          <th class="px-6 pb-4 pt-6">Perihal</th>
          <th class="px-6 pb-4 pt-6">Tujuan</th>
          <th class="px-6 pb-4 pt-6">Keperluan Konsumsi</th>
          <th class="px-6 pb-4 pt-6">Pengelolaan Konsumsi</th>
          <th class="px-6 pb-4 pt-6">Penggunaan Ruang</th>
        {:else if activeType === 3}
          <th class="px-6 pb-4 pt-6">Perihal</th>
          <th class="px-6 pb-4 pt-6">Tujuan</th>
        {/if}
        <th class="px-6 pb-4 pt-6">Draft Surat</th>
      </tr>
    </thead>
    <tbody>
      {#each paginatedSurat as surat (surat.id)}
        {#if auth}
          <tr
            class="focus-within:bg-gray-100 hover:bg-gray-100 cursor-pointer"
            on:click={() => window.location.href = getEditUrl(surat.type, surat.id)}
          >
            <td class="border-t">
              <p class="flex items-center px-6 py-4">{surat.created_at}</p>
            </td>
            <td class="border-t">
              <p class="flex items-center px-6 py-4" tabindex="-1">{surat.kode || ''}</p>
            </td>
            {#if activeType === 1}
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.perihal || ''}</p>
              </td>
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.tujuan || ''}</p>
              </td>
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.nomor || ''}</p>
              </td>
            {:else if activeType === 2}
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.perihal || ''}</p>
              </td>
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.tujuan || ''}</p>
              </td>
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.isKonsumsi ? 'Iya' : 'Tidak'}</p>
              </td>
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.isPengelolaan ? 'Iya' : 'Tidak'}</p>
              </td>
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.isRuangan ? 'Iya' : 'Tidak'}</p>
              </td>
            {:else if activeType === 3}
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.perihal || ''}</p>
              </td>
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.tujuan || ''}</p>
              </td>
            {/if}
            <td class="border-t px-6">
              {#if surat.filepath}
                <button
                  class="px-4 py-2 text-white bg-[#5661B3] rounded hover:bg-[#2F365F]"
                  on:click={(e) => { e.stopPropagation(); downloadFile(surat.filepath); }}
                >
                  Download
                </button>
              {:else}
                <p class=" text-gray-500">Belum Ada Draft Surat</p>
              {/if}
            </td>
            <td class="border-t px-6 text-right">
              <span class="text-gray-500">&rarr;</span>
            </td>
          </tr>
        {:else}
          <tr class="focus-within:bg-gray-100 hover:bg-gray-100">
            <td class="border-t">
              <p class="flex items-center px-6 py-4">{surat.created_at}</p>
            </td>
            <td class="border-t">
              <p class="flex items-center px-6 py-4" tabindex="-1">{surat.kode || ''}</p>
            </td>
            {#if activeType === 1}
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.perihal || ''}</p>
              </td>
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.tujuan || ''}</p>
              </td>
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.nomor || ''}</p>
              </td>
            {:else if activeType === 2}
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.perihal || ''}</p>
              </td>
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.tujuan || ''}</p>
              </td>
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.isKonsumsi ? 'Iya' : 'Tidak'}</p>
              </td>
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.isPengelolaan ? 'Iya' : 'Tidak'}</p>
              </td>
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.isRuangan ? 'Iya' : 'Tidak'}</p>
              </td>
            {:else if activeType === 3}
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.perihal || ''}</p>
              </td>
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.tujuan || ''}</p>
              </td>
            {/if}
            <td class="border-t px-6">
              {#if surat.filepath}
                <button
                  class="px-4 py-2 text-white bg-[#5661B3] rounded hover:bg-[#2F365F]"
                  on:click={(e) => { e.stopPropagation(); downloadFile(surat.filepath); }}
                >
                  Download
                </button>
              {:else}
                <p class=" text-gray-500">Belum Ada Draft Surat</p>
              {/if}
            </td>
          </tr>
        {/if}
      {/each}
      {#if paginatedSurat.length === 0}
        <tr>
          <td class="border-t px-6 py-4" colspan="10">Belum Ada Surat yang Diajukan.</td>
        </tr>
      {/if}
    </tbody>
  </table>
</div>
<Pagination
  totalPages={totalPages}
  currentPage={currentPage}
  onChange={changePage}
/>