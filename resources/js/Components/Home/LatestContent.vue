<script setup>
import {
    CalendarDays,
    MapPin,
    ArrowRight,
    Clock3,
    ExternalLink,
} from 'lucide-vue-next';

const news = [
    {
        date: 'May 15, 2026',
        category: 'FEATURED',
        title: 'BBHS Old Boys Continue a Legacy of Connection and Impact',
        excerpt:
            'Old Boys from different generations came together to celebrate brotherhood, strengthen relationships and support the school.',
        image: '/images/bbhs/events/reunion-01.jpg',
    },
    {
        date: 'May 5, 2026',
        category: 'NEWS',
        title: 'Supporting the Next Generation of BBHS Students',
        excerpt:
            'The association continues to explore initiatives that support students and the development of the school.',
        image: '/images/bbhs/students/bbhs-students-02.jpg',
    },
    {
        date: 'April 28, 2026',
        category: 'COMMUNITY',
        title: 'Old Boys Strengthen Alumni Connections',
        excerpt:
            'Class sets and alumni groups continue to build stronger connections across generations.',
        image: '/images/bbhs/events/reunion-02.jpg',
    },
];

const events = [
    {
        month: 'JUN',
        day: '15',
        title: 'Annual General Meeting',
        time: '10:00 AM – 2:00 PM',
        location: 'Virtual Event',
    },
    {
        month: 'JUL',
        day: '20',
        title: 'Global Alumni Reunion',
        time: 'Aug 20 – Aug 22, 2026',
        location: 'Lagos, Nigeria',
    },
    {
        month: 'AUG',
        day: '10',
        title: 'Career Mentorship Summit',
        time: '10:00 AM – 4:00 PM',
        location: 'BBHS School Auditorium',
    },
];
</script>

