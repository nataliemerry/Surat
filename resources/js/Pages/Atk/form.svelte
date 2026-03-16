<script context="module">
  import LayoutAlatTulis from '@/Shared/LayoutAlatTulis.svelte'
  export const layout = LayoutAlatTulis
</script>

<script>
  import { inertia } from '@inertiajs/svelte'
  import { useForm } from '@inertiajs/svelte'
  import LoadingButton from '@/Shared/LoadingButton.svelte'
  import { onMount } from 'svelte'
  import { title as pageTitle } from '@/Shared/LayoutAlatTulis.svelte'

  onMount(() => {
    pageTitle.set('Form Permintaan ATK')
  })

  export let teams = []
  export let categories = []
  export let items = []
  export let pegawais = []

  let step = 1
  let searchQuery = ''
  let activeCategory = null
  let showErrors = false

  let nameQuery = ''
  let nameDropdownOpen = false
  let nameHighlightIdx = -1

  $: nameFilteredList = nameQuery.trim() ? pegawais.filter((p) => p.nama.toLowerCase().includes(nameQuery.trim().toLowerCase())) : pegawais

  function selectStaffName(pegawai) {
    $form.pegawai_id = pegawai.id
    nameQuery = pegawai.nama
    nameDropdownOpen = false
    nameHighlightIdx = -1
  }

  function onNameInput(e) {
    nameQuery = e.target.value
    $form.pegawai_id = ''
    nameDropdownOpen = true
    nameHighlightIdx = -1
  }

  function onNameKeydown(e) {
    if (!nameDropdownOpen || nameFilteredList.length === 0) return
    if (e.key === 'ArrowDown') {
      e.preventDefault()
      nameHighlightIdx = Math.min(nameHighlightIdx + 1, nameFilteredList.length - 1)
    } else if (e.key === 'ArrowUp') {
      e.preventDefault()
      nameHighlightIdx = Math.max(nameHighlightIdx - 1, 0)
    } else if (e.key === 'Enter' && nameHighlightIdx >= 0) {
      e.preventDefault()
      selectStaffName(nameFilteredList[nameHighlightIdx])
    } else if (e.key === 'Escape') {
      nameDropdownOpen = false
    }
  }

  let cart = {}

  const form = useForm({
    pegawai_id: '',
    team_id: '',
    activity: '',
    items: [],
  })

  $: step1Valid = $form.pegawai_id !== '' && $form.team_id !== '' && $form.activity.trim() !== ''

  $: filteredItems = items.filter((item) => {
    const matchSearch = !searchQuery.trim() || item.name.toLowerCase().includes(searchQuery.trim().toLowerCase())
    const matchCat = activeCategory === null || item.category_id === activeCategory
    return matchSearch && matchCat
  })

  $: selectedItems = Object.entries(cart)
    .filter(([, qty]) => qty > 0)
    .map(([id, qty]) => {
      const found = items.find((i) => i.id === parseInt(id))
      return found ? { ...found, qty_requested: qty } : null
    })
    .filter(Boolean)

  $: cartedByCategory = categories.reduce((acc, cat) => {
    acc[cat.id] = items.filter((i) => i.category_id === cat.id && (cart[i.id] || 0) > 0).length
    return acc
  }, {})

  $: selectedTeam = teams.find((t) => t.id == $form.team_id)

  function getQty(item_id) {
    return cart[item_id] || 0
  }

  function toggleItem(item_id) {
    if (cart[item_id]) {
      const { [item_id]: _, ...rest } = cart
      cart = rest
    } else {
      cart = { ...cart, [item_id]: 1 }
    }
  }

  function setQty(item_id, value) {
    const num = parseInt(value)
    if (isNaN(num) || num < 1) {
      cart = { ...cart, [item_id]: 1 }
    } else {
      cart = { ...cart, [item_id]: num }
    }
  }

  function increment(item_id) {
    cart = { ...cart, [item_id]: (cart[item_id] || 1) + 1 }
  }

  function decrement(item_id) {
    const cur = cart[item_id] || 1
    cart = { ...cart, [item_id]: Math.max(1, cur - 1) }
  }

  function removeFromCart(item_id) {
    const { [item_id]: _, ...rest } = cart
    cart = rest
  }

  function goStep2() {
    showErrors = true
    if (!step1Valid) return
    showErrors = false
    step = 2
  }

  function goStep3() {
    if (selectedItems.length === 0) return
    step = 3
  }

  function submit() {
    $form.items = selectedItems.map((i) => ({
      item_id: i.id,
      qty_requested: i.qty_requested,
    }))
    $form.post('/atk/store')
  }
