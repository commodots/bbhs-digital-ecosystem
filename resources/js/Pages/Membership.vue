<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { ArrowRight, CheckCircle2, Users, HeartHandshake, Globe2, CalendarDays } from 'lucide-vue-next';

const benefits = [
    { icon: Users, title: 'Brotherhood', text: 'Reconnect with Old Boys across generations, classes and locations.' },
    { icon: HeartHandshake, title: 'Give Back', text: 'Support students, school development and initiatives that strengthen BBHS.' },
    { icon: Globe2, title: 'Stay Connected', text: 'Remain part of a growing global BBHS community.' },
    { icon: CalendarDays, title: 'Events & Networking', text: 'Participate in reunions, professional gatherings and association events.' },
];
</script>

<template>
<PublicLayout>
    <section class="bg-[#00351F]">
        <div class="mx-auto flex min-h-[390px] max-w-[1440px] items-center px-5 py-16 lg:px-10">
            <div class="max-w-3xl">
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F1D36A]">Membership</p>
                <h1 class="mt-4 text-4xl font-extrabold text-white sm:text-5xl lg:text-6xl">
                    Join the Brotherhood.
                    <span class="block text-[#16A34A]">Stay Connected.</span>
                </h1>
                <p class="mt-5 max-w-2xl text-base leading-7 text-white/80 sm:text-lg">
                    Become an active part of the BBHS Old Boys Association and help preserve our heritage while building the future.
                </p>
            </div>
        </div>
    </section>

    <section class="bg-white py-12 lg:py-14">
        <div class="mx-auto max-w-[1200px] px-5 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[1fr_1.2fr] lg:items-center">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#006B3C]">Why Join?</p>
                    <h2 class="mt-2 text-3xl font-extrabold text-[#00351F] sm:text-4xl">More Than Membership.</h2>
                    <p class="mt-4 text-sm leading-7 text-gray-600">Membership connects you to a lifelong network of Old Boys committed to friendship, service, mentorship and the continued progress of BBHS.</p>
                    <a href="/contact" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white">Ask About Membership <ArrowRight :size="16" /></a>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div v-for="benefit in benefits" :key="benefit.title" class="rounded-2xl border border-gray-200 p-6">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#EEF8F2] text-[#006B3C]"><component :is="benefit.icon" :size="21" /></div>
                        <h3 class="mt-4 font-extrabold text-[#00351F]">{{ benefit.title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-gray-500">{{ benefit.text }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-[#F7F9F7] py-12 lg:py-14">
        <div class="mx-auto max-w-[900px] px-5 text-center">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#006B3C]">Become a Member</p>
            <h2 class="mt-2 text-3xl font-extrabold text-[#00351F]">Be Part of the Future</h2>
            <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-gray-500">Membership details, categories and registration will be connected to the BBHS member system during the dynamic development phase.</p>
            <div class="mx-auto mt-7 grid max-w-2xl gap-3 text-left sm:grid-cols-2">
                <div v-for="reason in ['Connect with fellow Old Boys','Participate in reunions and association activities','Support BBHS students and development projects','Access alumni news and resources','Build professional and lifelong relationships']" :key="reason" class="flex items-start gap-3 rounded-xl bg-white p-4">
                    <CheckCircle2 :size="18" class="mt-0.5 shrink-0 text-[#006B3C]" />
                    <span class="text-sm font-medium text-gray-700">{{ reason }}</span>
                </div>
            </div>
            <a href="/login" class="mt-8 inline-flex items-center gap-2 rounded-lg bg-[#D4A72C] px-6 py-3 font-extrabold text-[#00351F]">START YOUR MEMBERSHIP <ArrowRight :size="16" /></a>
        </div>
    </section>
</PublicLayout>
</template>
