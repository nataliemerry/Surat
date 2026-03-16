<script context="module">
  import LayoutAlatTulis from '@/Shared/LayoutAlatTulis.svelte'
  export const layout = LayoutAlatTulis
</script>

<script>
  import { onMount } from 'svelte'
  import { router, useForm } from '@inertiajs/svelte'
  import { title as pageTitle } from '@/Shared/LayoutAlatTulis.svelte'
  import { Pencil, Trash2, Plus, X, AlertTriangle } from 'lucide-svelte'

  onMount(() => pageTitle.set('Kelola Barang'))

  export let categories = []
  export let items = []

  let activeTab = 'kategori'

  let searchQuery = ''
  let activeCategory = null

  $: filteredItems = items.filter((item) => {
    const matchSearch = !searchQuery.trim() || item.name.toLowerCase().includes(searchQuery.trim().toLowerCase())
    const matchCat = activeCategory === null || item.category_id === activeCategory
    return matchSearch && matchCat
  })

  let catModal = null
  let editCat = null

  const catForm = useForm({ name: '' })

  function openAddCat() {
    $catForm.name = ''
    editCat = null
    catModal = 'form'
  }

  function openEditCat(cat) {
    editCat = cat
    $catForm.name = cat.name
    catModal = 'form'
  }

  function openDeleteCat(cat) {
    editCat = cat
    catModal = 'delete'
  }

  function closeCatModal() {
    catModal = null
    editCat = null
  }

  function submitCat() {
    if (editCat) {
      $catForm.put(`/atk/barang/kategori/${editCat.id}`, { onSuccess: closeCatModal })
    } else {
      $catForm.post('/atk/barang/kategori', { onSuccess: closeCatModal })
    }
  }

  function confirmDeleteCat() {
    router.delete(`/atk/barang/kategori/${editCat.id}`, { onSuccess: closeCatModal })
  }

  let itemModal = null
  let editItem = null

  const itemForm = useForm({ category_id: '', name: '', satuan: '' })

  function openAddItem() {
    $itemForm.category_id = ''
    $itemForm.name = ''
    $itemForm.satuan = ''
    editItem = null
    itemModal = 'form'
  }

  function openEditItem(item) {
    editItem = item
    $itemForm.category_id = item.category_id
    $itemForm.name = item.name
    $itemForm.satuan = item.satuan
    itemModal = 'form'
  }

  function openDeleteItem(item) {
    editItem = item
    itemModal = 'delete'
  }

  function closeItemModal() {
    itemModal = null
    editItem = null
  }

  function submitItem() {
    if (editItem) {
      $itemForm.put(`/atk/barang/item/${editItem.id}`, { onSuccess: closeItemModal })
    } else {
      $itemForm.post('/atk/barang/item', { onSuccess: closeItemModal })
    }
  }

  function confirmDeleteItem() {
    router.delete(`/atk/barang/item/${editItem.id}`, { onSuccess: closeItemModal })
  }
</script>

