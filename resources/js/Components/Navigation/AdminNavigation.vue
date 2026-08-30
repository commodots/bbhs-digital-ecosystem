<script setup>
import { LayoutDashboard, Users, Newspaper, CalendarDays, FolderKanban, ArrowLeft, UserRound } from 'lucide-vue-next';
const items = [
    { label: 'Dashboard', href: '/admin/dashboard', icon: LayoutDashboard },
    { label: 'Members', href: '/admin/members', icon: Users },
    { label: 'News', href: '/admin/news', icon: Newspaper },
    { label: 'Events', href: '/admin/events', icon: CalendarDays },
    { label: 'Projects', href: '/admin/projects', icon: FolderKanban },
];
</script>
<template>
<div class="border-b border-[#DDE8E1] bg-[#F7F9F7]">
<div class="mx-auto flex max-w-[1440px] items-center gap-2 overflow-x-auto px-5 py-2.5 lg:px-8">
<span class="mr-2 shrink-0 text-[10px] font-extrabold uppercase tracking-[0.16em] text-gray-400">Admin</span>
<a v-for="item in items" :key="item.href" :href="item.href" class="inline-flex shrink-0 items-center gap-2 rounded-lg px-3 py-2 text-xs font-bold text-gray-600 transition hover:bg-white hover:text-[#006B3C]"><component :is="item.icon" :size="15" />{{ item.label }}</a>
<a href="/dashboard" class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-[#DDE8E1] bg-white px-3 py-2 text-xs font-bold text-[#006B3C] hover:bg-[#EEF8F2]"><UserRound :size="14" /> MEMBER DASHBOARD</a>
<a href="/" class="ml-auto inline-flex shrink-0 items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-bold text-gray-600 hover:text-[#006B3C]"><ArrowLeft :size="14" /> SITE</a>
</div></div>
</template>
