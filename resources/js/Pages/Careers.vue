<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { BriefcaseBusiness, Network, Lightbulb, ArrowRight, Mail } from 'lucide-vue-next';

const areas = [
    { icon: BriefcaseBusiness, title: 'Career opportunities', text: 'Share job openings, internships, professional opportunities and career resources with fellow Old Boys.' },
    { icon: Network, title: 'Professional connections', text: 'Build relationships across industries, professions, graduation sets and locations.' },
    { icon: Lightbulb, title: 'Business and enterprise', text: 'Discover potential collaborations, business referrals and opportunities to support one another.' },
];
</script>

<template>
    <PublicLayout>
        <section class="bg-[#00351F]">
            <div class="mx-auto max-w-[1200px] px-5 py-16 lg:px-8 lg:py-20">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-[#F1D36A]">Careers • Connections • Opportunity</p>
                <h1 class="mt-4 max-w-3xl text-4xl font-extrabold leading-tight text-white sm:text-5xl">BBHS Careers & Opportunities</h1>
                <p class="mt-5 max-w-2xl text-base leading-7 text-white/80 sm:text-lg">
                    A platform for professional networking, career development and sharing opportunities across the BBHS Old Boys community.
                </p>
                <a href="/contact" class="mt-8 inline-flex items-center gap-2 rounded-lg bg-[#D4A72C] px-5 py-3 text-sm font-bold text-[#00351F] hover:bg-[#E5B93F]">
                    Share an opportunity <ArrowRight :size="17" />
                </a>
            </div>
        </section>
        <section class="bg-white py-14 lg:py-16">
            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">
                <div class="max-w-3xl">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#006B3C]">Professional network</p>
                    <h2 class="mt-2 text-3xl font-extrabold text-[#00351F]">Growing together through opportunity</h2>
                    <p class="mt-4 text-sm leading-7 text-gray-600">
                        The BBHS Careers Centre is intended to help Old Boys share career opportunities, professional knowledge and business connections. Members and organisations are invited to contact the Association with opportunities and ideas for the centre.
                    </p>
                </div>
                <div class="mt-9 grid gap-5 md:grid-cols-3">
                    <article v-for="item in areas" :key="item.title" class="rounded-2xl border border-gray-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#EEF8F2] text-[#006B3C]">
                            <component :is="item.icon" :size="23" />
                        </div>
                        <h3 class="mt-5 text-lg font-extrabold text-[#00351F]">{{ item.title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-gray-600">{{ item.text }}</p>
                    </article>
                </div>
                <div class="mt-10 rounded-2xl border border-[#DCEBE2] bg-[#F7FAF8] p-7 sm:p-9">
                    <h2 class="text-2xl font-extrabold text-[#00351F]">Have a vacancy, opportunity or professional service to share?</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-gray-600">Send the details to the Association for review and appropriate circulation. Please include the role or opportunity, organisation, location, requirements and closing date where applicable.</p>
                    <a href="/contact" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white hover:bg-[#00552F]">
                        <Mail :size="17" /> Contact the Association
                    </a>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
