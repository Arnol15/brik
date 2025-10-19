<?php require __DIR__ . '/../constants/socials.php'; ?> 

<section class="flexCenter w-full flex-col pb-[100px] dark:bg-gray-900 bg-white transition-colors duration-300">
    <div class="get-app flex w-full flex-col lg:flex-row items-center lg:items-start justify-between gap-10">

        <!-- Left Content -->
        <div class="z-20 flex w-full flex-1 flex-col items-center lg:items-start justify-center gap-6 lg:gap-12 text-center lg:text-left">
            <h2 class="bold-20 lg:bold-32 xl:max-w-[320px]">
                Quality service for <br class="hidden lg:block"> Optimized Performance
            </h2>
            <p class="regular-16 text-gray-600 dark:text-gray-300">
                Follow us on our Socials.
            </p>

            <!-- Social Icons -->
            <!-- Social Icons -->
            <div class="flex items-center justify-center lg:justify-start gap-5">
                <?php foreach ($SOCIALS as $icon): ?>
                    <a href="/" 
                    aria-label="<?= pathinfo($icon, PATHINFO_FILENAME) ?>" 
                    class="p-2 rounded-full bg-white/30 dark:bg-white/10 hover:bg-black/5 dark:hover:bg-white/30 transition">
                        <img 
                            src="/<?= $icon ?>" 
                            alt="<?= pathinfo($icon, PATHINFO_FILENAME) ?> icon" 
                            width="30" 
                            height="30"
                            class="filter brightness-0 invert"
                        />
                    </a>
                <?php endforeach; ?>
            </div>

        </div>

        <!-- Right Content -->
        <div class="flex w-full flex-1 items-center justify-center lg:justify-end">
            <img 
                src="./images/controller_1.png" 
                alt="controller" 
                width="600" 
                height="1070" 
                class="max-w-[80%] sm:max-w-[60%] lg:max-w-[600px] h-auto"
            />
        </div>

    </div>
</section>

