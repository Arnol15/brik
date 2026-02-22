<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../../config/database.php';
?>

<div class="h-full flex flex-col">
  <!-- Title + Search -->
  <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 dark:text-gray-200">Manage Posts</h2>

    <div class="flex items-center gap-2 w-full sm:w-auto">
      <input id="postsSearch" type="search" placeholder="Search by title, category or author..."
             class="w-full sm:w-80 px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-600"
      />
      <button id="searchClear" class="px-3 py-2 bg-gray-200 dark:bg-gray-700 rounded-md">Clear</button>
    </div>
  </div>

  <!-- Flash Messages (index.php may also show global alert) -->
  <div id="localAlerts" class="mb-4"></div>

  <!-- Desktop table (hidden on small screens) -->
  <div id="postsTableWrapper" class="hidden sm:block flex-1 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700">
    <table id="postsTable" class="w-full text-sm sm:text-base border-collapse">
      <thead>
        <tr class="bg-gray-100 dark:bg-gray-700 text-left font-semibold text-gray-700 dark:text-gray-200">
          <th class="p-3 border-b border-gray-200 dark:border-gray-600">Title</th>
          <th class="p-3 border-b border-gray-200 dark:border-gray-600">Category</th>
          <th class="p-3 border-b border-gray-200 dark:border-gray-600">Author</th>
          <th class="p-3 border-b border-gray-200 dark:border-gray-600 text-center">Actions</th>
        </tr>
      </thead>
      <tbody id="postsTableBody">
        <!-- rows inserted via AJAX -->
      </tbody>
    </table>

    <!-- pagination -->
    <div id="postsPagination" class="p-4 flex justify-center"></div>
  </div>

  <!-- Mobile cards (visible on small screens) -->
  <div id="postsCardsWrapper" class="block sm:hidden space-y-3">
    <!-- cards inserted via AJAX -->
  </div>
</div>

<script>
(() => {
  const fetchUrl = 'logic/fetch-posts.php';
  const perPage = 10;
  let currentPage = 1;
  let currentQuery = '';

  const tableBody = document.getElementById('postsTableBody');
  const cardsWrapper = document.getElementById('postsCardsWrapper');
  const paginationEl = document.getElementById('postsPagination');
  const searchInput = document.getElementById('postsSearch');
  const clearBtn = document.getElementById('searchClear');
  const localAlerts = document.getElementById('localAlerts');

  function setLoading(state) {
    if (state) {
      tableBody.innerHTML = '<tr><td colspan="4" class="p-6 text-center">Loading…</td></tr>';
      cardsWrapper.innerHTML = '<div class="p-4 text-center">Loading…</div>';
      paginationEl.innerHTML = '';
    }
  }

  function renderResponse(json) {
    // Insert table rows and cards and pagination
    tableBody.innerHTML = json.table_html || '<tr><td colspan="4" class="p-6 text-center">No posts found.</td></tr>';
    cardsWrapper.innerHTML = json.cards_html || '<div class="p-4 text-center">No posts found.</div>';
    paginationEl.innerHTML = json.pagination_html || '';

    // show server-side alert if any (single-message style)
    if (json.alert_html) {
      localAlerts.innerHTML = json.alert_html;
      // auto-dismiss after 4s
      setTimeout(() => { localAlerts.innerHTML = ''; }, 4000);
    } else {
      localAlerts.innerHTML = '';
    }
  }

  async function fetchPosts(page = 1, q = '') {
    currentPage = page;
    currentQuery = q;
    setLoading(true);

    try {
      const form = new FormData();
      form.append('page', page);
      form.append('per_page', perPage);
      form.append('q', q);

      const res = await fetch(fetchUrl, { method: 'POST', body: form, credentials: 'same-origin' });
      if (!res.ok) throw new Error('Network error');
      const json = await res.json();
      renderResponse(json);
    } catch (err) {
      tableBody.innerHTML = `<tr><td colspan="4" class="p-6 text-center text-red-600">Error loading posts.</td></tr>`;
      cardsWrapper.innerHTML = `<div class="p-4 text-center text-red-600">Error loading posts.</div>`;
      paginationEl.innerHTML = '';
      console.error(err);
    }
  }

  // Events: search (debounced), pagination clicks
  let debounceTimer;
  searchInput.addEventListener('input', (e) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
      fetchPosts(1, searchInput.value.trim());
    }, 300);
  });

  clearBtn.addEventListener('click', () => {
    searchInput.value = '';
    fetchPosts(1, '');
  });

  // Delegate pagination clicks
  paginationEl.addEventListener('click', (e) => {
    if (e.target.matches('[data-page]')) {
      const page = parseInt(e.target.getAttribute('data-page'), 10) || 1;
      fetchPosts(page, currentQuery);
    }
  });

  // Delegate confirm-delete for dynamic content (table & cards)
  document.addEventListener('click', function(e) {
    if (e.target.matches('.js-delete') || e.target.closest('.js-delete')) {
      const link = e.target.matches('.js-delete') ? e.target : e.target.closest('.js-delete');
      e.preventDefault();
      const title = link.getAttribute('data-title') || 'this post';
      if (confirm(`Are you sure you want to permanently delete "${title}"? This action cannot be undone.`)) {
        window.location.href = link.href;
      }
    }
  });

  // Initial load
  document.addEventListener('DOMContentLoaded', () => fetchPosts(1, ''));
})();
</script>
