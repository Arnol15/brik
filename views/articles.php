<?php 
function normalize_path($p) {
    $p = trim((string)$p);
    if ($p === '') return $p;
    return ($p[0] === '/') ? $p : '/' . $p;
}

$POST_URL = [
    "images/user1.jpg",
    "images/user2.jpg",
    "images/user3.jpg",
    "images/user4.jpg",
];

$ARTICLES = [
    [
        "backgroundImage" => "images/track_2.jpg",
        "title" => "The Hidden Cost of Poor Maintenance in Agricultural Machines",
        "subtitle" => "Ramon M. | 20 Aug 2023",
        "impressions" => "1.2k"
    ],
    [
        "backgroundImage" => "images/electrical.jpg",
        "title" => "The Hidden Cost of Poor Maintenance in Industrial Machines",
        "subtitle" => "Ramon M. | 20 Aug 2023",
        "impressions" => "1.2k"
    ]
];
?>

<section class="relative flex flex-col py-10 lg:py-20 bg-white dark:bg-gray-900 transition-colors">

    <!-- Horizontal scroll container -->
    <div class="hide-scrollbar flex h-[340px] w-full items-start justify-start gap-8 overflow-x-auto sm:h-[400px] lg:h-[550px] xl:h-[610px]">
        <?php foreach ($ARTICLES as $article): 
            $bg = normalize_path($article['backgroundImage']);
        ?>
            <div 
                class="parallax-card flex-shrink-0 h-full w-full min-w-[85%] sm:min-w-[400px] relative rounded-3xl overflow-hidden shadow-lg"
                style="background-image: url('<?= htmlspecialchars($bg, ENT_QUOTES) ?>'); background-size: cover; background-position: center;"
            >
                <noscript>
                    <img src="<?= htmlspecialchars($bg, ENT_QUOTES) ?>" alt="<?= htmlspecialchars($article['title'], ENT_QUOTES) ?>" style="width:100%; display:block;">
                </noscript>

                <div class="relative z-10 flex h-full flex-col justify-between p-6 lg:px-12 lg:py-10 bg-black/40 rounded-3xl">
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-green-500 p-2">
                            <img src="./images/impression.svg" alt="map" class="w-[38px] h-[38px] filter invert brightness-0 dark:invert-0" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <h4 class="font-bold text-lg text-white"><?= htmlspecialchars($article['title'], ENT_QUOTES) ?></h4>
                            <p class="text-sm text-gray-200"><?= htmlspecialchars($article['subtitle'], ENT_QUOTES) ?></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-6">
                        <span class="flex -space-x-4 overflow-hidden">
                            <?php foreach ($POST_URL as $url): 
                                $avatar = normalize_path($url);
                            ?>
                                <img src="<?= htmlspecialchars($avatar, ENT_QUOTES) ?>" alt="avatar" class="inline-block h-10 w-10 rounded-full border-2 border-white dark:border-gray-700" />
                            <?php endforeach; ?>
                        </span>
                        <p class="font-bold text-white"><?= htmlspecialchars($article['impressions'], ENT_QUOTES) ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Overlay Card (slightly overlapping below images) --> 
    <div class="overlay-card absolute left-1/2 -translate-x-1/2 
            translate-y-4 sm:translate-y-8 md:translate-y-10 lg:translate-y-12 xl:translate-y-14 
            lg:left-[55%] xl:left-[58%] 
            bottom-0 z-20 
            w-[90%] sm:w-[80%] md:w-[70%] lg:w-auto">
    
            <div class="bg-green-500 p-6 md:p-8 lg:max-w-[420px] xl:max-w-[560px] 
                        xl:rounded-4xl xl:px-12 xl:py-16 relative w-full 
                        overflow-hidden rounded-3xl shadow-2xl">
                
                <h2 class="text-lg md:text-2xl 2xl:text-4xl capitalize text-white leading-snug">
                    <strong>Feeling Lost</strong> and Not knowing The way?
                </h2>

                <p class="mt-4 text-sm xl:text-base text-white">
                    “When systems fail and solutions seem out of reach, the uncertainty can feel overwhelming. 
                    We step in to clear the path, offering the expertise and direction you need to keep operations running smoothly.”
                </p>

                <img 
                    src="./images/quote.svg" 
                    alt="quote" 
                    class="absolute bottom-0 right-0 w-[120px] h-[140px] opacity-80 filter invert brightness-20"
                />
            </div>
</div>
</section>
