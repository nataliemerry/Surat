<script>
  export let totalPages = 1
  export let currentPage = 1
  export let itemsPerPage = 10
  export let totalItems = 0
  export let onPageChange = null
  export let onItemsPerPageChange = null

  const goToPage = (page) => {
    if (onPageChange && page !== currentPage && page > 0 && page <= totalPages) {
      onPageChange(page)
    }
  }

  const handleItemsPerPageChange = (event) => {
    if (onItemsPerPageChange) {
      onItemsPerPageChange(parseInt(event.target.value))
    }
  }

  $: paginationRange = (() => {
    const delta = 1
    const range = []
    const rangeWithDots = []
    let l

    range.push(1)
    for (let i = currentPage - delta; i <= currentPage + delta; i++) {
      if (i < totalPages && i > 1) {
        range.push(i)
      }
    }
    if (totalPages > 1) {
      range.push(totalPages)
    }

    for (const i of range) {
      if (l) {
        if (i - l === 2) {
          rangeWithDots.push(l + 1)
        } else if (i - l !== 1) {
          rangeWithDots.push('...')
        }
      }
      rangeWithDots.push(i)
      l = i
    }

    return rangeWithDots
  })()

  $: startItem = totalItems > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0
  $: endItem = totalItems > 0 ? Math.min(currentPage * itemsPerPage, totalItems) : 0
</script>

<div class="flex flex-col items-center justify-between gap-4 pt-4 sm:flex-row">
  <div class="text-sm text-gray-600">
    {#if totalItems > 0}
      Menampilkan <span class="font-semibold text-gray-800">{startItem}</span> sampai <span class="font-semibold text-gray-800">{endItem}</span> dari <span class="font-semibold text-gray-800">{totalItems}</span>
    {:else}
      Tidak ada data
    {/if}
  </div>

  <div class="flex items-center gap-4">
    {#if totalPages > 1}
      <div class="flex flex-wrap items-center gap-1">
        <button class="rounded border px-2 py-1.5 text-sm leading-4 focus:outline-none enabled:hover:bg-gray-100 disabled:border-gray-300 disabled:text-gray-400" on:click={() => goToPage(currentPage - 1)} disabled={currentPage === 1}>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
        </button>
        {#each paginationRange as page}
          {#if page === '...'}
            <span class="px-3 py-1.5 text-sm leading-4 text-gray-500">...</span>
          {:else}
            <button class={`rounded border px-3 py-1.5 text-sm leading-4 ${page === currentPage ? 'border-indigo-600 bg-indigo-600 font-bold text-white' : 'hover:bg-gray-100'}`} on:click={() => goToPage(page)}>
              {page}
            </button>
          {/if}
        {/each}
        <button class="rounded border px-2 py-1.5 text-sm leading-4 focus:outline-none enabled:hover:bg-gray-100 disabled:border-gray-300 disabled:text-gray-400" on:click={() => goToPage(currentPage + 1)} disabled={currentPage === totalPages}>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
        </button>
      </div>
    {/if}

    <div class="flex items-center gap-2 text-sm">
      <span class="text-gray-600">Tampilkan:</span>
      <select bind:value={itemsPerPage} on:change={handleItemsPerPageChange} class="py-1 text-sm border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500">
        <option value={5}>5</option>
        <option value={10}>10</option>
        <option value={15}>15</option>
        <option value={20}>20</option>
        <option value={25}>25</option>
      </select>
    </div>
  </div>
</div>
