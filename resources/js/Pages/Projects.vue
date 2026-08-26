<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';

import {
    ArrowRight,
    CheckCircle2,
    GraduationCap,
    HeartHandshake,
    Lightbulb,
    Building2,
} from 'lucide-vue-next';

const images = {
    gate: '/images/bbhs/school/bbhs-school-gate.jpg',
    students01: '/images/bbhs/students/bbhs-students-01.jpg',
    students02: '/images/bbhs/students/bbhs-students-02.jpg',
    students03: '/images/bbhs/students/bbhs-students-03.jpg',
    reunion01: '/images/bbhs/events/reunion-01.jpg',
    reunion02: '/images/bbhs/events/reunion-02.jpg',
};

const projects = [
    {
        category: 'Education',
        title: 'Supporting the Future of BBHS',
        description:
            'Supporting initiatives that improve the learning environment and create better opportunities for present and future students.',
        image: images.gate,
        status: 'Active',
        progress: 75,
        raised: '₦45M',
        target: '₦60M',
    },
    {
        category: 'Students',
        title: 'Student Development & Mentorship',
        description:
            'Creating opportunities for students to develop confidence, leadership skills, career awareness and a strong sense of purpose.',
        image: images.students01,
        status: 'Ongoing',
        progress: 60,
        raised: '₦18M',
        target: '₦30M',
    },
    {
        category: 'Infrastructure',
        title: 'BBHS Infrastructure Support',
        description:
            'Supporting projects that improve facilities, learning spaces and the overall environment of Baptist Boys’ High School.',
        image: images.students02,
        status: 'Ongoing',
        progress: 50,
        raised: '₦25M',
        target: '₦50M',
    },
];
</script>

