<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';

import {
    CalendarDays,
    MapPin,
    Clock3,
    ArrowRight,
    Users,
    Plus,
} from 'lucide-vue-next';

const assets = {
    reunion01: '/images/bbhs/events/reunion-01.jpg',
    reunion02: '/images/bbhs/events/reunion-02.jpg',
    students01: '/images/bbhs/students/bbhs-students-01.jpg',
};


const upcomingEvents = [
    {
        day: '15',
        month: 'JUN',
        title: 'BBHS Old Boys Annual General Meeting',
        type: 'Association Event',
        location: 'Abeokuta, Ogun State',
        time: '10:00 AM',
        image: assets.reunion01,
    },
    {
        day: '22',
        month: 'JUL',
        title: 'Global BBHS Alumni Reunion',
        type: 'Reunion',
        location: 'Lagos, Nigeria',
        time: '4:00 PM',
        image: assets.reunion02,
    },
    {
        day: '17',
        month: 'AUG',
        title: 'BBHS Career & Mentorship Summit',
        type: 'Mentorship',
        location: 'BBHS Abeokuta',
        time: '11:00 AM',
        image: assets.students01,
    },
];

const pastEvents = [
    {
        title: 'Class of 1995 Reunion',
        date: 'May 2026',
        location: 'Lagos',
		image: assets.reunion01,
    },
    {
        title: 'BBHS Development Dinner',
        date: 'March 2026',
        location: 'Abeokuta',
        image: assets.reunion02,
    },
    {
        title: 'Old Boys Networking Evening',
        date: 'January 2026',
        location: 'Lagos',
        image: assets.reunion01,
    },
];
</script>

