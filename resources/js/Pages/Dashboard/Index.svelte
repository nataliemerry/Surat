<script context="module">
  import Layout, { title } from '@/Shared/Layout.svelte'
  export const layout = Layout
</script>

<script>
  import { onMount } from 'svelte'
  import Pagination from '@/Shared/Pagination.svelte'
  import { router, page } from '@inertiajs/svelte'
  import { Pencil, Trash2 } from 'lucide-svelte'

  let toastMessage = ''
  let showToast = false
  let toastTimeout

  let bannerMessage = ''
  let showBanner = false
  let bannerTimeout

  function triggerToast(message) {
    toastMessage = message
    showToast = true
    clearTimeout(toastTimeout)
    toastTimeout = setTimeout(() => {
      showToast = false
    }, 3500)
  }

  function triggerBanner(message) {
    bannerMessage = message
    showBanner = true
    clearTimeout(bannerTimeout)
    bannerTimeout = setTimeout(() => {
      showBanner = false
    }, 10000)
  }

  let lastFlashMessage = ''

  $: if ($page?.props?.flash?.success) {
    const msg = $page.props.flash.success
    if (msg && msg !== lastFlashMessage) {
      lastFlashMessage = msg
      if (msg.includes('Berhasil Dibuat') || msg.includes('Nomor Surat')) {
        triggerBanner(msg)
      } else {
        triggerToast(msg)
      }
    }
  }
  export let surat = []
  export let auth

  let filteredSurat = surat.filter((item) => item.type === 1)
  let paginatedSurat = []
  let currentPage = 1
  let itemsPerPage = 10
  let totalPages = 1
  let activeType = 1

  $title = 'Dashboard'

  function filterByType(type) {
    activeType = type
    filteredSurat = surat.filter((item) => item.type === type)
    currentPage = 1
    updatePagination()
  }

  function updatePagination() {
    totalPages = Math.ceil(filteredSurat.length / itemsPerPage)
    const start = (currentPage - 1) * itemsPerPage
    const end = start + itemsPerPage
    paginatedSurat = filteredSurat.slice(start, end)
  }

  function changePage(page) {
    currentPage = page
    updatePagination()
  }

  $: updatePagination()

  onMount(() => {
    const params = new URLSearchParams(window.location.search)
    const type = parseInt(params.get('type'))
    if (type === 1 || type === 2 || type === 3) {
      filterByType(type)
    }
  })

  function downloadFile(filepath) {
    const link = document.createElement('a')
    link.href = filepath
    link.download = 'Draft Surat'
    link.click()
  }

  function getEditUrl(type, id) {
    switch (type) {
      case 1:
        return `/surat-tugas/${id}/edit`
      case 2:
        return `/surat-undangan/${id}/edit`
      case 3:
        return `/surat-dinas/${id}/edit`
      default:
        return '#'
    }
  }

  function getDeleteUrl(type, id) {
    switch (type) {
      case 1:
        return `/surat-tugas/${id}`
      case 2:
        return `/surat-undangan/${id}`
      case 3:
        return `/surat-dinas/${id}`
      default:
        return null
    }
  }

  let showDeleteModal = false
  let deleteTarget = null

  function confirmDelete(type, id, label) {
    deleteTarget = { type, id, label }
    showDeleteModal = true
  }

  function executeDelete() {
    if (!deleteTarget) return
    const url = getDeleteUrl(deleteTarget.type, deleteTarget.id)
    if (url) {
      const deletedId = deleteTarget.id
      router.delete(url, {
        preserveScroll: true,
        onSuccess: () => {
          surat = surat.filter((s) => s.id !== deletedId)
          filteredSurat = filteredSurat.filter((s) => s.id !== deletedId)
          updatePagination()
          triggerToast('Surat berhasil dihapus.')
        },
      })
    }
    showDeleteModal = false
    deleteTarget = null
  }

  function cancelDelete() {
    showDeleteModal = false
    deleteTarget = null
  }
</script>

<h1 class="mb-4 text-3xl font-bold">Riwayat Surat yang Diajukan</h1>

