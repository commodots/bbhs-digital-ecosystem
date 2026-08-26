<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { ArrowRight, PlayCircle, ChevronLeft, ChevronRight } from 'lucide-vue-next';

const slides = [
    {
        image: '/images/bbhs/school/bbhs-school-gate.png',
        eyebrow: 'BBHS OLD BOYS ASSOCIATION',
        title: 'United by Heritage.',
        highlight: 'Driven by Purpose.',
        ending: 'Building Our Future.',
        description:
            'Connecting generations of Baptist Boys\' High School Old Boys to preserve our legacy, support our school, and uplift one another.',
    },
    {
        image: '/images/bbhs/students/bbhs-students-01.jpg',
        eyebrow: 'THE NEXT GENERATION',
        title: 'Investing in',
        highlight: 'Our Future.',
        ending: 'Inspiring Excellence.',
        description:
            'Supporting the students of Baptist Boys\' High School and helping prepare the next generation for a future of excellence and purpose.',
    },
    {
        image: '/images/bbhs/students/bbhs-students-02.jpg',
        eyebrow: 'EDUCATION & MENTORSHIP',
        title: 'Guiding Young',
        highlight: 'Minds.',
        ending: 'Building Character.',
        description:
            'Creating meaningful connections between today\'s students and the generations of Old Boys who came before them.',
    },
    {
        image: '/images/bbhs/students/bbhs-students-03.jpg',
        eyebrow: 'EXCELLENCE & TEAMWORK',
        title: 'Growing Through',
        highlight: 'Discipline.',
        ending: 'Achieving Together.',
        description:
            'Supporting activities that build confidence, teamwork, discipline and the spirit of excellence that defines BBHS.',
    },
    {
        image: '/images/bbhs/events/reunion-01.jpg',
        eyebrow: 'BROTHERHOOD',
        title: 'Connected Across',
        highlight: 'Generations.',
        ending: 'Stronger Together.',
        description:
            'Bringing Old Boys together through reunions, class sets, events and lifelong relationships built on a shared heritage.',
    },
];

const currentSlide = ref(0);
let timer = null;

const nextSlide = () => {
    currentSlide.value = (currentSlide.value + 1) % slides.length;
};

const previousSlide = () => {
    currentSlide.value =
        (currentSlide.value - 1 + slides.length) % slides.length;
};

const goToSlide = (index) => {
    currentSlide.value = index;
};

const startTimer = () => {
    timer = setInterval(nextSlide, 6000);
};

const stopTimer = () => {
    if (timer) {
        clearInterval(timer);
        timer = null;
    }
};

onMounted(() => {
    startTimer();
});

onUnmounted(() => {
    stopTimer();
});
</script>