<template>
    <PublicLayout>

        <!-- HERO -->
        <section class="relative overflow-hidden bg-[#00351F]">

            <div class="absolute inset-0">
                <img
                    :src="assets.reunion02"
                    alt="BBHS Old Boys reunion"
                    class="h-full w-full object-cover"
                />

                <div
                    class="absolute inset-0"
                    style="background: linear-gradient(
                        90deg,
                        rgba(0,53,31,.97) 0%,
                        rgba(0,53,31,.87) 42%,
                        rgba(0,53,31,.52) 75%,
                        rgba(0,53,31,.25) 100%
                    );"
                ></div>
            </div>

            <div
                class="relative mx-auto flex min-h-[400px] max-w-[1440px] items-center px-5 py-16 lg:px-10"
            >
                <div class="max-w-3xl">

                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F1D36A]">
                        BBHS Events & Reunions
                    </p>

                    <h1 class="mt-4 text-4xl font-extrabold leading-tight text-white sm:text-5xl lg:text-6xl">
                        Where Brotherhood
                        <span class="block text-[#16A34A]">
                            Comes Together.
                        </span>
                    </h1>

                    <p class="mt-5 max-w-2xl text-base leading-7 text-white/85 sm:text-lg">
                        From class reunions and association meetings to
                        professional gatherings and mentorship programmes,
                        discover opportunities to connect with the BBHS
                        brotherhood.
                    </p>

                </div>
            </div>

        </section>


        <!-- UPCOMING EVENTS -->
        <section class="bg-white py-14 lg:py-16">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">

                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#006B3C]">
                            What's Happening
                        </p>

                        <h2 class="mt-2 text-3xl font-extrabold text-[#00351F] sm:text-4xl">
                            Upcoming Events
                        </h2>

                        <p class="mt-2 max-w-xl text-sm leading-6 text-gray-500">
                            Stay connected and take part in upcoming BBHS
                            gatherings and activities.
                        </p>
                    </div>

                    <a
                        href="#past-events"
                        class="inline-flex items-center gap-2 text-sm font-bold text-[#006B3C]"
                    >
                        View past events
                        <ArrowRight :size="16" />
                    </a>

                </div>


                <div class="grid gap-5 lg:grid-cols-3">

                    <article
                        v-for="event in upcomingEvents"
                        :key="event.title"
                        class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
                    >

                        <div class="relative h-[220px] overflow-hidden">

                            <img
                                :src="event.image"
                                :alt="event.title"
                                class="h-full w-full object-cover transition duration-500 hover:scale-105"
                            />

                            <div class="absolute left-4 top-4 flex h-16 w-16 flex-col items-center justify-center rounded-xl bg-white shadow-lg">

                                <span class="text-xl font-extrabold leading-none text-[#00351F]">
                                    {{ event.day }}
                                </span>

                                <span class="mt-1 text-[9px] font-bold tracking-wider text-[#006B3C]">
                                    {{ event.month }}
                                </span>

                            </div>

                        </div>


                        <div class="p-5">

                            <span class="rounded-full bg-[#EEF8F2] px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-[#006B3C]">
                                {{ event.type }}
                            </span>

                            <h3 class="mt-4 text-xl font-extrabold leading-snug text-[#00351F]">
                                {{ event.title }}
                            </h3>

                            <div class="mt-4 space-y-2">

                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <MapPin :size="15" class="text-[#006B3C]" />
                                    {{ event.location }}
                                </div>

                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <Clock3 :size="15" class="text-[#006B3C]" />
                                    {{ event.time }}
                                </div>

                            </div>

                            <a
                                href="#"
                                class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-[#006B3C]"
                            >
                                View Event
                                <ArrowRight :size="16" />
                            </a>

                        </div>

                    </article>

                </div>

            </div>

        </section>


        <!-- FEATURED REUNION -->
        <section class="bg-[#F7F9F7] py-14 lg:py-16">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div class="grid overflow-hidden rounded-2xl bg-[#00351F] lg:grid-cols-2">

                    <div class="min-h-[360px]">

                        <img
                            :src="assets.reunion02"
                            alt="BBHS Old Boys reunion"
                            class="h-full w-full object-cover"
                        />

                    </div>

                    <div class="flex items-center p-7 sm:p-10">

                        <div>

                            <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">
                                Featured Reunion
                            </p>

                            <h2 class="mt-3 text-3xl font-extrabold leading-tight text-white sm:text-4xl">
                                Reconnect.
                                <span class="block text-[#16A34A]">
                                    Remember.
                                </span>
                                Rebuild.
                            </h2>

                            <p class="mt-5 text-sm leading-7 text-white/70">
                                Reunions give Old Boys an opportunity to return
                                to their roots, reconnect with classmates and
                                celebrate the relationships formed at BBHS.
                            </p>

                            <div class="mt-6 flex flex-wrap gap-3">

                                <a
                                    href="#"
                                    class="inline-flex items-center gap-2 rounded-lg bg-[#D4A72C] px-5 py-3 text-sm font-extrabold text-[#00351F] transition hover:bg-[#E4BA42]"
                                >
                                    View Reunion
                                    <ArrowRight :size="17" />
                                </a>

                                <a
                                    href="#"
                                    class="inline-flex items-center gap-2 rounded-lg border border-white/20 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10"
                                >
                                    Register Interest
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- REUNIONS -->
        <section class="bg-white py-14 lg:py-16">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div class="grid gap-8 lg:grid-cols-[.8fr_1.2fr] lg:items-center">

                    <div>

                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#006B3C]">
                            Class Reunions
                        </p>

                        <h2 class="mt-2 text-3xl font-extrabold text-[#00351F] sm:text-4xl">
                            Your Class.
                            <span class="block text-[#009C3F]">
                                Your Story.
                            </span>
                        </h2>

                        <p class="mt-4 text-sm leading-7 text-gray-600">
                            Class reunions keep the memories alive and give
                            classmates an opportunity to reconnect, celebrate
                            milestones and support one another.
                        </p>

                        <a
                            href="/contact"
                            class="mt-6 inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#00532F]"
                        >
                            Plan a Reunion
                            <Plus :size="17" />
                        </a>

                    </div>


                    <div class="grid gap-4 sm:grid-cols-2">

                        <div class="rounded-2xl border border-gray-200 bg-[#F7F9F7] p-6">

                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white">
                                <Users :size="21" class="text-[#006B3C]" />
                            </div>

                            <h3 class="mt-4 text-lg font-extrabold text-[#00351F]">
                                Organise Your Class
                            </h3>

                            <p class="mt-2 text-sm leading-6 text-gray-500">
                                Bring your classmates together and create a
                                reunion event through the association.
                            </p>

                        </div>


                        <div class="rounded-2xl border border-gray-200 bg-[#F7F9F7] p-6">

                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white">
                                <CalendarDays :size="21" class="text-[#D4A72C]" />
                            </div>

                            <h3 class="mt-4 text-lg font-extrabold text-[#00351F]">
                                Stay Connected
                            </h3>

                            <p class="mt-2 text-sm leading-6 text-gray-500">
                                Keep your class information current and
                                receive updates about upcoming activities.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- PAST EVENTS -->
        <section
            id="past-events"
            class="bg-[#F7F9F7] py-14 lg:py-16"
        >

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div class="mb-8">

                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#006B3C]">
                        Our Memories
                    </p>

                    <h2 class="mt-2 text-3xl font-extrabold text-[#00351F]">
                        Past Events
                    </h2>

                </div>


                <div class="grid gap-5 md:grid-cols-3">

                    <article
                        v-for="event in pastEvents"
                        :key="event.title"
                        class="overflow-hidden rounded-xl border border-gray-200 bg-white"
                    >

                        <div class="h-[190px] overflow-hidden">

                            <img
                                :src="event.image"
                                :alt="event.title"
                                class="h-full w-full object-cover transition duration-500 hover:scale-105"
                            />

                        </div>

                        <div class="p-5">

                            <p class="text-xs font-semibold text-[#006B3C]">
                                {{ event.date }}
                            </p>

                            <h3 class="mt-2 text-lg font-extrabold text-[#00351F]">
                                {{ event.title }}
                            </h3>

                            <div class="mt-3 flex items-center gap-2 text-xs text-gray-500">
                                <MapPin :size="14" class="text-[#006B3C]" />
                                {{ event.location }}
                            </div>

                        </div>

                    </article>

                </div>

            </div>

        </section>


        <!-- CTA -->
        <section class="bg-white py-14 lg:py-16">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div class="relative overflow-hidden rounded-2xl bg-[#00351F] px-6 py-12 text-center sm:px-10">

                    <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full border-[45px] border-white/5"></div>

                    <div class="absolute -bottom-24 -left-20 h-64 w-64 rounded-full border-[45px] border-[#D4A72C]/10"></div>

                    <div class="relative">

                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">
                            Bring the Brotherhood Together
                        </p>

                        <h2 class="mt-3 text-3xl font-extrabold text-white sm:text-4xl">
                            Planning a Reunion?
                        </h2>

                        <p class="mx-auto mt-4 max-w-2xl text-sm leading-6 text-white/70">
                            Let us know about your class reunion, alumni
                            gathering or BBHS-related event.
                        </p>

                        <a
                            href="/contact"
                            class="mt-7 inline-flex items-center gap-2 rounded-lg bg-[#D4A72C] px-6 py-3.5 text-sm font-extrabold text-[#00351F] transition hover:bg-[#E4BA42]"
                        >
                            GET IN TOUCH
                            <ArrowRight :size="17" />
                        </a>

                    </div>

                </div>

            </div>

        </section>

    </PublicLayout>
</template>