{#if showBanner}
  <div class="flex items-center justify-between px-5 py-3 mb-4 text-white bg-green-500 rounded-lg shadow">
    <div class="flex items-center gap-3">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>
      <span class="text-sm font-medium">{bannerMessage}</span>
    </div>
    <button class="text-white/70 hover:text-white" on:click={() => (showBanner = false)}>✕</button>
  </div>
{/if}
<div class="flex gap-2 mb-4">
  <button class={`rounded px-4 py-2 focus:outline-none ${activeType === 1 ? 'bg-[#5661B3] font-bold text-white hover:bg-[#2F365F]' : 'bg-white text-[#374151] hover:bg-gray-100'}`} on:click={() => filterByType(1)}> Surat Tugas </button>
  <button class={`rounded px-4 py-2 focus:outline-none ${activeType === 2 ? 'bg-[#5661B3] font-bold text-white hover:bg-[#2F365F]' : 'bg-white text-[#374151] hover:bg-gray-100'}`} on:click={() => filterByType(2)}> Surat Undangan </button>
  <button class={`rounded px-4 py-2 focus:outline-none ${activeType === 3 ? 'bg-[#5661B3] font-bold text-white hover:bg-[#2F365F]' : 'bg-white text-[#374151] hover:bg-gray-100'}`} on:click={() => filterByType(3)}> Surat Dinas </button>
</div>
<div class="overflow-x-auto bg-white rounded-md shadow">
  <table class="w-full whitespace-nowrap">
    <thead>
      <tr class="font-bold text-left">
        <th class="px-6 pt-6 pb-4">Tanggal</th>
        <th class="px-6 pt-6 pb-4">Kode Arsip</th>
        {#if activeType === 1}
          <th class="px-6 pt-6 pb-4">Kegiatan</th>
          <th class="px-6 pt-6 pb-4">Kepada</th>
          <th class="px-6 pt-6 pb-4">Nomor Surat</th>
        {:else if activeType === 2}
          <th class="px-6 pt-6 pb-4">Perihal</th>
          <th class="px-6 pt-6 pb-4">Tujuan</th>
          <th class="px-6 pt-6 pb-4">Nomor Surat</th>
          <th class="px-6 pt-6 pb-4">Keperluan Konsumsi</th>
          <th class="px-6 pt-6 pb-4">Pengelolaan Konsumsi</th>
          <th class="px-6 pt-6 pb-4">Penggunaan Aula</th>
        {:else if activeType === 3}
          <th class="px-6 pt-6 pb-4">Perihal</th>
          <th class="px-6 pt-6 pb-4">Tujuan</th>
          <th class="px-6 pt-6 pb-4">Nomor Surat</th>
        {/if}
        <th class="px-6 pt-6 pb-4">Draft Surat</th>
        {#if auth?.user}
          <th class="px-6 pt-6 pb-4">Aksi</th>
        {/if}
      </tr>
    </thead>
    <tbody>
      {#each paginatedSurat as surat (surat.id)}
        {#if auth?.user}
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
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.nomor || ''}</p>
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
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.nomor || ''}</p>
              </td>
            {/if}
            <td class="px-6 border-t">
              {#if surat.filepath}
                <button
                  class="rounded bg-[#5661B3] px-4 py-2 text-white hover:bg-[#2F365F]"
                  on:click={(e) => {
                    e.stopPropagation()
                    downloadFile(surat.filepath)
                  }}>
                  Download
                </button>
              {:else}
                <p class="text-gray-500">Belum Ada Draft Surat</p>
              {/if}
            </td>
            <td class="px-6 text-center border-t">
              <div class="flex items-center justify-center gap-2">
                <button class="inline-flex items-center p-2 text-white bg-green-500 rounded hover:bg-green-600 focus:outline-none" on:click={() => (window.location.href = getEditUrl(surat.type, surat.id))} aria-label="Edit">
                  <Pencil class="w-4 h-4 text-white" />
                </button>
                <button class="inline-flex items-center p-2 text-white bg-red-500 rounded hover:bg-red-600 focus:outline-none" on:click={() => confirmDelete(surat.type, surat.id, surat.perihal || surat.nomor || `ID ${surat.id}`)} aria-label="Hapus">
                  <Trash2 class="w-4 h-4 text-white" />
                </button>
              </div>
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
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.nomor || ''}</p>
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
              <td class="border-t">
                <p class="flex items-center px-6 py-4" tabindex="-1">{surat.nomor || ''}</p>
              </td>
            {/if}
            <td class="px-6 border-t">
              <div class="flex items-center justify-center">
                {#if surat.filepath}
                  <button
                    class="rounded bg-[#5661B3] px-4 py-2 text-white hover:bg-[#2F365F]"
                    on:click={(e) => {
                      e.stopPropagation()
                      downloadFile(surat.filepath)
                    }}>
                    Download
                  </button>
                {:else}
                  <p class="text-gray-500">Belum Ada Draft Surat</p>
                {/if}
              </div>
            </td>
          </tr>
        {/if}
      {/each}
      {#if paginatedSurat.length === 0}
        <tr>
          <td class="px-6 py-4 border-t" colspan="10">Belum Ada Surat yang Diajukan.</td>
        </tr>
      {/if}
    </tbody>
  </table>
</div>
<Pagination {totalPages} {currentPage} onChange={changePage} />

{#if showDeleteModal}
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-xl">
      <h2 class="mb-2 text-lg font-bold text-gray-800">Konfirmasi Hapus</h2>
      <p class="mb-1 text-gray-600">Apakah Anda yakin ingin menghapus surat ini?</p>
      <p class="mb-6 font-semibold text-gray-800 truncate">"{deleteTarget?.label}"</p>
      <div class="flex justify-end gap-3">
        <button class="px-4 py-2 text-gray-600 rounded hover:bg-gray-100 focus:outline-none" on:click={cancelDelete}> Batal </button>
        <button class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-600 focus:outline-none" on:click={executeDelete}> Hapus </button>
      </div>
    </div>
  </div>
{/if}

{#if showToast}
  <div class="fixed z-50 flex items-center gap-3 px-5 py-3 text-white transition-all bg-green-500 rounded-lg shadow-xl bottom-6 right-6">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
    </svg>
    <span class="text-sm font-medium">{toastMessage}</span>
    <button class="ml-2 text-white/70 hover:text-white" on:click={() => (showToast = false)}>✕</button>
  </div>
{/if}