<template>
    <section class="bg-[#f7f9f7] py-10 sm:py-12 lg:py-14">

        <div class="mx-auto max-w-[1440px] px-5 lg:px-8">

            <!-- SECTION HEADER -->
            <div class="mb-5 flex items-end justify-between">

                <div>
                    <p
                        class="text-[10px] font-bold uppercase tracking-[0.22em] text-[#006b3c]"
                    >
                        Stay Connected
                    </p>

                    <h2
                        class="mt-1 text-2xl font-extrabold tracking-tight text-[#00351F] sm:text-3xl"
                    >
                        Latest News & Updates
                    </h2>
                </div>

                <a
                    href="/news"
                    class="hidden items-center gap-1.5 text-xs font-bold text-[#006b3c] transition hover:text-[#00351F] sm:flex"
                >
                    View all news
                    <ArrowRight :size="15" />
                </a>

            </div>


            <!-- =====================================================
                 CONTENT GRID
            ====================================================== -->

            <div class="grid gap-4 lg:grid-cols-12">


                <!-- =================================================
                     LATEST NEWS
                ================================================== -->

                <div
                    class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm lg:col-span-5"
                >

                    <div class="grid gap-4 xl:grid-cols-[1.2fr_0.8fr]">

                        <!-- Featured article -->

                        <article class="group overflow-hidden">

                            <div
                                class="relative h-[190px] overflow-hidden rounded-lg sm:h-[205px]"
                            >

                                <img
                                    :src="news[0].image"
                                    :alt="news[0].title"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                />

                                <div
                                    class="absolute left-3 top-3 rounded-md bg-[#006b3c] px-2 py-1 text-[9px] font-bold uppercase tracking-wide text-white"
                                >
                                    {{ news[0].category }}
                                </div>

                            </div>

                            <div class="pt-3">

                                <p class="text-[10px] text-gray-400">
                                    {{ news[0].date }}
                                </p>

                                <h3
                                    class="mt-1.5 text-base font-extrabold leading-5 text-[#00351F]"
                                >
                                    {{ news[0].title }}
                                </h3>

                                <p
                                    class="mt-1.5 line-clamp-3 text-xs leading-5 text-gray-500"
                                >
                                    {{ news[0].excerpt }}
                                </p>

                            </div>

                        </article>


                        <!-- Smaller news -->

                        <div class="space-y-3">

                            <article
                                v-for="item in news.slice(1)"
                                :key="item.title"
                                class="group flex gap-3"
                            >

                                <div
                                    class="h-[65px] w-[78px] shrink-0 overflow-hidden rounded-md"
                                >
                                    <img
                                        :src="item.image"
                                        :alt="item.title"
                                        class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                    />
                                </div>

                                <div class="min-w-0">

                                    <p class="text-[9px] text-gray-400">
                                        {{ item.date }}
                                    </p>

                                    <h4
                                        class="mt-1 line-clamp-3 text-xs font-bold leading-4 text-[#00351F]"
                                    >
                                        {{ item.title }}
                                    </h4>

                                </div>

                            </article>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     UPCOMING EVENTS
                ================================================== -->

                <div
                    class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm lg:col-span-4"
                >

                    <div class="mb-2 flex items-start justify-between">

                        <div>

                            <p
                                class="text-[10px] font-bold uppercase tracking-[0.18em] text-[#006b3c]"
                            >
                                Calendar
                            </p>

                            <h3
                                class="mt-1 text-xl font-extrabold text-[#00351F]"
                            >
                                Upcoming Events
                            </h3>

                        </div>

                        <a
                            href="/events"
                            class="mt-1 text-[11px] font-bold text-[#006b3c]"
                        >
                            View all
                        </a>

                    </div>


                    <div class="divide-y divide-gray-100">

                        <article
                            v-for="event in events"
                            :key="event.title"
                            class="flex gap-3 py-3 first:pt-2 last:pb-2"
                        >

                            <!-- Date -->

                            <div
                                class="flex h-[55px] w-[48px] shrink-0 flex-col items-center justify-center rounded-lg bg-[#006b3c] text-white"
                            >

                                <span
                                    class="text-[9px] font-bold tracking-wide text-[#D4A72C]"
                                >
                                    {{ event.month }}
                                </span>

                                <span
                                    class="text-xl font-extrabold leading-none"
                                >
                                    {{ event.day }}
                                </span>

                            </div>


                            <!-- Event information -->

                            <div class="min-w-0 flex-1">

                                <h4
                                    class="text-sm font-bold leading-5 text-[#00351F]"
                                >
                                    {{ event.title }}
                                </h4>

                                <div
                                    class="mt-1 space-y-0.5 text-[10px] text-gray-500"
                                >

                                    <div class="flex items-center gap-1.5">
                                        <Clock3 :size="11" />
                                        <span>{{ event.time }}</span>
                                    </div>

                                    <div class="flex items-center gap-1.5">
                                        <MapPin :size="11" />
                                        <span>{{ event.location }}</span>
                                    </div>

                                </div>

                            </div>


                            <!-- Register -->

                            <a
                                href="/events"
                                class="self-center rounded-md border border-gray-200 px-2 py-1.5 text-[9px] font-bold text-[#006b3c] transition hover:border-[#006b3c] hover:bg-[#f1f8f3]"
                            >
                                Register
                            </a>

                        </article>

                    </div>


                    <a
                        href="/events"
                        class="mt-2 flex items-center justify-center gap-1.5 border-t border-gray-100 pt-3 text-[9px] font-bold uppercase tracking-wide text-[#006b3c]"
                    >
                        <CalendarDays :size="12" />
                        Add to Calendar
                    </a>

                </div>


                <!-- =================================================
                     FEATURED PROJECT
                ================================================== -->

                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm lg:col-span-3"
                >

                    <!-- Project image -->

                    <div
                        class="relative h-[170px] overflow-hidden sm:h-[180px]"
                    >

                        <img
                            :src="'/images/bbhs/school/bbhs-school-gate.png'"
                            alt="Baptist Boys' High School gate"
                            class="h-full w-full object-cover object-center transition duration-500 hover:scale-105"
                        />
						

                        <div
                            class="absolute inset-0 bg-gradient-to-t from-[#00351F]/85 via-[#00351F]/10 to-transparent"
                        ></div>

                        <div
                            class="absolute bottom-3 left-3 rounded-md bg-[#00351F]/80 px-2 py-1 text-[9px] font-bold uppercase tracking-wider text-white"
                        >
                            Featured Project
                        </div>

                    </div>


                    <!-- Project content -->

                    <div class="p-4">

                        <h3
                            class="text-base font-extrabold leading-5 text-[#00351F]"
                        >
                            Supporting the Future of BBHS
                        </h3>

                        <p
                            class="mt-2 line-clamp-3 text-xs leading-5 text-gray-500"
                        >
                            Help create a stronger learning environment
                            and support initiatives that benefit current
                            and future generations.
                        </p>


                        <!-- Funding -->

                        <div class="mt-4">

                            <div
                                class="mb-1.5 flex justify-between text-[10px]"
                            >

                                <span class="font-semibold text-gray-600">
                                    75% Funded
                                </span>

                                <span class="font-bold text-[#006b3c]">
                                    ₦45M raised
                                </span>

                            </div>

                            <div
                                class="h-1.5 overflow-hidden rounded-full bg-gray-100"
                            >

                                <div
                                    class="h-full w-[75%] rounded-full bg-[#006b3c]"
                                ></div>

                            </div>

                        </div>


                        <a
                            href="/projects"
                            class="mt-4 flex items-center justify-center gap-2 rounded-lg bg-[#006b3c] px-3 py-2.5 text-xs font-bold text-white transition hover:bg-[#00532f]"
                        >
                            Support This Project

                            <ExternalLink :size="13" />

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </section>
</template>