<div class="w-full">
  <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h1 class="text-2xl font-bold text-gray-800 sm:text-3xl">Kelola Barang</h1>
      <p class="mt-1 text-gray-500">Manajemen kategori dan daftar barang alat tulis kantor.</p>
    </div>
    {#if activeTab === 'kategori'}
      <button on:click={openAddCat} class="inline-flex w-fit items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
        <Plus class="h-4 w-4" />
        Tambah Kategori
      </button>
    {:else}
      <button on:click={openAddItem} class="inline-flex w-fit items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
        <Plus class="h-4 w-4" />
        Tambah Barang
      </button>
    {/if}
  </div>

  <div class="mb-4 flex gap-1 border-b border-gray-200">
    <button class="px-5 py-2.5 text-sm font-medium transition-colors {activeTab === 'kategori' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700'}" on:click={() => (activeTab = 'kategori')}> Kategori </button>
    <button class="px-5 py-2.5 text-sm font-medium transition-colors {activeTab === 'barang' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700'}" on:click={() => (activeTab = 'barang')}> Barang </button>
  </div>

  {#if activeTab === 'kategori'}
    {#if categories.length === 0}
      <div class="rounded-xl border border-dashed border-gray-300 py-14 text-center text-gray-400">
        <p class="text-sm">Belum ada kategori. Tambahkan kategori baru.</p>
      </div>
    {:else}
      <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
            <tr>
              <th class="px-4 py-3 text-left">Nama Kategori</th>
              <th class="px-4 py-3 text-center">Jumlah Barang</th>
              <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            {#each categories as cat (cat.id)}
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-800">{cat.name}</td>
                <td class="px-4 py-3 text-center">
                  <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
                    {cat.items_count} barang
                  </span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center justify-center gap-2">
                    <button on:click={() => openEditCat(cat)} class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                      <Pencil class="h-3.5 w-3.5" />
                      Edit
                    </button>
                    <button on:click={() => openDeleteCat(cat)} class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-sm font-medium text-red-600 shadow-sm hover:bg-red-50">
                      <Trash2 class="h-3.5 w-3.5" />
                      Hapus
                    </button>
                  </div>
                </td>
              </tr>
            {/each}
          </tbody>
        </table>
      </div>
    {/if}
  {:else}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
      <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-4 py-3 sm:px-5">
        <div class="relative w-full max-w-sm">
          <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8" /><path stroke-linecap="round" d="m21 21-4.35-4.35" />
          </svg>
          <input type="search" bind:value={searchQuery} placeholder="Cari nama barang…" class="w-full rounded-lg border border-gray-300 py-2 pl-9 pr-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
        </div>
        {#if searchQuery || activeCategory !== null}
          <button
            on:click={() => {
              searchQuery = ''
              activeCategory = null
            }}
            class="shrink-0 text-xs text-indigo-600 hover:underline">
            Reset filter
          </button>
        {/if}
      </div>

      <div class="flex flex-col md:flex-row" style="min-height: 420px;">
        <!-- Sidebar Kategori (Desktop) -->
        <nav class="hidden w-44 shrink-0 overflow-y-auto border-r border-gray-100 bg-gray-50 md:block">
          <button type="button" on:click={() => (activeCategory = null)} class="flex w-full items-center px-4 py-2.5 text-left text-sm transition {activeCategory === null ? 'bg-indigo-50 font-semibold text-indigo-700' : 'text-gray-600 hover:bg-gray-100'}"> Semua Kategori </button>
          {#each categories as cat (cat.id)}
            <button type="button" on:click={() => (activeCategory = cat.id)} class="flex w-full items-center justify-between px-4 py-2.5 text-left text-sm transition {activeCategory === cat.id ? 'bg-indigo-50 font-semibold text-indigo-700' : 'text-gray-600 hover:bg-gray-100'}">
              <span class="leading-tight">{cat.name}</span>
              <span class="ml-1 shrink-0 rounded-full bg-gray-200 px-1.5 py-0.5 text-[10px] font-semibold text-gray-600">{cat.items_count}</span>
            </button>
          {/each}
        </nav>

        <!-- Kategori (Mobile Horizontal Scroll) -->
        <div class="hide-scrollbar flex w-full gap-2 overflow-x-auto border-b border-gray-100 bg-gray-50 px-4 py-2.5 md:hidden">
          <button type="button" on:click={() => (activeCategory = null)} class="shrink-0 rounded-full border px-3 py-1 text-xs font-medium transition {activeCategory === null ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-gray-200 bg-white text-gray-600 hover:border-indigo-300'}"> Semua </button>
          {#each categories as cat (cat.id)}
            <button type="button" on:click={() => (activeCategory = cat.id)} class="shrink-0 rounded-full border px-3 py-1 text-xs font-medium transition {activeCategory === cat.id ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-gray-200 bg-white text-gray-600 hover:border-indigo-300'}">
              {cat.name}
            </button>
          {/each}
        </div>

        <div class="flex-1 overflow-y-auto">
          {#if filteredItems.length === 0}
            <div class="flex h-full items-center justify-center py-16 text-center text-gray-400">
              <p class="px-4 text-sm">
                {searchQuery ? `Tidak ada barang yang cocok dengan "${searchQuery}".` : 'Belum ada barang di kategori ini.'}
              </p>
            </div>
          {:else}
            <!-- Mobile View: Cards -->
            <div class="grid grid-cols-1 divide-y divide-gray-100 md:hidden">
              {#each filteredItems as item (item.id)}
                <div class="space-y-3 bg-white p-4">
                  <div class="flex items-start justify-between">
                    <div class="min-w-0 flex-1">
                      <h3 class="truncate font-semibold text-gray-900">{item.name}</h3>
                      <div class="mt-1 flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-full border border-indigo-100 bg-indigo-50 px-2 py-0.5 text-[10px] font-medium text-indigo-700">
                          {item.category ? item.category.name : '-'}
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-medium text-gray-500">
                          Unit: {item.satuan}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="flex gap-2 pt-1">
                    <button on:click={() => openEditItem(item)} class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 active:bg-gray-100">
                      <Pencil class="h-3.5 w-3.5" />
                      Edit
                    </button>
                    <button on:click={() => openDeleteItem(item)} class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-2 text-sm font-medium text-red-600 shadow-sm hover:bg-red-50 active:bg-red-100">
                      <Trash2 class="h-3.5 w-3.5" />
                      Hapus
                    </button>
                  </div>
                </div>
              {/each}
            </div>

            <!-- Desktop View: Table -->
            <table class="hidden min-w-full text-sm md:table">
              <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                <tr>
                  <th class="px-4 py-3 text-left">Nama Barang</th>
                  <th class="px-4 py-3 text-left">Kategori</th>
                  <th class="px-4 py-3 text-left">Satuan</th>
                  <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                {#each filteredItems as item (item.id)}
                  <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">{item.name}</td>
                    <td class="px-4 py-3">
                      <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700">
                        {item.category ? item.category.name : '-'}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{item.satuan}</td>
                    <td class="px-4 py-3">
                      <div class="flex items-center justify-center gap-2">
                        <button on:click={() => openEditItem(item)} class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                          <Pencil class="h-3.5 w-3.5" />
                          Edit
                        </button>
                        <button on:click={() => openDeleteItem(item)} class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-sm font-medium text-red-600 shadow-sm hover:bg-red-50">
                          <Trash2 class="h-3.5 w-3.5" />
                          Hapus
                        </button>
                      </div>
                    </td>
                  </tr>
                {/each}
              </tbody>
            </table>
          {/if}
        </div>
      </div>
    </div>
  {/if}
</div>

{#if catModal === 'form'}
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl">
      <div class="flex items-center justify-between border-b px-6 py-4">
        <h2 class="text-lg font-bold text-gray-800">{editCat ? 'Edit Kategori' : 'Tambah Kategori'}</h2>
        <button on:click={closeCatModal} class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
          <X class="h-5 w-5" />
        </button>
      </div>
      <div class="px-6 py-5">
        <label class="block text-sm font-medium text-gray-700" for="cat-name">Nama Kategori</label>
        <input id="cat-name" type="text" bind:value={$catForm.name} placeholder="Contoh: Alat Tulis" class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 {$catForm.errors.name ? 'border-red-400' : ''}" />
        {#if $catForm.errors.name}
          <p class="mt-1 text-xs text-red-600">{$catForm.errors.name}</p>
        {/if}
      </div>
      <div class="flex items-center justify-end gap-3 border-t px-6 py-4">
        <button on:click={closeCatModal} class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200" disabled={$catForm.processing}> Batal </button>
        <button on:click={submitCat} disabled={$catForm.processing} class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 disabled:opacity-60">
          {$catForm.processing ? 'Menyimpan…' : editCat ? 'Simpan Perubahan' : 'Tambahkan'}
        </button>
      </div>
    </div>
  </div>
{/if}

{#if catModal === 'delete'}
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl">
      <div class="flex items-center justify-between border-b px-6 py-4">
        <h2 class="text-lg font-bold text-gray-800">Hapus Kategori</h2>
        <button on:click={closeCatModal} class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
          <X class="h-5 w-5" />
        </button>
      </div>
      <div class="px-6 py-5">
        <p class="text-sm text-gray-600">
          Anda akan menghapus kategori <span class="font-semibold text-gray-800">"{editCat?.name}"</span>.
        </p>
        {#if editCat?.items_count > 0}
          <div class="mt-3 flex items-start gap-2 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <AlertTriangle class="mt-0.5 h-4 w-4 shrink-0 text-amber-500" />
            <span>Kategori ini memiliki <strong>{editCat.items_count} barang</strong> yang juga akan ikut terhapus.</span>
          </div>
        {/if}
        <p class="mt-3 text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
      </div>
      <div class="flex items-center justify-end gap-3 border-t px-6 py-4">
        <button on:click={closeCatModal} class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200"> Batal </button>
        <button on:click={confirmDeleteCat} class="rounded-lg bg-red-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-red-700"> Hapus </button>
      </div>
    </div>
  </div>
{/if}

{#if itemModal === 'form'}
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl">
      <div class="flex items-center justify-between border-b px-6 py-4">
        <h2 class="text-lg font-bold text-gray-800">{editItem ? 'Edit Barang' : 'Tambah Barang'}</h2>
        <button on:click={closeItemModal} class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
          <X class="h-5 w-5" />
        </button>
      </div>
      <div class="space-y-4 px-6 py-5">
        <div>
          <label class="block text-sm font-medium text-gray-700" for="item-category">Kategori</label>
          <select id="item-category" bind:value={$itemForm.category_id} class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 {$itemForm.errors.category_id ? 'border-red-400' : ''}">
            <option value="">Pilih kategori…</option>
            {#each categories as cat (cat.id)}
              <option value={cat.id}>{cat.name}</option>
            {/each}
          </select>
          {#if $itemForm.errors.category_id}
            <p class="mt-1 text-xs text-red-600">{$itemForm.errors.category_id}</p>
          {/if}
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700" for="item-name">Nama Barang</label>
          <input id="item-name" type="text" bind:value={$itemForm.name} placeholder="Contoh: Ballpoint Hitam" class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 {$itemForm.errors.name ? 'border-red-400' : ''}" />
          {#if $itemForm.errors.name}
            <p class="mt-1 text-xs text-red-600">{$itemForm.errors.name}</p>
          {/if}
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700" for="item-satuan">Satuan</label>
          <input id="item-satuan" type="text" bind:value={$itemForm.satuan} placeholder="Contoh: buah, pak, rim" class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 {$itemForm.errors.satuan ? 'border-red-400' : ''}" />
          {#if $itemForm.errors.satuan}
            <p class="mt-1 text-xs text-red-600">{$itemForm.errors.satuan}</p>
          {/if}
        </div>
      </div>
      <div class="flex items-center justify-end gap-3 border-t px-6 py-4">
        <button on:click={closeItemModal} class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200" disabled={$itemForm.processing}> Batal </button>
        <button on:click={submitItem} disabled={$itemForm.processing} class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 disabled:opacity-60">
          {$itemForm.processing ? 'Menyimpan…' : editItem ? 'Simpan Perubahan' : 'Tambahkan'}
        </button>
      </div>
    </div>
  </div>
{/if}

{#if itemModal === 'delete'}
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl">
      <div class="flex items-center justify-between border-b px-6 py-4">
        <h2 class="text-lg font-bold text-gray-800">Hapus Barang</h2>
        <button on:click={closeItemModal} class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
          <X class="h-5 w-5" />
        </button>
      </div>
      <div class="px-6 py-5">
        <p class="text-sm text-gray-600">
          Anda akan menghapus barang <span class="font-semibold text-gray-800">"{editItem?.name}"</span>.
        </p>
        <p class="mt-3 text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
      </div>
      <div class="flex items-center justify-end gap-3 border-t px-6 py-4">
        <button on:click={closeItemModal} class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200"> Batal </button>
        <button on:click={confirmDeleteItem} class="rounded-lg bg-red-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-red-700"> Hapus </button>
      </div>
    </div>
  </div>
{/if}
