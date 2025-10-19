<?php
// Session already started in index.php, so no need to call session_start()
// Just in case it's opened directly, protect it:
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<section class="dashboard">

    <!-- Feedback messages -->
    <?php if (isset($_SESSION['add-project-success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= $_SESSION['add-project-success']; unset($_SESSION['add-project-success']); ?>
        </div>
    <?php elseif (isset($_SESSION['add-project-error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= $_SESSION['add-project-error']; unset($_SESSION['add-project-error']); ?>
        </div>
    <?php endif; ?>

    <form action="../logic/add-project-logic.php" method="POST" enctype="multipart/form-data"
          class="bg-white dark:bg-[#2a3b45] shadow-md rounded-xl p-6 space-y-6 w-full">

        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white border-b pb-3">
            Add New Project
        </h2>

        <!-- Project Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Project Name
            </label>
            <input type="text" name="project_name" placeholder="Enter project name" required
                   class="w-full px-4 py-2 border rounded-md shadow-sm 
                          dark:bg-gray-700 dark:text-white dark:border-gray-600
                          focus:outline-none focus:ring-2 focus:ring-[#014d3a] focus:border-[#014d3a]">
        </div>

        <!-- Description -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Description
            </label>
            <textarea name="description" rows="4" placeholder="Brief description of the project" required
                      class="w-full px-4 py-2 border rounded-md shadow-sm 
                             dark:bg-gray-700 dark:text-white dark:border-gray-600
                             focus:outline-none focus:ring-2 focus:ring-[#014d3a] focus:border-[#014d3a]"></textarea>
        </div>

        <!-- Upload Sections -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php
            $sections = [
                'documentation' => 'Upload manuals, guides, or project notes.',
                'programs' => 'Upload PLC, HMI, or configuration software.',
                'schematics' => 'Upload electrical or pneumatic diagrams.',
                'bom' => 'Upload the BOM file for components and parts.',
                'licenses' => 'Upload licensing files and README documents.'
            ];

            foreach ($sections as $key => $desc): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <?= ucfirst($key) ?>
                    </label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                        <?= htmlspecialchars($desc) ?>
                    </p>
                    <input type="file" name="<?= $key ?>"
                           class="w-full text-sm text-gray-500 
                                  file:mr-3 file:py-2 file:px-4 
                                  file:rounded-md file:border-0 
                                  file:bg-[#014d3a] file:text-white 
                                  hover:file:bg-[#013828] cursor-pointer">
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Submit -->
        <div class="pt-4">
            <button type="submit"
                    class="w-full py-3 bg-[#014d3a] text-white font-medium rounded-md 
                           hover:bg-[#013828] transition shadow-md">
                Save Project
            </button>
        </div>
    </form>
</section>
