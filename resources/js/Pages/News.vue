<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';

import {
    ArrowRight,
    CalendarDays,
    Clock3,
    PlayCircle,
} from 'lucide-vue-next';

const assets = {
    gate: '/images/bbhs/school/bbhs-school-gate.jpg',
    students01: '/images/bbhs/students/bbhs-students-01.jpg',
    students02: '/images/bbhs/students/bbhs-students-02.jpg',
    students03: '/images/bbhs/students/bbhs-students-03.jpg',
    reunion01: '/images/bbhs/events/reunion-01.jpg',
    reunion02: '/images/bbhs/events/reunion-02.jpg',
    seun: '/images/bbhs/alumni/oyewole-seun.jpg',
    akinlade: '/images/bbhs/alumni/akinkunmi-akinlade.jpg',
    okunowo: '/images/bbhs/alumni/otunba-wole-okunowo.jpg',
};

const latestNews = [
    {
        category: 'Association News',
        date: 'May 15, 2026',
        title: 'BBHS Old Boys Continue a Legacy of Connection and Impact',
        excerpt:
            'Old Boys from different generations continue to strengthen brotherhood, support the school and build meaningful connections.',
        image: assets.reunion01,
    },
    {
        category: 'School News',
        date: 'May 5, 2026',
        title: 'Supporting the Next Generation of BBHS Students',
        excerpt:
            'The association remains committed to supporting students and creating opportunities for the next generation.',
        image: assets.students01,
    },
    {
        category: 'Alumni News',
        date: 'April 28, 2026',
        title: 'Old Boys Strengthen Alumni Connections',
        excerpt:
            'Across class sets and locations, BBHS Old Boys continue to reconnect and create lasting relationships.',
        image: assets.students02,
    },
];

const stories = [
    {
        category: 'School',
        title: 'Investing in the Future of BBHS',
        image: assets.students03,
    },
    {
        category: 'Community',
        title: 'Connected Across Generations',
        image: assets.reunion02,
    },
    {
        category: 'Alumni',
        title: 'Celebrating Our Brotherhood',
        image: assets.okunowo,
    },
];
</script>

