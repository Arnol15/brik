<?php
// fetch-posts.php
// Returns JSON with server-rendered HTML for posts table / cards and pagination
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../../config/database.php';

// Read parameters (with safe defaults)
$page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
$per_page = isset($_POST['per_page']) ? max(1, intval($_POST['per_page'])) : 10;
$q = isset($_POST['q']) ? trim($_POST['q']) : '';

// Build search filter (search title, category title, author names/username)
$search_sql = '';
$params = [];
$types = '';
if ($q !== '') {
    $q_like = '%' . $q . '%';
    $search_sql = "AND (posts.title LIKE ? OR categories.title LIKE ? OR users.username LIKE ? OR users.firstname LIKE ? OR users.lastname LIKE ?)";
    $params = [$q_like, $q_like, $q_like, $q_like, $q_like];
    $types = str_repeat('s', count($params));
}

// Count total matching rows
$count_sql = "SELECT COUNT(*) FROM posts
              LEFT JOIN categories ON posts.category_id = categories.id
              LEFT JOIN users ON posts.author_id = users.id
              WHERE 1=1 $search_sql";

$stmt = mysqli_prepare($connection, $count_sql);
if ($search_sql !== '') {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $total_rows);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

$total_rows = intval($total_rows);
$total_pages = max(1, (int) ceil($total_rows / $per_page));
if ($page > $total_pages) $page = $total_pages;
$offset = ($page - 1) * $per_page;

// Fetch paginated results
$select_sql = "SELECT posts.id, posts.title, posts.thumbnail, categories.title AS category_title,
               users.firstname, users.lastname, users.username, posts.created_at
               FROM posts
               LEFT JOIN categories ON posts.category_id = categories.id
               LEFT JOIN users ON posts.author_id = users.id
               WHERE 1=1 $search_sql
               ORDER BY posts.created_at DESC
               LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($connection, $select_sql);

// bind params: search params first, then two integers
if ($search_sql !== '') {
    // types are e.g. 'sssssii' (search types + ii)
    $full_types = $types . 'ii';
    mysqli_stmt_bind_param($stmt, $full_types, ...array_merge($params, [$per_page, $offset]));
} else {
    mysqli_stmt_bind_param($stmt, 'ii', $per_page, $offset);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Build HTML for desktop table rows
$table_html = '';
while ($row = mysqli_fetch_assoc($result)) {
    $id = (int)$row['id'];
    $title = htmlspecialchars($row['title']);
    $category = htmlspecialchars($row['category_title'] ?? 'Uncategorized');
    // Choose author display: firstname lastname if exists else username
    $authorName = '';
    if (!empty($row['firstname']) || !empty($row['lastname'])) {
        $authorName = trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? ''));
    }
    if (empty($authorName)) $authorName = htmlspecialchars($row['username'] ?? 'Unknown');
    $authorName = htmlspecialchars($authorName);

    $editUrl = "edit-post.php?id={$id}";
    $deleteUrl = "logic/delete-post.php?id={$id}";
    $safeTitleAttr = htmlspecialchars($row['title'], ENT_QUOTES);

    $table_html .= "<tr class=\"bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-600 transition\">";
    $table_html .= "<td class=\"p-3 border-b border-gray-200 dark:border-gray-600 break-words\">{$title}</td>";
    $table_html .= "<td class=\"p-3 border-b border-gray-200 dark:border-gray-600 break-words\">{$category}</td>";
    $table_html .= "<td class=\"p-3 border-b border-gray-200 dark:border-gray-600 break-words\">{$authorName}</td>";
    $table_html .= "<td class=\"p-3 border-b border-gray-200 dark:border-gray-600 text-center\">";
    $table_html .= "<div class=\"flex justify-center gap-2\">";
    $table_html .= "<a href=\"{$editUrl}\" class=\"px-3 py-2 bg-green-600 text-white text-xs sm:text-sm font-medium rounded-md hover:bg-green-700 transition\">Edit</a>";
    $table_html .= "<a href=\"{$deleteUrl}\" data-title=\"{$safeTitleAttr}\" class=\"js-delete px-3 py-2 bg-red-500 text-white text-xs sm:text-sm font-medium rounded-md hover:bg-red-600 transition\">Delete</a>";
    $table_html .= "</div></td></tr>";
}

// Build HTML for mobile cards (we need to rewind result set or rerun query. Simpler: rerun)
mysqli_stmt_close($stmt);

// Re-run the select to get rows again for cards
$stmt = mysqli_prepare($connection, $select_sql);
if ($search_sql !== '') {
    $full_types = $types . 'ii';
    mysqli_stmt_bind_param($stmt, $full_types, ...array_merge($params, [$per_page, $offset]));
} else {
    mysqli_stmt_bind_param($stmt, 'ii', $per_page, $offset);
}
mysqli_stmt_execute($stmt);
$result_cards = mysqli_stmt_get_result($stmt);

