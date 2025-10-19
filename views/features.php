<?php
$QUESTIONS = [
    "Has Sourcing components been simple?",
    "Are your machines fully efficient?",
    "Need smarter automation today?",
    "Is maintenance overdue already?",
];
?>
<script>
  // Fade-up animation for overlay card for Features section when it enters viewport
document.addEventListener("DOMContentLoaded", () => {
    const overlay = document.getElementById("feature-overlay");

    const observer = new IntersectionObserver(
        entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    overlay.classList.remove("opacity-0", "translate-y-6");
                    overlay.classList.add("opacity-100", "translate-y-0");
                    observer.unobserve(overlay); // run only once
                }
            });
        },
        { threshold: 0.3 }
    );

    if (overlay) observer.observe(overlay);
});
</script>

<section class="flexCenter flex-col bg-white dark:bg-gray-900 transition-colors duration-300">
  <div class="padding-container max-container w-full pb-12 px-4 sm:px-6 lg:px-8">
    <p class="uppercase regular-18 mt-4 mb-3 text-green-500 text-center sm:text-left">What we do?</p>

    <div class="flex flex-col lg:flex-row flex-wrap justify-between gap-6 lg:gap-10">
      <h2 class="bold-32 sm:bold-40 lg:bold-64 xl:max-w-[490px] text-gray-900 dark:text-white text-center lg:text-left">
        “Your Shortcut to Clarity.”
      </h2>

      <p class="regular-15 sm:regular-16 text-gray-600 dark:text-gray-300 xl:max-w-[720px] text-justify leading-relaxed">
        We offer diagnostics, support, and repair for industrial machinery, ensuring motors, drives, PLCs, sensors,
        and production systems operate efficiently with minimal downtime. In addition, we act as a link bridge
        for sourcing machinery and components, connecting clients to trusted local and international suppliers
        for spare parts, industrial equipment, and agricultural machinery. As we grow, we are expanding into
        installation and control cabinet setup, providing organized, safe, and end-to-end solutions. We are also
        exploring cloud integration through OPC UA and MQTT, enabling smarter factories with real-time monitoring
        and industrial IoT solutions. By engaging with industry leaders like Wilo, Krones, IFM, and Kone, and
        through participation in events, we continue to strengthen our expertise. Our mission is to deliver
        dependable services today while preparing industries for tomorrow’s technologies.
      </p>
    </div>
  </div>

  <div class="flexCenter max-container relative w-full px-3 sm:px-6 lg:px-0">
    <!-- Background Image -->
    <img src="images/track_1.jpg"
         alt="io-module connection"
         class="w-full object-cover object-center rounded-none sm:rounded-xl lg:rounded-3xl 2xl:rounded-5xl h-[280px] sm:h-[380px] md:h-[480px] lg:h-auto" />

    <!-- Overlay card -->
    <div id="feature-overlay"
         class="absolute inset-x-4 sm:inset-x-auto sm:left-[5%] lg:top-20 bottom-6 sm:bottom-auto 
                flex flex-col sm:flex-row bg-white/95 dark:bg-neutral-800/90
                py-4 sm:py-6 md:py-8 px-4 sm:pl-5 sm:pr-7 gap-3 sm:gap-4
                rounded-2xl sm:rounded-3xl border border-gray-200 dark:border-gray-700
                shadow-md transition-colors duration-300 max-w-[95%] sm:max-w-md md:max-w-lg lg:max-w-none mx-auto
                opacity-0 translate-y-6 transition-all duration-700 ease-out">

      <!-- Icon -->
      <div class="flex justify-center sm:justify-start">
        <img src="images/meter.svg"
             alt="meter"
             class="h-10 w-auto sm:h-full invert-0 dark:invert" />
      </div>

      <!-- Texts -->
      <div class="flex flex-col w-full text-center sm:text-left">
        <p class="bold-16 text-green-500 mb-2">Ask any Questions!</p>

        <div class="flex flex-col gap-1 sm:gap-2">
          <?php foreach ($QUESTIONS as $q): ?>
            <p class="regular-15 sm:regular-16 text-gray-600 dark:text-gray-300"><?= $q ?></p>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>