<template>
    <section
        class="relative min-h-[650px] overflow-hidden"
        @mouseenter="stopTimer"
        @mouseleave="startTimer"
    >

        <!-- =====================================================
             SLIDES
        ====================================================== -->

        <TransitionGroup name="hero-fade">

            <div
                v-for="(slide, index) in slides"
                v-show="currentSlide === index"
                :key="slide.image"
                class="absolute inset-0"
            >

                <!-- Background image -->

                <img
                    :src="slide.image"
                    :alt="slide.title"
                    class="h-full w-full object-cover object-center"
                />

                <!-- Main green overlay -->

                <div
                    class="absolute inset-0"
                    style="background:
                        linear-gradient(
                            90deg,
                            rgba(0, 53, 31, 0.96) 0%,
                            rgba(0, 53, 31, 0.84) 32%,
                            rgba(0, 53, 31, 0.55) 58%,
                            rgba(0, 53, 31, 0.12) 100%
                        );"
                ></div>

                <!-- Bottom fade -->

                <div
                    class="absolute inset-x-0 bottom-0 h-40"
                    style="background:
                        linear-gradient(
                            to top,
                            rgba(0, 53, 31, 0.55),
                            transparent
                        );"
                ></div>

            </div>

        </TransitionGroup>


        <!-- =====================================================
             CONTENT
        ====================================================== -->

        <div
            class="relative z-10 mx-auto flex min-h-[650px] max-w-[1440px] items-center px-6 py-20 lg:px-10"
        >

            <div class="max-w-3xl">

                <!-- Eyebrow -->

                <div
                    class="mb-5 text-sm font-bold uppercase tracking-[0.30em]"
                    style="color: #F1D36A;"
                >
                    {{ slides[currentSlide].eyebrow }}
                </div>


                <!-- Heading -->

                <h1
                    class="text-5xl font-extrabold leading-[1.02] tracking-tight text-white sm:text-6xl lg:text-7xl"
                >

                    {{ slides[currentSlide].title }}

                    <span
                        class="block"
                        style="color: #16A34A;"
                    >
                        {{ slides[currentSlide].highlight }}
                    </span>

                    <span class="block">
                        {{ slides[currentSlide].ending }}
                    </span>

                </h1>


                <!-- Description -->

                <p
                    class="mt-7 max-w-2xl text-lg leading-8 text-white/90 lg:text-xl"
                >
                    {{ slides[currentSlide].description }}
                </p>


                <!-- CTA -->

                <div class="mt-9 flex flex-wrap gap-4">

                    <a
                        href="/membership"
                        class="group inline-flex items-center gap-3 rounded-lg px-6 py-4 font-bold shadow-lg transition-all hover:-translate-y-0.5"
                        style="background-color: #D4A72C; color: #00351F;"
                    >
                        JOIN THE BROTHERHOOD

                        <ArrowRight
                            :size="19"
                            class="transition-transform group-hover:translate-x-1"
                        />
                    </a>


                    <button
                        type="button"
                        class="inline-flex items-center gap-3 rounded-lg border border-white/80 px-6 py-4 font-semibold text-white transition-all hover:bg-white hover:text-[#00351F]"
                    >

                        <PlayCircle :size="21" />

                        WATCH OUR STORY

                    </button>

                </div>


                <!-- Heritage -->

                <div class="mt-9 flex items-center gap-4">

                    <div
                        class="h-px w-14"
                        style="background-color: #D4A72C;"
                    ></div>

                    <span class="text-sm text-white/80">
                        Founded 1923 · Abeokuta · Nulli Secundus
                    </span>

                </div>

            </div>

        </div>


        <!-- =====================================================
             PREVIOUS / NEXT
        ====================================================== -->

        <button
            type="button"
            @click="previousSlide"
            class="absolute left-5 top-1/2 z-20 hidden -translate-y-1/2 rounded-full border border-white/40 bg-black/20 p-3 text-white backdrop-blur-sm transition hover:bg-white hover:text-[#00351F] md:block"
            aria-label="Previous slide"
        >
            <ChevronLeft :size="22" />
        </button>


        <button
            type="button"
            @click="nextSlide"
            class="absolute right-5 top-1/2 z-20 hidden -translate-y-1/2 rounded-full border border-white/40 bg-black/20 p-3 text-white backdrop-blur-sm transition hover:bg-white hover:text-[#00351F] md:block"
            aria-label="Next slide"
        >
            <ChevronRight :size="22" />
        </button>


        <!-- =====================================================
             SLIDE INDICATORS
        ====================================================== -->

        <div
            class="absolute bottom-7 left-1/2 z-20 flex -translate-x-1/2 items-center gap-2"
        >

            <button
                v-for="(_, index) in slides"
                :key="index"
                type="button"
                @click="goToSlide(index)"
                :aria-label="`Go to slide ${index + 1}`"
                class="h-2 rounded-full transition-all duration-300"
                :class="
                    currentSlide === index
                        ? 'w-8 bg-[#D4A72C]'
                        : 'w-2 bg-white/60 hover:bg-white'
                "
            ></button>

        </div>

    </section>
</template>

<style scoped>
.hero-fade-enter-active,
.hero-fade-leave-active {
    transition: opacity 0.8s ease;
}

.hero-fade-enter-from,
.hero-fade-leave-to {
    opacity: 0;
}
</style>