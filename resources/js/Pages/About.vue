<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import {
    HeartHandshake,
    Target,
    Eye,
    ShieldCheck,
    Users,
    GraduationCap,
    Landmark,
    ArrowRight,
} from 'lucide-vue-next';

const values = [
    {
        icon: HeartHandshake,
        title: 'Brotherhood',
        text: 'We foster lifelong relationships and a strong sense of belonging among BBHS Old Boys.',
    },
    {
        icon: ShieldCheck,
        title: 'Integrity',
        text: 'We uphold honesty, responsibility and the values that have shaped generations of BBHS Old Boys.',
    },
    {
        icon: GraduationCap,
        title: 'Excellence',
        text: 'We encourage excellence in education, professional development, leadership and service.',
    },
    {
        icon: Users,
        title: 'Service',
        text: 'We use our collective strength and resources to support our school, students and wider community.',
    },
];

const impact = [
    {
        value: '2,500+',
        label: 'Alumni Worldwide',
        icon: Users,
    },
    {
        value: '25+',
        label: 'Countries Represented',
        icon: Landmark,
    },
    {
        value: '150+',
        label: 'Events & Reunions',
        icon: HeartHandshake,
    },
    {
        value: '500+',
        label: 'Students Impacted',
        icon: GraduationCap,
    },
];
</script>

