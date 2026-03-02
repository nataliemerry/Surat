<script>
  export let totalPages = 1
  export let currentPage = 1
  export let onChange = null

  const goToPage = (page) => {
    if (onChange && page !== currentPage && page > 0 && page <= totalPages) {
      onChange(page)
    }
  }
</script>

{#if totalPages > 1}
  <div class="-mb-1 mt-2 flex flex-wrap">
    <button class={`mb-1 mr-1 rounded border px-4 py-3 text-sm leading-4 focus:outline-none ${currentPage === 1 ? ' border-gray-300 text-gray-300' : 'hover:bg-gray-200'}`} on:click={() => goToPage(currentPage - 1)} disabled={currentPage === 1}> Previous </button>
    {#each Array(totalPages)
      .fill(0)
      .map((_, i) => i + 1) as page}
      <button class={`mb-1 mr-1 rounded border px-4 py-3 text-sm leading-4 ${page === currentPage ? 'bg-[#5661B3] font-bold text-white hover:bg-[#2F365F]' : 'hover:bg-gray-200'}`} on:click={() => goToPage(page)}> {page}</button>
    {/each}

    <button class={`mb-1 mr-1 rounded border px-4 py-3 text-sm leading-4 focus:outline-none ${currentPage === totalPages ? 'border-gray-300 text-gray-300' : 'hover:bg-gray-200'}`} on:click={() => goToPage(currentPage + 1)} disabled={currentPage === totalPages}> Next </button>
  </div>
{/if}

<!-- 
<script>
  import { inertia } from '@inertiajs/svelte'
  export let links = []
</script>

{#if links.length > 3}
  <div class={`-mb-1 flex flex-wrap ${$$restProps.class || ''}`}>
    {#each links as link, key (key)}
      {#if link.url === null}
        <div class="mb-1 mr-1 rounded border px-4 py-3 text-sm leading-4 text-gray-400">
          {@html link.label}
        </div>
      {:else}
        <a use:inertia href={link.url} class={`mb-1 mr-1 rounded border px-4 py-3 text-sm leading-4 hover:bg-white focus:border-indigo-500 focus:text-indigo-500 ${link.active && 'bg-white'}`}>
          {@html link.label}
        </a>
      {/if}
    {/each}
  </div>
{/if} -->
