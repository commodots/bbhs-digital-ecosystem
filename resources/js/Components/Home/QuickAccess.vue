<script setup>
import {
    UsersRound,
    CalendarDays,
    HeartHandshake,
    GraduationCap,
    BriefcaseBusiness,
    Landmark,
    ArrowRight,
} from 'lucide-vue-next';

const items = [
    {
        title: 'Alumni Directory',
        description: 'Find classmates and connect with Old Boys worldwide.',
        icon: UsersRound,
        href: '/alumni',
    },
    {
        title: 'Events & Reunions',
        description: 'Join events, reunions and special gatherings.',
        icon: CalendarDays,
        href: '/events',
    },
    {
        title: 'Donate & Support',
        description: 'Support school projects, scholarships and initiatives.',
        icon: HeartHandshake,
        href: '/projects',
    },
    {
        title: 'Mentorship',
        description: 'Mentor students and help develop future leaders.',
        icon: GraduationCap,
        href: '/mentorship',
    },
    {
        title: 'Career Centre',
        description: 'Jobs, opportunities and business connections.',
        icon: BriefcaseBusiness,
        href: '/careers',
    },
    {
        title: 'Chapters',
        description: 'Connect with Old Boys in your location.',
        icon: Landmark,
        href: '/chapters',
    },
];
</script>

<template>
    <section class="bg-white px-5 py-5 lg:px-8 lg:py-6">
        <div class="mx-auto max-w-[1440px]">

            <div
                class="grid overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6"
            >

                <a
                    v-for="(item, index) in items"
                    :key="item.title"
                    :href="item.href"
                    class="group relative flex min-h-[128px] flex-col justify-between border-gray-100 p-5 transition hover:bg-[#f7f9f7] lg:min-h-[135px]"
                    :class="[
                        index < items.length - 1
                            ? 'border-b sm:border-r lg:border-b-0'
                            : '',
                    ]"
                >

                    <div>

                        <!-- Icon -->

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#eaf5ee] text-[#006b3c] transition group-hover:bg-[#006b3c] group-hover:text-white"
                        >
                            <component
                                :is="item.icon"
                                :size="18"
                                :stroke-width="1.8"
                            />
                        </div>


                        <!-- Title -->

                        <h3
                            class="mt-3 text-sm font-bold leading-tight text-[#00351F]"
                        >
                            {{ item.title }}
                        </h3>


                        <!-- Description -->

                        <p
                            class="mt-1.5 text-[11px] leading-5 text-gray-500"
                        >
                            {{ item.description }}
                        </p>

                    </div>


                    <!-- Arrow -->

                    <div
                        class="mt-2 flex justify-end text-[#006b3c] transition group-hover:translate-x-1"
                    >
                        <ArrowRight :size="15" />
                    </div>

                </a>

            </div>

        </div>
    </section>
</template>