<template>
    <PublicLayout>

        <!-- =====================================================
             PAGE HERO
        ====================================================== -->

        <section class="relative overflow-hidden bg-[#00351F]">

            <div class="absolute inset-0">
                <img
                    :src="'/images/bbhs/school/bbhs-school-gate.png'"
                    alt="Baptist Boys' High School Abeokuta"
                    class="h-full w-full object-cover"
                />

                <div
                    class="absolute inset-0"
                    style="background:
                        linear-gradient(
                            90deg,
                            rgba(0,53,31,.96) 0%,
                            rgba(0,53,31,.84) 42%,
                            rgba(0,53,31,.45) 75%,
                            rgba(0,53,31,.25) 100%
                        );"
                ></div>
            </div>

            <div
                class="relative mx-auto flex min-h-[420px] max-w-[1440px] items-center px-5 py-20 lg:px-10"
            >

                <div class="max-w-3xl">

                    <p
                        class="mb-5 text-xs font-bold uppercase tracking-[0.28em] text-[#F1D36A]"
                    >
                        About BBHS Old Boys Association
                    </p>

                    <h1
                        class="text-4xl font-extrabold leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl"
                    >
                        United by Heritage.
                        <span class="block text-[#16A34A]">
                            Connected for Life.
                        </span>
                    </h1>

                    <p
                        class="mt-6 max-w-2xl text-base leading-7 text-white/85 sm:text-lg"
                    >
                        A global community of Baptist Boys' High School Old Boys
                        committed to preserving our heritage, strengthening
                        brotherhood and creating a lasting impact.
                    </p>

                </div>

            </div>

        </section>


        <!-- =====================================================
             INTRODUCTION
        ====================================================== -->

        <section class="bg-white py-14 lg:py-20">

            <div
                class="mx-auto grid max-w-[1200px] gap-10 px-5 lg:grid-cols-2 lg:items-center lg:px-8"
            >

                <div>

                    <p
                        class="text-xs font-bold uppercase tracking-[0.22em] text-[#006b3c]"
                    >
                        Who We Are
                    </p>

                    <h2
                        class="mt-2 text-3xl font-extrabold leading-tight text-[#00351F] sm:text-4xl"
                    >
                        One Brotherhood.
                        <span class="block text-[#009C3F]">
                            Many Generations.
                        </span>
                    </h2>

                    <div class="mt-5 space-y-4 text-sm leading-7 text-gray-600">

                        <p>
                            The Baptist Boys' High School Old Boys Association
                            brings together former students of Baptist Boys'
                            High School, Abeokuta, across generations, class
                            sets, professions and locations.
                        </p>

                        <p>
                            Our association provides a platform for Old Boys
                            to reconnect, support one another, celebrate our
                            shared heritage and contribute meaningfully to the
                            continued development of the school.
                        </p>

                        <p>
                            From reunions and professional connections to
                            mentorship, student support and development
                            projects, the BBHS brotherhood continues to make
                            a difference.
                        </p>

                    </div>

                </div>


                <div class="relative">

                    <div class="overflow-hidden rounded-2xl">
                        <img
                            :src="'/images/bbhs/students/bbhs-students-02.jpg'"
                            alt="BBHS students"
                            class="h-[360px] w-full object-cover"
                        />
                    </div>

                    <div
                        class="absolute -bottom-5 -left-3 hidden rounded-xl bg-[#D4A72C] px-6 py-4 shadow-lg sm:block"
                    >
                        <p
                            class="text-2xl font-extrabold text-[#00351F]"
                        >
                            1923
                        </p>

                        <p
                            class="text-[10px] font-bold uppercase tracking-wider text-[#00351F]"
                        >
                            BBHS Heritage
                        </p>
                    </div>

                </div>

            </div>

        </section>


        <!-- =====================================================
             MISSION / VISION
        ====================================================== -->

        <section class="bg-[#f7f9f7] py-14 lg:py-18">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div class="mb-9 text-center">

                    <p
                        class="text-xs font-bold uppercase tracking-[0.22em] text-[#006b3c]"
                    >
                        Our Purpose
                    </p>

                    <h2
                        class="mt-2 text-3xl font-extrabold text-[#00351F]"
                    >
                        What Drives Us
                    </h2>

                </div>


                <div class="grid gap-5 md:grid-cols-2">

                    <!-- Mission -->

                    <div
                        class="rounded-2xl bg-[#00351F] p-7 text-white shadow-sm"
                    >

                        <div
                            class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-white/10"
                        >
                            <Target
                                :size="24"
                                class="text-[#D4A72C]"
                            />
                        </div>

                        <h3 class="text-2xl font-extrabold">
                            Our Mission
                        </h3>

                        <p
                            class="mt-4 text-sm leading-7 text-white/75"
                        >
                            To build a connected and purposeful community of
                            BBHS Old Boys that preserves our shared heritage,
                            supports the school and empowers present and future
                            generations.
                        </p>

                    </div>


                    <!-- Vision -->

                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-7 shadow-sm"
                    >

                        <div
                            class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-[#eef8f2]"
                        >
                            <Eye
                                :size="24"
                                class="text-[#006b3c]"
                            />
                        </div>

                        <h3
                            class="text-2xl font-extrabold text-[#00351F]"
                        >
                            Our Vision
                        </h3>

                        <p
                            class="mt-4 text-sm leading-7 text-gray-600"
                        >
                            A united, active and globally connected BBHS
                            brotherhood making a meaningful and sustainable
                            contribution to education, community and society.
                        </p>

                    </div>

                </div>

            </div>

        </section>


        <!-- =====================================================
             CORE VALUES
        ====================================================== -->

        <section class="bg-white py-14 lg:py-18">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div
                    class="mb-9 flex flex-col justify-between gap-3 sm:flex-row sm:items-end"
                >

                    <div>

                        <p
                            class="text-xs font-bold uppercase tracking-[0.22em] text-[#006b3c]"
                        >
                            What We Stand For
                        </p>

                        <h2
                            class="mt-2 text-3xl font-extrabold text-[#00351F]"
                        >
                            Our Core Values
                        </h2>

                    </div>

                    <p class="max-w-md text-sm leading-6 text-gray-500">
                        The principles that guide our relationships,
                        activities and contribution to the BBHS community.
                    </p>

                </div>


                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

                    <article
                        v-for="value in values"
                        :key="value.title"
                        class="rounded-xl border border-gray-200 bg-white p-5 transition hover:-translate-y-1 hover:shadow-lg"
                    >

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-lg bg-[#eef8f2]"
                        >
                            <component
                                :is="value.icon"
                                :size="21"
                                class="text-[#006b3c]"
                            />
                        </div>

                        <h3
                            class="mt-4 text-lg font-extrabold text-[#00351F]"
                        >
                            {{ value.title }}
                        </h3>

                        <p
                            class="mt-2 text-xs leading-6 text-gray-500"
                        >
                            {{ value.text }}
                        </p>

                    </article>

                </div>

            </div>

        </section>


        <!-- =====================================================
             IMPACT
        ====================================================== -->

        <section class="bg-[#00351F] py-12 lg:py-14">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div
                    class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"
                >

                    <div>

                        <p
                            class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]"
                        >
                            Our Impact
                        </p>

                        <h2
                            class="mt-2 text-3xl font-extrabold text-white"
                        >
                            Building a Legacy That Lasts.
                        </h2>

                    </div>

                    <p
                        class="max-w-md text-sm leading-6 text-white/60"
                    >
                        Our collective strength allows us to connect
                        generations and support initiatives that matter.
                    </p>

                </div>


                <div
                    class="grid grid-cols-2 divide-x divide-white/10 lg:grid-cols-4"
                >

                    <div
                        v-for="item in impact"
                        :key="item.label"
                        class="px-4 py-4 first:pl-0 last:pr-0"
                    >

                        <component
                            :is="item.icon"
                            :size="25"
                            class="text-[#D4A72C]"
                        />

                        <p
                            class="mt-3 text-2xl font-extrabold text-white"
                        >
                            {{ item.value }}
                        </p>

                        <p
                            class="mt-1 text-[10px] font-semibold uppercase tracking-wide text-white/60"
                        >
                            {{ item.label }}
                        </p>

                    </div>

                </div>

            </div>

        </section>


        <!-- =====================================================
             BROTHERHOOD IMAGE / STORY
        ====================================================== -->

        <section class="bg-[#f7f9f7] py-14 lg:py-20">

            <div
                class="mx-auto grid max-w-[1200px] gap-8 px-5 lg:grid-cols-2 lg:items-center lg:px-8"
            >

                <div
                    class="order-2 overflow-hidden rounded-2xl lg:order-1"
                >

                    <img
                        :src="'/images/bbhs/events/reunion-02.jpg'"
                        alt="BBHS Old Boys gathering"
                        class="h-[380px] w-full object-cover"
                    />

                </div>


                <div class="order-1 lg:order-2">

                    <p
                        class="text-xs font-bold uppercase tracking-[0.22em] text-[#006b3c]"
                    >
                        The Brotherhood
                    </p>

                    <h2
                        class="mt-2 text-3xl font-extrabold leading-tight text-[#00351F] sm:text-4xl"
                    >
                        Connected Across
                        <span class="block text-[#009C3F]">
                            Generations.
                        </span>
                    </h2>

                    <p
                        class="mt-5 text-sm leading-7 text-gray-600"
                    >
                        BBHS Old Boys are connected by more than the years
                        spent within the walls of our school. We share a
                        heritage, a culture and a responsibility to leave a
                        stronger legacy for those coming after us.
                    </p>

                    <p
                        class="mt-4 text-sm leading-7 text-gray-600"
                    >
                        Through class reunions, mentorship, professional
                        networks, school projects and community initiatives,
                        the association creates opportunities for Old Boys to
                        reconnect and make an impact.
                    </p>

                    <a
                        href="/alumni"
                        class="mt-7 inline-flex items-center gap-2 rounded-lg bg-[#006b3c] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#00532f]"
                    >
                        Meet Our Alumni
                        <ArrowRight :size="17" />
                    </a>

                </div>

            </div>

        </section>


        <!-- =====================================================
             CTA
        ====================================================== -->

        <section class="bg-white py-14 lg:py-16">

            <div
                class="mx-auto max-w-[1200px] px-5 lg:px-8"
            >

                <div
                    class="relative overflow-hidden rounded-2xl bg-[#00351F] px-6 py-12 text-center sm:px-10"
                >

                    <!-- decorative circles -->

                    <div
                        class="absolute -right-20 -top-20 h-64 w-64 rounded-full border-[45px] border-white/5"
                    ></div>

                    <div
                        class="absolute -bottom-24 -left-20 h-64 w-64 rounded-full border-[45px] border-[#D4A72C]/10"
                    ></div>


                    <div class="relative">

                        <p
                            class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]"
                        >
                            Join the Brotherhood
                        </p>

                        <h2
                            class="mt-3 text-3xl font-extrabold text-white sm:text-4xl"
                        >
                            Be Part of the BBHS Legacy.
                        </h2>

                        <p
                            class="mx-auto mt-4 max-w-2xl text-sm leading-6 text-white/70"
                        >
                            Connect with Old Boys around the world, participate
                            in events, support meaningful initiatives and help
                            build the future of Baptist Boys' High School.
                        </p>

                        <a
                            href="/membership"
                            class="mt-7 inline-flex items-center gap-2 rounded-lg bg-[#D4A72C] px-6 py-3.5 text-sm font-extrabold text-[#00351F] transition hover:bg-[#e4ba42]"
                        >
                            JOIN BBHS
                            <ArrowRight :size="17" />
                        </a>

                    </div>

                </div>

            </div>

        </section>

    </PublicLayout>
</template>