</script>

<div class="max-w-5xl">
  <div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
      <a use:inertia href="/atk" class="text-indigo-500 hover:text-indigo-700">ATK</a>
      <span class="mx-1 font-medium text-indigo-400">/</span>
      Form Permintaan
    </h1>
  </div>

  <div class="mb-6 flex items-center gap-1">
    {#each [{ n: 1, label: 'Identitas' }, { n: 2, label: 'Pilih Barang' }, { n: 3, label: 'Review' }] as s}
      <div class="flex items-center gap-1.5 sm:gap-2">
        <div
          class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold transition-colors sm:h-8 sm:w-8 sm:text-sm
            {step > s.n ? 'bg-indigo-600 text-white' : step === s.n ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400'}">
          {#if step > s.n}
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
          {:else}
            {s.n}
          {/if}
        </div>
        <span class="text-xs font-medium sm:text-sm {step === s.n ? 'text-indigo-700' : step > s.n ? 'text-gray-500' : 'text-gray-400'}">{s.label}</span>
      </div>
      {#if s.n < 3}
        <div class="mx-1 h-px w-5 flex-shrink-0 transition-colors sm:mx-2 sm:w-8 {step > s.n ? 'bg-indigo-400' : 'bg-gray-300'}"></div>
      {/if}
    {/each}
  </div>

  {#if step === 1}
    <div class="max-w-2xl overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
      <div class="border-b border-gray-100 px-5 py-4 sm:px-8 sm:py-5">
        <h2 class="text-base font-semibold text-gray-800">Data Pemohon</h2>
        <p class="text-sm text-gray-500">Lengkapi identitas dan tujuan permintaan.</p>
      </div>

      <div class="space-y-5 px-5 py-5 sm:px-8 sm:py-6">
        <div>
          <label for="requester_name" class="mb-1 block text-sm font-medium text-gray-700">
            Nama Pemohon <span class="text-red-500">*</span>
          </label>
          <div class="relative">
            <input
              id="pegawai_nama"
              type="text"
              value={nameQuery}
              on:input={onNameInput}
              on:focus={() => {
                nameDropdownOpen = true
              }}
              on:keydown={onNameKeydown}
              on:blur={() =>
                setTimeout(() => {
                  nameDropdownOpen = false
                }, 150)}
              placeholder="Cari atau ketik nama pemohon..."
              autocomplete="off"
              class="form-input {showErrors && !$form.pegawai_id ? 'error' : ''}" />
            {#if nameDropdownOpen && nameFilteredList.length > 0}
              <ul class="absolute z-20 mt-1 max-h-56 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white text-sm shadow-lg">
                {#each nameFilteredList as pegawai, i}
                  <li>
                    <button type="button" on:mousedown|preventDefault={() => selectStaffName(pegawai)} class="w-full px-4 py-2 text-left transition-colors {i === nameHighlightIdx ? 'bg-indigo-50 font-medium text-indigo-700' : 'text-gray-700 hover:bg-gray-50'}">
                      {pegawai.nama}
                    </button>
                  </li>
                {/each}
              </ul>
            {/if}
          </div>
          {#if showErrors && !$form.pegawai_id}
            <p class="form-error">Nama tidak boleh kosong.</p>
          {:else if $form.errors.pegawai_id}
            <p class="form-error">{$form.errors.pegawai_id}</p>
          {/if}
        </div>

        <div>
          <label for="team_id" class="mb-1 block text-sm font-medium text-gray-700">
            Tim <span class="text-red-500">*</span>
          </label>
          <select id="team_id" bind:value={$form.team_id} class="form-select {showErrors && !$form.team_id ? 'error' : ''}">
            <option value="">Pilih Tim Kerja </option>
            {#each teams as team}
              <option value={team.id}>{team.name}</option>
            {/each}
          </select>
          {#if showErrors && !$form.team_id}
            <p class="form-error">Tim Kerja harus dipilih.</p>
          {:else if $form.errors.team_id}
            <p class="form-error">{$form.errors.team_id}</p>
          {/if}
        </div>

        <div>
          <label for="activity" class="mb-1 block text-sm font-medium text-gray-700">
            Nama Kegiatan <span class="text-red-500">*</span>
          </label>
          <input id="activity" type="text" bind:value={$form.activity} placeholder="Contoh: Susenas, Pengolahan, dll" class="form-input {showErrors && !$form.activity.trim() ? 'error' : ''}" />
          {#if showErrors && !$form.activity.trim()}
            <p class="form-error">Nama kegiatan tidak boleh kosong.</p>
          {:else if $form.errors.activity}
            <p class="form-error">{$form.errors.activity}</p>
          {/if}
        </div>
      </div>

      <div class="flex items-center justify-end border-t border-gray-100 bg-gray-50 px-5 py-4 sm:px-8">
        <button type="button" on:click={goStep2} class="btn-indigo hover:bg-indigo-700">Selanjutnya</button>
      </div>
    </div>
  {/if}

  {#if step === 2}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
      <div class="flex flex-col gap-3 border-b border-gray-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <div>
          <h2 class="text-base font-semibold text-gray-800">Katalog Barang</h2>
          <p class="text-sm text-gray-500">Centang barang yang dibutuhkan, lalu atur jumlahnya.</p>
        </div>
        <div class="relative w-full sm:w-64">
          <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8" /><path stroke-linecap="round" d="m21 21-4.35-4.35" />
          </svg>
          <input type="search" bind:value={searchQuery} placeholder="Cari nama barang..." class="form-input py-2 pl-9 text-sm" />
        </div>
      </div>

      <div class="flex gap-2 overflow-x-auto border-b border-gray-100 bg-gray-50 px-4 py-2.5 md:hidden">
        <button
          type="button"
          on:click={() => {
            activeCategory = null
            searchQuery = ''
          }}
          class="shrink-0 rounded-full border px-3 py-1 text-xs font-medium transition
            {activeCategory === null && !searchQuery ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-gray-200 bg-white text-gray-600 hover:border-indigo-300 hover:text-indigo-600'}">
          Semua
        </button>
        {#each categories as cat}
          {@const badge = cartedByCategory[cat.id] || 0}
          <button
            type="button"
            on:click={() => {
              activeCategory = cat.id
              searchQuery = ''
            }}
            class="relative shrink-0 rounded-full border px-3 py-1 text-xs font-medium transition
              {activeCategory === cat.id ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-gray-200 bg-white text-gray-600 hover:border-indigo-300 hover:text-indigo-600'}">
            {cat.name}
            {#if badge > 0}
              <span class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-orange-400 text-[9px] font-bold leading-none text-white">{badge}</span>
            {/if}
          </button>
        {/each}
      </div>

      <div class="hidden md:flex" style="height: 460px;">
        <nav class="w-48 shrink-0 overflow-y-auto border-r border-gray-100 bg-gray-50">
          <button
            type="button"
            on:click={() => {
              activeCategory = null
              searchQuery = ''
            }}
            class="flex w-full items-center justify-between px-4 py-2.5 text-left text-sm transition
              {activeCategory === null && !searchQuery ? 'bg-indigo-50 font-semibold text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'}">
            <span>Semua Kategori</span>
          </button>
          {#each categories as cat}
            {@const badge = cartedByCategory[cat.id] || 0}
            <button
              type="button"
              on:click={() => {
                activeCategory = cat.id
                searchQuery = ''
              }}
              class="flex w-full items-center justify-between px-4 py-2.5 text-left text-sm transition
                {activeCategory === cat.id ? 'bg-indigo-50 font-semibold text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'}">
              <span class="leading-tight">{cat.name}</span>
              {#if badge > 0}
                <span class="ml-1 shrink-0 rounded-full bg-orange-400 px-1.5 py-0.5 text-[10px] font-bold leading-none text-white">{badge}</span>
              {/if}
            </button>
          {/each}
        </nav>

        <div class="flex-1 overflow-y-auto">
          {#if filteredItems.length === 0}
            <div class="flex h-full items-center justify-center py-16 text-center">
              <div>
                <svg class="mx-auto mb-3 h-10 w-10 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <circle cx="11" cy="11" r="8" /><path stroke-linecap="round" d="m21 21-4.35-4.35" />
                </svg>
                <p class="text-sm text-gray-400">Tidak ada barang ditemukan.</p>
              </div>
            </div>
          {:else}
            <ul class="divide-y divide-gray-100">
              {#each filteredItems as item (item.id)}
                <li class="flex items-center gap-4 px-5 py-3 transition-colors {cart[item.id] > 0 ? 'bg-indigo-50' : 'hover:bg-gray-50'}">
                  <input type="checkbox" id="item-d-{item.id}" checked={cart[item.id] > 0} on:change={() => toggleItem(item.id)} class="h-4 w-4 shrink-0 cursor-pointer accent-indigo-600" />
                  <label for="item-d-{item.id}" class="min-w-0 flex-1 cursor-pointer">
                    <span class="block text-sm font-medium leading-snug {cart[item.id] > 0 ? 'text-indigo-800' : 'text-gray-800'}">{item.name}</span>
                    <span class="text-[11px] text-gray-400">per {item.satuan}</span>
                  </label>
                  {#if cart[item.id] > 0}
                    <div class="flex shrink-0 items-center overflow-hidden rounded-md border border-gray-300">
                      <button type="button" on:click={() => decrement(item.id)} class="flex h-8 w-8 select-none items-center justify-center text-base text-gray-500 transition hover:bg-indigo-50 hover:text-indigo-600">−</button>
                      <input type="text" inputmode="numeric" value={cart[item.id]} on:change={(e) => setQty(item.id, e.target.value)} class="h-8 w-10 border-x border-gray-300 text-center text-sm font-semibold text-gray-800 focus:bg-indigo-50 focus:outline-none" />
                      <button type="button" on:click={() => increment(item.id)} class="flex h-8 w-8 select-none items-center justify-center text-base text-gray-500 transition hover:bg-indigo-50 hover:text-indigo-600">+</button>
                    </div>
                  {:else}
                    <span class="w-[104px] shrink-0"></span>
                  {/if}
                </li>
              {/each}
            </ul>
          {/if}
        </div>
      </div>

      <div class="md:hidden" style="max-height: 420px; overflow-y: auto;">
        {#if filteredItems.length === 0}
          <div class="py-12 text-center">
            <svg class="mx-auto mb-3 h-10 w-10 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8" /><path stroke-linecap="round" d="m21 21-4.35-4.35" />
            </svg>
            <p class="text-sm text-gray-400">Tidak ada barang ditemukan.</p>
          </div>
        {:else}
          <ul class="divide-y divide-gray-100">
            {#each filteredItems as item (item.id)}
              <li class="flex items-center gap-3 px-4 py-3 transition-colors {cart[item.id] > 0 ? 'bg-indigo-50' : 'hover:bg-gray-50'}">
                <input type="checkbox" id="item-m-{item.id}" checked={cart[item.id] > 0} on:change={() => toggleItem(item.id)} class="h-4 w-4 shrink-0 cursor-pointer accent-indigo-600" />
                <label for="item-m-{item.id}" class="min-w-0 flex-1 cursor-pointer">
                  <span class="block text-sm font-medium leading-snug {cart[item.id] > 0 ? 'text-indigo-800' : 'text-gray-800'}">{item.name}</span>
                  <span class="text-[11px] text-gray-400">per {item.satuan}</span>
                </label>
                {#if cart[item.id] > 0}
                  <div class="flex shrink-0 items-center overflow-hidden rounded-md border border-gray-300">
                    <button type="button" on:click={() => decrement(item.id)} class="flex h-8 w-8 select-none items-center justify-center text-base text-gray-500 transition hover:bg-indigo-50 hover:text-indigo-600">−</button>
                    <input type="text" inputmode="numeric" value={cart[item.id]} on:change={(e) => setQty(item.id, e.target.value)} class="h-8 w-10 border-x border-gray-300 text-center text-sm font-semibold text-gray-800 focus:bg-indigo-50 focus:outline-none" />
                    <button type="button" on:click={() => increment(item.id)} class="flex h-8 w-8 select-none items-center justify-center text-base text-gray-500 transition hover:bg-indigo-50 hover:text-indigo-600">+</button>
                  </div>
                {/if}
              </li>
            {/each}
          </ul>
        {/if}
      </div>

      <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 px-4 py-4 sm:px-6">
        <button type="button" on:click={() => (step = 1)} class="rounded bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-300">Kembali</button>
        <div class="flex items-center gap-3">
          {#if selectedItems.length > 0}
            <span class="hidden text-sm text-gray-500 sm:inline">{selectedItems.length} barang dipilih</span>
          {/if}
          <button type="button" on:click={goStep3} disabled={selectedItems.length === 0} class="btn-indigo hover:bg-indigo-700 {selectedItems.length === 0 ? 'cursor-not-allowed opacity-50' : ''}"> Tinjau </button>
        </div>
      </div>
    </div>
  {/if}

  {#if step === 3}
    <div class="max-w-2xl overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
      <div class="border-b border-gray-100 px-5 py-4 sm:px-8 sm:py-5">
        <h2 class="text-base font-semibold text-gray-800">Tinjau Permintaan</h2>
        <p class="text-sm text-gray-500">Pastikan semua data sudah benar sebelum diajukan.</p>
      </div>

      <div class="divide-y divide-gray-100">
        <div class="px-5 py-4 sm:px-8 sm:py-5">
          <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Identitas</h3>
          <dl class="space-y-2 text-sm">
            <div class="flex gap-4">
              <dt class="w-32 shrink-0 text-gray-500">Nama Pemohon</dt>
              <dd class="font-medium text-gray-800">{nameQuery}</dd>
            </div>
            <div class="flex gap-4">
              <dt class="w-32 shrink-0 text-gray-500">Tim</dt>
              <dd class="font-medium capitalize text-gray-800">{selectedTeam?.name ?? '-'}</dd>
            </div>
            <div class="flex gap-4">
              <dt class="w-32 shrink-0 text-gray-500">Kegiatan</dt>
              <dd class="font-medium text-gray-800">{$form.activity}</dd>
            </div>
          </dl>
        </div>

        <div class="px-5 py-4 sm:px-8 sm:py-5">
          <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400">
            Daftar Barang ({selectedItems.length} item)
          </h3>
          <ul class="space-y-2">
            {#each selectedItems as si, i}
              <li class="flex items-center gap-3 rounded-lg border border-gray-100 bg-gray-50 px-4 py-2.5">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-600">{i + 1}</span>
                <span class="flex-1 text-sm text-gray-800">{si.name}</span>
                <span class="shrink-0 rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
                  {si.qty_requested}
                  {si.satuan}
                </span>
                <button type="button" on:click={() => removeFromCart(si.id)} class="shrink-0 text-gray-300 transition hover:text-red-500" title="Hapus">✕</button>
              </li>
            {/each}
          </ul>
        </div>
      </div>

      {#if $form.errors.items}
        <div class="px-5 pb-4 sm:px-8">
          <p class="form-error">{$form.errors.items}</p>
        </div>
      {/if}

      <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 px-5 py-4 sm:px-8">
        <button type="button" on:click={() => (step = 2)} class="rounded bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-300">Kembali</button>
        <form on:submit|preventDefault={submit}>
          <LoadingButton loading={$form.processing} class="btn-indigo hover:bg-indigo-700" type="submit">Ajukan Permintaan</LoadingButton>
        </form>
      </div>
    </div>
  {/if}
</div>