<template>
    <PublicLayout>

        <!-- =====================================================
             HERO
        ====================================================== -->
        <section class="relative overflow-hidden bg-[#00351F]">

            <div class="absolute inset-0">
                <img
                    :src="assets.gate"
                    alt="Baptist Boys' High School Abeokuta"
                    class="h-full w-full object-cover"
                />

                <div
                    class="absolute inset-0"
                    style="background: linear-gradient(
                        90deg,
                        rgba(0,53,31,.96) 0%,
                        rgba(0,53,31,.86) 40%,
                        rgba(0,53,31,.48) 72%,
                        rgba(0,53,31,.18) 100%
                    );"
                ></div>
            </div>

            <div
                class="relative mx-auto flex min-h-[390px] max-w-[1440px] items-center px-5 py-16 lg:px-10"
            >
                <div class="max-w-3xl">

                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F1D36A]">
                        BBHS News & Media
                    </p>

                    <h1 class="mt-4 text-4xl font-extrabold leading-tight text-white sm:text-5xl lg:text-6xl">
                        Stay Connected.
                        <span class="block text-[#16A34A]">
                            Stay Informed.
                        </span>
                    </h1>

                    <p class="mt-5 max-w-2xl text-base leading-7 text-white/85 sm:text-lg">
                        Discover the latest news, stories, achievements and
                        updates from the BBHS Old Boys Association and our
                        wider community.
                    </p>

                </div>
            </div>

        </section>


        <!-- =====================================================
             FEATURED STORY
        ====================================================== -->
        <section class="bg-white py-12 lg:py-14">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div class="mb-7 flex items-end justify-between gap-5">

                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#006B3C]">
                            Featured Story
                        </p>

                        <h2 class="mt-2 text-3xl font-extrabold text-[#00351F] sm:text-4xl">
                            The Brotherhood in Action
                        </h2>
                    </div>

                    <a
                        href="/news"
                        class="hidden items-center gap-2 text-sm font-bold text-[#006B3C] sm:inline-flex"
                    >
                        View all news
                        <ArrowRight :size="16" />
                    </a>

                </div>


                <article class="grid overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm lg:grid-cols-[1.25fr_.75fr]">

                    <div class="relative min-h-[300px]">

                        <img
                            :src="assets.reunion01"
                            alt="BBHS Old Boys gathering"
                            class="absolute inset-0 h-full w-full object-cover"
                        />

                        <div class="absolute left-5 top-5 rounded-full bg-[#D4A72C] px-4 py-2 text-[10px] font-extrabold uppercase tracking-wide text-[#00351F]">
                            Featured
                        </div>

                    </div>


                    <div class="flex items-center p-7 sm:p-9">

                        <div>

                            <div class="flex items-center gap-2 text-xs font-semibold text-[#006B3C]">
                                <CalendarDays :size="14" />
                                May 15, 2026
                            </div>

                            <h3 class="mt-4 text-2xl font-extrabold leading-tight text-[#00351F] sm:text-3xl">
                                BBHS Old Boys Continue a Legacy of Connection and Impact
                            </h3>

                            <p class="mt-4 text-sm leading-7 text-gray-600">
                                Old Boys from different generations continue
                                to come together to celebrate brotherhood,
                                strengthen relationships and support the
                                continued development of BBHS.
                            </p>

                            <a
                                href="#"
                                class="mt-6 inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#00532F]"
                            >
                                Read Story
                                <ArrowRight :size="16" />
                            </a>

                        </div>

                    </div>

                </article>

            </div>

        </section>


        <!-- =====================================================
             LATEST NEWS
        ====================================================== -->
        <section class="bg-[#F7F9F7] py-12 lg:py-14">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div class="mb-7 flex items-end justify-between">

                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#006B3C]">
                            Stay Connected
                        </p>

                        <h2 class="mt-2 text-3xl font-extrabold text-[#00351F]">
                            Latest News
                        </h2>
                    </div>

                    <a
                        href="#"
                        class="hidden items-center gap-2 text-sm font-bold text-[#006B3C] sm:inline-flex"
                    >
                        View all
                        <ArrowRight :size="16" />
                    </a>

                </div>


                <div class="grid gap-5 md:grid-cols-3">

                    <article
                        v-for="story in latestNews"
                        :key="story.title"
                        class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md"
                    >

                        <div class="h-[210px] overflow-hidden">

                            <img
                                :src="story.image"
                                :alt="story.title"
                                class="h-full w-full object-cover transition duration-500 hover:scale-105"
                            />

                        </div>

                        <div class="p-5">

                            <div class="flex items-center justify-between gap-3">

                                <span class="rounded-full bg-[#EEF8F2] px-3 py-1 text-[9px] font-bold uppercase tracking-wide text-[#006B3C]">
                                    {{ story.category }}
                                </span>

                                <span class="text-[10px] text-gray-400">
                                    {{ story.date }}
                                </span>

                            </div>

                            <h3 class="mt-4 text-lg font-extrabold leading-snug text-[#00351F]">
                                {{ story.title }}
                            </h3>

                            <p class="mt-3 text-sm leading-6 text-gray-500">
                                {{ story.excerpt }}
                            </p>

                            <a
                                href="#"
                                class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-[#006B3C]"
                            >
                                Read More
                                <ArrowRight :size="15" />
                            </a>

                        </div>

                    </article>

                </div>

            </div>

        </section>


        <!-- =====================================================
             MORE STORIES
        ====================================================== -->
        <section class="bg-white py-12 lg:py-14">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div class="mb-7">

                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#006B3C]">
                        More From BBHS
                    </p>

                    <h2 class="mt-2 text-3xl font-extrabold text-[#00351F]">
                        Stories & Highlights
                    </h2>

                </div>


                <div class="grid gap-5 md:grid-cols-3">

                    <article
                        v-for="story in stories"
                        :key="story.title"
                        class="group relative overflow-hidden rounded-2xl"
                    >

                        <div class="h-[250px]">

                            <img
                                :src="story.image"
                                :alt="story.title"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                            />

                        </div>

                        <div
                            class="absolute inset-x-0 bottom-0 p-5 pt-16"
                            style="background: linear-gradient(
                                to top,
                                rgba(0,53,31,.95),
                                rgba(0,53,31,.05)
                            );"
                        >

                            <span class="text-[9px] font-bold uppercase tracking-[0.18em] text-[#D4A72C]">
                                {{ story.category }}
                            </span>

                            <h3 class="mt-2 text-xl font-extrabold text-white">
                                {{ story.title }}
                            </h3>

                            <a
                                href="#"
                                class="mt-3 inline-flex items-center gap-2 text-xs font-bold text-white"
                            >
                                Explore
                                <ArrowRight :size="14" />
                            </a>

                        </div>

                    </article>

                </div>

            </div>

        </section>


        <!-- =====================================================
             MEDIA CTA
        ====================================================== -->
        <section class="bg-[#F7F9F7] py-12 lg:py-14">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div class="grid overflow-hidden rounded-2xl bg-[#00351F] lg:grid-cols-2">

                    <div class="flex items-center p-7 sm:p-10">

                        <div>

                            <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">
                                BBHS Media
                            </p>

                            <h2 class="mt-3 text-3xl font-extrabold text-white sm:text-4xl">
                                See the Brotherhood
                                <span class="block text-[#16A34A]">
                                    in Action.
                                </span>
                            </h2>

                            <p class="mt-4 max-w-xl text-sm leading-7 text-white/70">
                                Browse photographs, celebrations, school
                                activities, reunions and moments from across
                                the BBHS community.
                            </p>

                            <a
                                href="/gallery"
                                class="mt-6 inline-flex items-center gap-2 rounded-lg bg-[#D4A72C] px-5 py-3 text-sm font-extrabold text-[#00351F]"
                            >
                                VIEW GALLERY
                                <ArrowRight :size="16" />
                            </a>

                        </div>

                    </div>


                    <div class="relative min-h-[300px]">

                        <img
                            :src="assets.students03"
                            alt="BBHS students"
                            class="absolute inset-0 h-full w-full object-cover"
                        />

                        <div class="absolute inset-0 bg-[#00351F]/20"></div>

                        <div class="absolute bottom-5 left-5 flex items-center gap-3 rounded-xl bg-white/95 px-4 py-3">

                            <PlayCircle
                                :size="22"
                                class="text-[#006B3C]"
                            />

                            <div>
                                <p class="text-xs font-bold text-[#00351F]">
                                    BBHS Story
                                </p>

                                <p class="text-[10px] text-gray-500">
                                    Watch & explore
                                </p>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- =====================================================
             NEWSLETTER
        ====================================================== -->
        <section class="bg-white py-12">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div class="relative overflow-hidden rounded-2xl bg-[#00351F] px-6 py-10 sm:px-10">

                    <div class="absolute -right-20 -top-24 h-64 w-64 rounded-full border-[45px] border-white/5"></div>

                    <div class="absolute -bottom-24 -left-20 h-64 w-64 rounded-full border-[45px] border-[#D4A72C]/10"></div>

                    <div class="relative flex flex-col justify-between gap-7 lg:flex-row lg:items-center">

                        <div>

                            <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">
                                Stay Connected
                            </p>

                            <h2 class="mt-2 text-2xl font-extrabold text-white sm:text-3xl">
                                Keep Up With BBHS
                            </h2>

                            <p class="mt-2 text-sm text-white/70">
                                Get news, events and alumni updates delivered
                                to your inbox.
                            </p>

                        </div>


                        <form class="flex w-full max-w-xl gap-2">

                            <input
                                type="email"
                                placeholder="Enter your email address"
                                class="min-w-0 flex-1 rounded-lg border-0 px-4 py-3 text-sm outline-none"
                            />

                            <button
                                type="submit"
                                class="rounded-lg bg-[#D4A72C] px-5 py-3 text-sm font-extrabold text-[#00351F]"
                            >
                                SUBSCRIBE
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </section>

    </PublicLayout>
</template>