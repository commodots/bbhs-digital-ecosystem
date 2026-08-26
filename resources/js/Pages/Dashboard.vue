<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { CalendarDays, UserCircle, Newspaper, HeartHandshake, ArrowRight } from 'lucide-vue-next';

const cards = [
    { icon: UserCircle, title: 'My Profile', text: 'Update your alumni information and contact details.', href: '/profile' },
    { icon: CalendarDays, title: 'My Events', text: 'View events, reunions and activities you are attending.', href: '/events' },
    { icon: Newspaper, title: 'Latest News', text: 'Catch up with the latest BBHS and alumni stories.', href: '/news' },
    { icon: HeartHandshake, title: 'Support a Project', text: 'Discover projects and initiatives you can support.', href: '/projects' },
];
</script>

<template>
<PublicLayout>
<section class="bg-[#00351F]">
    <div class="mx-auto max-w-[1200px] px-5 py-12 lg:px-8">
        <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">Member Portal</p>
        <h1 class="mt-2 text-3xl font-extrabold text-white sm:text-4xl">Welcome Back, Old Boy.</h1>
        <p class="mt-2 text-sm text-white/70">Your BBHS member dashboard.</p>
    </div>
</section>

<section class="bg-[#F7F9F7] py-12">
    <div class="mx-auto max-w-[1200px] px-5 lg:px-8">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <a v-for="card in cards" :key="card.title" :href="card.href" class="rounded-2xl border border-gray-200 bg-white p-6 transition hover:-translate-y-1 hover:shadow-md">
                <component :is="card.icon" :size="24" class="text-[#006B3C]" />
                <h2 class="mt-5 font-extrabold text-[#00351F]">{{ card.title }}</h2>
                <p class="mt-2 text-sm leading-6 text-gray-500">{{ card.text }}</p>
                <span class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-[#006B3C]">Open <ArrowRight :size="15" /></span>
            </a>
        </div>

        <div class="mt-8 rounded-2xl bg-white p-7 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#006B3C]">Your Membership</p>
            <h2 class="mt-2 text-2xl font-extrabold text-[#00351F]">Member information</h2>
            <div class="mt-6 grid gap-5 sm:grid-cols-3">
                <div><p class="text-xs text-gray-400">Status</p><p class="mt-1 font-bold text-[#006B3C]">Active</p></div>
                <div><p class="text-xs text-gray-400">Class / Set</p><p class="mt-1 font-bold text-[#00351F]">Not yet configured</p></div>
                <div><p class="text-xs text-gray-400">Member Since</p><p class="mt-1 font-bold text-[#00351F]">—</p></div>
            </div>
        </div>
    </div>
</section>
</PublicLayout>
</template>
