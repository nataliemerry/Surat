<script context="module">
  import Layout, { title } from '@/Shared/Layout.svelte'
  export const layout = Layout
</script>

<script>
  import { onMount } from 'svelte'
  import Pagination from '@/Shared/Pagination.svelte'
  import { router, page } from '@inertiajs/svelte'
  import { Pencil, Trash2, Search } from 'lucide-svelte'

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
  let searchQuery = ''

  $title = 'Dashboard'

  function applyFilters() {
    filteredSurat = surat.filter((item) => {
      const matchType = item.type === activeType
      const safePerihal = item.perihal || ''
      const matchSearch = !searchQuery || safePerihal.toLowerCase().includes(searchQuery.toLowerCase())
      return matchType && matchSearch
    })
    currentPage = 1
    updatePagination()
  }

  function filterByType(type) {
    activeType = type
    applyFilters()
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
        return `/surat/tugas/${id}/edit`
      case 2:
        return `/surat/undangan/${id}/edit`
      case 3:
        return `/surat/dinas/${id}/edit`
      default:
        return '#'
    }
  }

  function getDeleteUrl(type, id) {
    switch (type) {
      case 1:
        return `/surat/tugas/${id}`
      case 2:
        return `/surat/undangan/${id}`
      case 3:
        return `/surat/dinas/${id}`
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

  function formatTanggal(dateStr) {
    if (!dateStr) return '-'
    const d = new Date(dateStr)
    if (isNaN(d)) return dateStr
    const dd = String(d.getDate()).padStart(2, '0')
    const mm = String(d.getMonth() + 1).padStart(2, '0')
    const yyyy = d.getFullYear()
    return `${dd}/${mm}/${yyyy}`
  }
</script>

<h1 class="mb-4 text-2xl font-bold text-gray-800 sm:text-3xl">Riwayat Surat yang Diajukan</h1>

{#if showBanner}
  <div class="mb-4 flex items-center justify-between rounded-lg bg-green-500 px-5 py-3 text-white shadow">
    <div class="flex items-center gap-3">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>
      <span class="text-sm font-medium">{bannerMessage}</span>
    </div>
    <button class="text-white/70 hover:text-white" on:click={() => (showBanner = false)}>✕</button>
  </div>
{/if}
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
  <!-- Desktop Tabs -->
  <div class="hidden gap-1 border-b border-gray-200 sm:flex">
    <button class="px-5 py-2.5 text-sm font-medium transition-colors {activeType === 1 ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700'}" on:click={() => filterByType(1)}> Surat Tugas </button>
    <button class="px-5 py-2.5 text-sm font-medium transition-colors {activeType === 2 ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700'}" on:click={() => filterByType(2)}> Surat Undangan </button>
    <button class="px-5 py-2.5 text-sm font-medium transition-colors {activeType === 3 ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700'}" on:click={() => filterByType(3)}> Surat Dinas </button>
  </div>

  <!-- Mobile Dropdown -->
  <div class="relative block sm:hidden">
    <select class="block w-full appearance-none rounded-lg border-gray-300 bg-white py-2 pl-3 pr-10 text-sm font-medium text-gray-700 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500" value={activeType} on:change={(e) => filterByType(parseInt(e.target.value))}>
      <option value={1}>Surat Tugas</option>
      <option value={2}>Surat Undangan</option>
      <option value={3}>Surat Dinas</option>
    </select>
    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
      <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </div>
  </div>

  <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
    <!-- Search Bar -->
    <div class="relative w-full sm:w-64">
      <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
        <Search class="h-4 w-4 text-gray-400" />
      </div>
      <input type="text" bind:value={searchQuery} on:input={applyFilters} placeholder="Cari berdasarkan perihal..." class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-10 pr-3 text-sm placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
    </div>

    <div class="flex items-center">
      {#if activeType === 1}
        <a href="/surat/tugas/form" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
          </svg>
          Buat Surat Tugas
        </a>
      {:else if activeType === 2}
        <a href="/surat/undangan/form" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
          </svg>
          Buat Surat Undangan
        </a>
      {:else if activeType === 3}
        <a href="/surat/dinas/form" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
          </svg>
          Buat Surat Dinas
        </a>
      {/if}
    </div>
  </div>
</div>
<div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
  <table class="min-w-full whitespace-nowrap text-sm">
    <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
      <tr>
        <th class="px-4 py-3 text-left">Tanggal</th>
        <th class="px-4 py-3 text-left">Kode Arsip</th>
        {#if activeType === 1}
          <th class="px-4 py-3 text-left">Kegiatan</th>
          <th class="px-4 py-3 text-left">Kepada</th>
          <th class="px-4 py-3 text-left">Nomor Surat</th>
        {:else if activeType === 2}
          <th class="px-4 py-3 text-left">Perihal</th>
          <th class="px-4 py-3 text-left">Tujuan</th>
          <th class="px-4 py-3 text-left">Nomor Surat</th>
          <th class="px-4 py-3 text-left">Tanggal Pelaksanaan</th>
          <th class="px-4 py-3 text-left">Keperluan Konsumsi</th>
          <th class="px-4 py-3 text-left">Pengelolaan Konsumsi</th>
          <th class="px-4 py-3 text-left">Penggunaan Aula</th>
        {:else if activeType === 3}
          <th class="px-4 py-3 text-left">Perihal</th>
          <th class="px-4 py-3 text-left">Tujuan</th>
          <th class="px-4 py-3 text-left">Nomor Surat</th>
        {/if}
        <th class="px-4 py-3 text-left">Dokumen Surat</th>
        {#if auth?.user}
          <th class="px-4 py-3 text-center">Aksi</th>
        {/if}
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
      {#each paginatedSurat as surat (surat.id)}
        <tr class="hover:bg-gray-50">
          <td class="whitespace-nowrap px-4 py-3 text-gray-600">{surat.created_at}</td>
          <td class="px-4 py-3 text-gray-600">{surat.kode || ''}</td>
          {#if activeType === 1}
            <td class="px-4 py-3 text-gray-600">{surat.perihal || ''}</td>
            <td class="px-4 py-3 text-gray-600">{surat.tujuan || ''}</td>
            <td class="px-4 py-3 font-medium text-gray-800">{surat.nomor || ''}</td>
          {:else if activeType === 2}
            <td class="px-4 py-3 text-gray-600">{surat.perihal || ''}</td>
            <td class="px-4 py-3 text-gray-600">{surat.tujuan || ''}</td>
            <td class="px-4 py-3 font-medium text-gray-800">{surat.nomor || ''}</td>
            <td class="px-4 py-3 text-gray-600">{formatTanggal(surat.tanggal_pelaksanaan)}</td>
            <td class="px-4 py-3 text-gray-600">{surat.isKonsumsi ? 'Iya' : 'Tidak'}</td>
            <td class="px-4 py-3 text-gray-600">{surat.isPengelolaan ? 'TU/Umum' : 'Dikelola Sendiri'}</td>
            <td class="px-4 py-3 text-gray-600">{surat.isRuangan ? 'Iya' : 'Tidak'}</td>
          {:else if activeType === 3}
            <td class="px-4 py-3 text-gray-600">{surat.perihal || ''}</td>
            <td class="px-4 py-3 text-gray-600">{surat.tujuan || ''}</td>
            <td class="px-4 py-3 font-medium text-gray-800">{surat.nomor || ''}</td>
          {/if}
          <td class="px-4 py-3">
            {#if surat.link}
              <button
                class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-indigo-700"
                on:click={(e) => {
                  e.stopPropagation()
                  window.open(surat.link, '_blank')
                }}>
                Download
              </button>
            {:else}
              <span class="text-xs text-gray-400">Belum Ada Dokumen</span>
            {/if}
          </td>
          {#if auth?.user}
            <td class="px-4 py-3 text-center">
              <button class="inline-flex items-center rounded-lg bg-green-500 p-2 text-white hover:bg-green-600 focus:outline-none" on:click={() => (window.location.href = getEditUrl(surat.type, surat.id))} aria-label="Edit">
                <Pencil class="h-4 w-4" />
              </button>
            </td>
          {/if}
        </tr>
      {/each}
      {#if paginatedSurat.length === 0}
        <tr>
          <td class="px-4 py-4 text-sm text-gray-400" colspan="10">Belum Ada Surat yang Diajukan.</td>
        </tr>
      {/if}
    </tbody>
  </table>
</div>
<Pagination {totalPages} {currentPage} onChange={changePage} />

{#if showDeleteModal}
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
      <h2 class="mb-2 text-lg font-bold text-gray-800">Konfirmasi Hapus</h2>
      <p class="mb-1 text-gray-600">Apakah Anda yakin ingin menghapus surat ini?</p>
      <p class="mb-6 truncate font-semibold text-gray-800">"{deleteTarget?.label}"</p>
      <div class="flex justify-end gap-3">
        <button class="rounded px-4 py-2 text-gray-600 hover:bg-gray-100 focus:outline-none" on:click={cancelDelete}> Batal </button>
        <button class="rounded bg-red-500 px-4 py-2 text-white hover:bg-red-600 focus:outline-none" on:click={executeDelete}> Hapus </button>
      </div>
    </div>
  </div>
{/if}

{#if showToast}
  <div class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-lg bg-green-500 px-5 py-3 text-white shadow-xl transition-all">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
    </svg>
    <span class="text-sm font-medium">{toastMessage}</span>
    <button class="ml-2 text-white/70 hover:text-white" on:click={() => (showToast = false)}>✕</button>
  </div>
{/if}
