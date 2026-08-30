<script setup>
import PublicLayout from '../../Layouts/PublicLayout.vue';
import { Users, Newspaper, CalendarDays, FolderKanban, ArrowRight, Clock3 } from 'lucide-vue-next';

const props = defineProps({
    admin: Object,
    stats: { type: Object, default: () => ({}) },
    recentMembers: { type: Array, default: () => [] },
    upcomingEvents: { type: Array, default: () => [] },
    recentNews: { type: Array, default: () => [] },
});

const cards = [
    { title: 'Members', value: 'members', description: 'Registered Old Boys', icon: Users, href: '/admin/members' },
    { title: 'Pending', value: 'pending_members', description: 'Membership approvals', icon: Users, href: '/admin/members' },
    { title: 'News', value: 'published_news', description: 'Published articles', icon: Newspaper, href: '/admin/news' },
    { title: 'Events', value: 'upcoming_events', description: 'Upcoming events', icon: CalendarDays, href: '/admin/events' },
    { title: 'Projects', value: 'published_projects', description: 'Published projects', icon: FolderKanban, href: '/admin/projects' },
];
</script>

<template>
<PublicLayout>
<section class="bg-[#00351F]">
<div class="mx-auto max-w-[1400px] px-5 py-12 lg:px-8">
<p class="text-xs font-bold uppercase tracking-[0.25em] text-[#D4A72C]">Administration</p>
<h1 class="mt-2 text-3xl font-extrabold text-white sm:text-4xl">Admin Dashboard</h1>
<p class="mt-3 text-sm text-white/75">Welcome back, {{ admin?.name ?? 'Administrator' }}.</p>
<div class="mt-5 flex flex-wrap gap-2">
<a href="/dashboard" class="inline-flex items-center gap-2 rounded-lg bg-[#D4A72C] px-4 py-2.5 text-xs font-bold text-[#00351F]">Member Dashboard</a>
<a href="/" class="inline-flex items-center gap-2 rounded-lg border border-white/20 px-4 py-2.5 text-xs font-bold text-white hover:bg-white/10">View Public Site</a>
</div>
</div>
</section>

<section class="bg-[#F7F9F7] py-10 lg:py-14">
<div class="mx-auto max-w-[1400px] px-5 lg:px-8">
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
<a v-for="card in cards" :key="card.title" :href="card.href" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
<div class="flex items-center justify-between"><div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#EEF8F2] text-[#006B3C]"><component :is="card.icon" :size="20" /></div><ArrowRight :size="17" class="text-gray-300" /></div>
<div class="mt-5 text-3xl font-extrabold text-[#00351F]">{{ stats[card.value] ?? 0 }}</div>
<h2 class="mt-1 font-bold text-[#00351F]">{{ card.title }}</h2>
<p class="mt-1 text-xs text-gray-500">{{ card.description }}</p>
</a>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
<div class="rounded-2xl border border-gray-200 bg-white">
<div class="flex items-center justify-between border-b border-gray-100 p-5"><div><h2 class="font-extrabold text-[#00351F]">Recent Members</h2><p class="text-xs text-gray-500">Latest registrations</p></div><a href="/admin/members" class="text-xs font-bold text-[#006B3C]">VIEW ALL</a></div>
<div v-if="!recentMembers.length" class="p-6 text-sm text-gray-500">No members yet.</div>
<div v-else class="divide-y divide-gray-100">
<div v-for="member in recentMembers" :key="member.id" class="flex items-center justify-between p-4"><div><p class="text-sm font-bold text-[#00351F]">{{ member.name }}</p><p class="text-xs text-gray-500">{{ member.email }}</p></div><span class="rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-bold uppercase text-amber-700">{{ member.member_status }}</span></div>
</div>
</div>

<div class="rounded-2xl border border-gray-200 bg-white">
<div class="flex items-center justify-between border-b border-gray-100 p-5"><div><h2 class="font-extrabold text-[#00351F]">Upcoming Events</h2><p class="text-xs text-gray-500">Next association activities</p></div><a href="/admin/events" class="text-xs font-bold text-[#006B3C]">MANAGE</a></div>
<div v-if="!upcomingEvents.length" class="p-6 text-sm text-gray-500">No upcoming events.</div>
<div v-else class="divide-y divide-gray-100">
<div v-for="event in upcomingEvents" :key="event.id" class="p-4"><p class="text-sm font-bold text-[#00351F]">{{ event.title }}</p><p class="mt-1 flex items-center gap-2 text-xs text-gray-500"><Clock3 :size="13" /> {{ event.starts_at }} <span v-if="event.venue">· {{ event.venue }}</span></p></div>
</div>
</div>
</div>

<div class="mt-6 rounded-2xl border border-gray-200 bg-white">
<div class="flex items-center justify-between border-b border-gray-100 p-5"><div><h2 class="font-extrabold text-[#00351F]">Recent News</h2><p class="text-xs text-gray-500">Latest content</p></div><a href="/admin/news" class="text-xs font-bold text-[#006B3C]">MANAGE</a></div>
<div v-if="!recentNews.length" class="p-6 text-sm text-gray-500">No news articles.</div>
<div v-else class="grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-3">
<div v-for="item in recentNews" :key="item.id" class="rounded-xl bg-[#F7F9F7] p-4"><p class="font-bold text-[#00351F]">{{ item.title }}</p><p class="mt-2 text-xs text-gray-500">{{ item.published ? 'Published' : 'Draft' }} · {{ item.published_at || 'Not published' }}</p></div>
</div>
</div>
</div>
</section>
</PublicLayout>
</template>