$cards_html = '';
while ($row = mysqli_fetch_assoc($result_cards)) {
    $id = (int)$row['id'];
    $title = htmlspecialchars($row['title']);
    $category = htmlspecialchars($row['category_title'] ?? 'Uncategorized');
    $authorName = '';
    if (!empty($row['firstname']) || !empty($row['lastname'])) {
        $authorName = trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? ''));
    }
    if (empty($authorName)) $authorName = htmlspecialchars($row['username'] ?? 'Unknown');
    $authorName = htmlspecialchars($authorName);
    $editUrl = "edit-post.php?id={$id}";
    $deleteUrl = "logic/delete-post.php?id={$id}";
    $safeTitleAttr = htmlspecialchars($row['title'], ENT_QUOTES);

    $cards_html .= "<div class=\"bg-white dark:bg-gray-800 p-4 rounded-lg shadow border border-gray-200 dark:border-gray-700\">";
    $cards_html .= "<h3 class=\"text-lg font-semibold text-gray-800 dark:text-gray-100 mb-1\">{$title}</h3>";
    $cards_html .= "<p class=\"text-sm text-gray-500 dark:text-gray-400 mb-2\">Author: <span class=\"font-medium text-gray-700 dark:text-gray-300\">{$authorName}</span></p>";
    $cards_html .= "<p class=\"text-sm text-gray-500 dark:text-gray-400 mb-3\">Category: <span class=\"font-medium text-gray-700 dark:text-gray-300\">{$category}</span></p>";
    $cards_html .= "<div class=\"flex flex-wrap gap-2\">";
    $cards_html .= "<a href=\"{$editUrl}\" class=\"flex-1 text-center px-3 py-2 bg-green-600 text-white text-xs font-medium rounded-md hover:bg-green-700 transition\">Edit</a>";
    $cards_html .= "<a href=\"{$deleteUrl}\" data-title=\"{$safeTitleAttr}\" class=\"js-delete flex-1 text-center px-3 py-2 bg-red-500 text-white text-xs font-medium rounded-md hover:bg-red-600 transition\">Delete</a>";
    $cards_html .= "</div></div>";
}

// Build pagination HTML (simple)
$pagination_html = '';
if ($total_pages > 1) {
    $pagination_html .= '<nav class="inline-flex items-center gap-2">';
    // prev
    $prev_page = max(1, $page - 1);
    $disabled_prev = ($page <= 1) ? 'opacity-50 pointer-events-none' : '';
    $pagination_html .= "<button data-page=\"{$prev_page}\" class=\"px-3 py-2 rounded-md border {$disabled_prev}\">Prev</button>";

    // page numbers (limit visible pages)
    $max_visible = 7;
    $start = max(1, $page - intval($max_visible/2));
    $end = min($total_pages, $start + $max_visible - 1);
    if ($end - $start + 1 < $max_visible) {
        $start = max(1, $end - $max_visible + 1);
    }
    for ($p = $start; $p <= $end; $p++) {
        $active = ($p === $page) ? 'bg-green-600 text-white' : 'bg-white dark:bg-gray-800';
        $pagination_html .= "<button data-page=\"{$p}\" class=\"px-3 py-2 rounded-md border {$active}\">{$p}</button>";
    }

    // next
    $next_page = min($total_pages, $page + 1);
    $disabled_next = ($page >= $total_pages) ? 'opacity-50 pointer-events-none' : '';
    $pagination_html .= "<button data-page=\"{$next_page}\" class=\"px-3 py-2 rounded-md border {$disabled_next}\">Next</button>";
    $pagination_html .= '</nav>';
}

// Optional server-side alert (if you set one in session)
$alert_html = '';
if (isset($_SESSION['alert'])) {
    $type = $_SESSION['alert']['type'] ?? 'info';
    $msg = htmlspecialchars($_SESSION['alert']['message'] ?? '');
    $classes = $type === 'success' ? 'bg-green-100 text-green-700' : ($type === 'error' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-700');
    $alert_html = "<div class='p-3 rounded {$classes}'>{$msg}</div>";
    unset($_SESSION['alert']);
}

// Output JSON
echo json_encode([
    'table_html' => $table_html,
    'cards_html' => $cards_html,
    'pagination_html' => $pagination_html,
    'alert_html' => $alert_html,
    'total_rows' => $total_rows,
    'page' => $page,
    'per_page' => $per_page,
], JSON_UNESCAPED_UNICODE);
exit;
