<?php require __DIR__ . '/../constants/services.php'; ?>
<script src="https://cdn.tailwindcss.com"></script>
<section class="flex-col flexCenter overflow-hidden bg-feature-bg bgcenter bg-no-repeat py-24 bg-white dark:bg-gray-900 transition-colors">
    <div class="max-container padding-container relative w-full flex justify-end">
        
        <!-- Image Side -->
        <div class="flex flex-1 lg:min-h-[900px]">
            <img 
                src="./images/io-master.png" 
                alt="phone" 
                width="600" 
                height="1000" 
                class="feature-phone" 
            />
        </div>

        <!-- Services Side -->
        <div class="z-20 flex w-full flex-col lg:w-[60%]">
            <div class="relative">
                <h2 class="bold-32 lg:bold-40 text-gray-900 dark:text-white transition-colors">
                    Our Services
                </h2>
            </div>

            <ul class="mt-10 grid gap-10 md:grid-cols-2 lg:mb-20 lg:gap-20">
                <?php foreach ($SERVICES as $service): ?>
                    <li class="flex w-full flex-1 flex-col items-start">
                        <!-- Icon -->
                        <div class="rounded-full p-1 lg:p-3 bg-green-500">
                            <img 
                                src="<?= $service['icon']; ?>" 
                                alt="<?= htmlspecialchars($service['title']); ?>" 
                                width="28" 
                                height="28" 
                                class="dark:invert"
                            />
                        </div>

                        <!-- Title -->
                        <h2 class="bold-16 lg:bold-20 mt-2 capitalize text-gray-900 dark:text-gray-100 transition-colors">
                            <?= htmlspecialchars($service['title']); ?>
                        </h2>

                        <!-- Description -->
                        <p class="regular-16 mt-2 text-gray-700 dark:text-gray-300 lg:mt-[30px] transition-colors">
                            <?= htmlspecialchars($service['description']); ?>
                        </p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>