<template>
    <PublicLayout>

        <!-- HERO -->
        <section class="relative overflow-hidden bg-[#00351F]">

            <div class="absolute inset-0">
                <img
                    :src="images.gate"
                    alt="Baptist Boys' High School"
                    class="h-full w-full object-cover"
                />

                <div
                    class="absolute inset-0"
                    style="background: linear-gradient(
                        90deg,
                        rgba(0,53,31,.96) 0%,
                        rgba(0,53,31,.86) 40%,
                        rgba(0,53,31,.48) 75%,
                        rgba(0,53,31,.15) 100%
                    );"
                ></div>
            </div>

            <div class="relative mx-auto flex min-h-[390px] max-w-[1440px] items-center px-5 py-16 lg:px-10">

                <div class="max-w-3xl">

                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F1D36A]">
                        BBHS Projects & Initiatives
                    </p>

                    <h1 class="mt-4 text-4xl font-extrabold leading-tight text-white sm:text-5xl lg:text-6xl">
                        Building a Legacy.
                        <span class="block text-[#16A34A]">
                            Creating Impact.
                        </span>
                    </h1>

                    <p class="mt-5 max-w-2xl text-base leading-7 text-white/85 sm:text-lg">
                        Discover the projects and initiatives through which
                        BBHS Old Boys are supporting students, strengthening
                        the school and building a stronger future.
                    </p>

                </div>

            </div>
        </section>


        <!-- INTRO -->
        <section class="bg-white py-12 lg:py-14">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div class="grid gap-8 lg:grid-cols-[1fr_1.3fr] lg:items-end">

                    <div>

                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#006B3C]">
                            Our Impact
                        </p>

                        <h2 class="mt-2 text-3xl font-extrabold text-[#00351F] sm:text-4xl">
                            Turning Brotherhood
                            <span class="text-[#16A34A]">
                                Into Action.
                            </span>
                        </h2>

                    </div>

                    <p class="max-w-2xl text-sm leading-7 text-gray-600">
                        Our projects bring together the collective strength,
                        experience and resources of BBHS Old Boys to support
                        meaningful initiatives that benefit students, the
                        school and the wider community.
                    </p>

                </div>

            </div>

        </section>


        <!-- IMPACT AREAS -->
        <section class="bg-[#F7F9F7] py-12 lg:py-14">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">

                    <div class="rounded-2xl border border-gray-200 bg-white p-6">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#EEF8F2] text-[#006B3C]">
                            <GraduationCap :size="21" />
                        </div>

                        <h3 class="mt-5 font-extrabold text-[#00351F]">
                            Education
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-gray-500">
                            Supporting better learning opportunities and
                            educational development.
                        </p>

                    </div>


                    <div class="rounded-2xl border border-gray-200 bg-white p-6">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#FFF8E5] text-[#B18400]">
                            <Building2 :size="21" />
                        </div>

                        <h3 class="mt-5 font-extrabold text-[#00351F]">
                            Infrastructure
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-gray-500">
                            Improving facilities and creating better spaces
                            for students.
                        </p>

                    </div>


                    <div class="rounded-2xl border border-gray-200 bg-white p-6">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#EEF8F2] text-[#006B3C]">
                            <HeartHandshake :size="21" />
                        </div>

                        <h3 class="mt-5 font-extrabold text-[#00351F]">
                            Community
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-gray-500">
                            Supporting initiatives that strengthen our
                            community.
                        </p>

                    </div>


                    <div class="rounded-2xl border border-gray-200 bg-white p-6">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#FFF8E5] text-[#B18400]">
                            <Lightbulb :size="21" />
                        </div>

                        <h3 class="mt-5 font-extrabold text-[#00351F]">
                            Innovation
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-gray-500">
                            Creating opportunities for skills, technology
                            and future-focused development.
                        </p>

                    </div>

                </div>

            </div>

        </section>


        <!-- PROJECTS -->
        <section class="bg-white py-12 lg:py-14">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div class="mb-8">

                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#006B3C]">
                        Featured Projects
                    </p>

                    <h2 class="mt-2 text-3xl font-extrabold text-[#00351F]">
                        Projects Making a Difference
                    </h2>

                </div>


                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">

                    <article
                        v-for="project in projects"
                        :key="project.title"
                        class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md"
                    >

                        <div class="relative h-[220px] overflow-hidden">

                            <img
                                :src="project.image"
                                :alt="project.title"
                                class="h-full w-full object-cover transition duration-500 hover:scale-105"
                            />

                            <span class="absolute left-4 top-4 rounded-full bg-white/95 px-3 py-1 text-[9px] font-bold uppercase tracking-wide text-[#006B3C]">
                                {{ project.category }}
                            </span>

                        </div>


                        <div class="p-5">

                            <div class="flex items-center justify-between">

                                <h3 class="text-lg font-extrabold text-[#00351F]">
                                    {{ project.title }}
                                </h3>

                                <span class="text-[9px] font-bold uppercase text-[#006B3C]">
                                    {{ project.status }}
                                </span>

                            </div>

                            <p class="mt-3 text-sm leading-6 text-gray-500">
                                {{ project.description }}
                            </p>


                            <div class="mt-5">

                                <div class="mb-2 flex justify-between text-xs font-semibold">

                                    <span class="text-gray-500">
                                        {{ project.progress }}% funded
                                    </span>

                                    <span class="text-[#006B3C]">
                                        {{ project.raised }} raised
                                    </span>

                                </div>

                                <div class="h-2 overflow-hidden rounded-full bg-gray-100">

                                    <div
                                        class="h-full rounded-full bg-[#006B3C]"
                                        :style="{ width: project.progress + '%' }"
                                    ></div>

                                </div>

                                <div class="mt-2 text-right text-[10px] text-gray-400">
                                    Goal: {{ project.target }}
                                </div>

                            </div>


                            <a
                                href="#"
                                class="mt-5 inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-4 py-2.5 text-xs font-bold text-white"
                            >
                                View Project
                                <ArrowRight :size="14" />
                            </a>

                        </div>

                    </article>

                </div>

            </div>

        </section>


        <!-- CTA -->
        <section class="bg-[#F7F9F7] py-12">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div class="relative overflow-hidden rounded-2xl bg-[#00351F] px-7 py-10 text-center sm:px-10">

                    <h2 class="text-3xl font-extrabold text-white">
                        Be Part of the Impact.
                    </h2>

                    <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-white/70">
                        Your support can help us continue investing in
                        students, strengthening BBHS and preserving our
                        shared heritage.
                    </p>

                    <a
                        href="/membership"
                        class="mt-6 inline-flex items-center gap-2 rounded-lg bg-[#D4A72C] px-6 py-3 text-sm font-extrabold text-[#00351F]"
                    >
                        SUPPORT BBHS
                        <ArrowRight :size="16" />
                    </a>

                </div>

            </div>

        </section>

    </PublicLayout>
</template>