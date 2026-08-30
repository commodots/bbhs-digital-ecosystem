<script setup>
import { CalendarDays, MapPin, ArrowRight, Clock3, ExternalLink } from 'lucide-vue-next';

const props = defineProps({
    news: { type: Array, default: () => [] },
    events: { type: Array, default: () => [] },
    projects: { type: Array, default: () => [] },
});
</script>

<template>
<section class="bg-[#f7f9f7] py-10 sm:py-12 lg:py-14">
<div class="mx-auto max-w-[1440px] px-5 lg:px-8">
    <div class="mb-5 flex items-end justify-between">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-[#006b3c]">Stay Connected</p>
            <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-[#00351F] sm:text-3xl">Latest News & Updates</h2>
        </div>
        <a href="/news" class="hidden items-center gap-1.5 text-xs font-bold text-[#006b3c] sm:flex">View all news <ArrowRight :size="15" /></a>
    </div>

    <div class="grid gap-4 lg:grid-cols-12">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm lg:col-span-5">
            <div v-if="!props.news.length" class="flex min-h-[220px] items-center justify-center text-sm text-gray-500">No published news yet.</div>
            <div v-else class="grid gap-4 xl:grid-cols-[1.2fr_0.8fr]">
                <a :href="`/news/${props.news[0].slug}`" class="group overflow-hidden">
                    <div class="relative h-[190px] overflow-hidden rounded-lg sm:h-[205px]">
                        <img :src="props.news[0].image" :alt="props.news[0].title" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                        <div class="absolute left-3 top-3 rounded-md bg-[#006b3c] px-2 py-1 text-[9px] font-bold uppercase tracking-wide text-white">{{ props.news[0].category }}</div>
                    </div>
                    <div class="pt-3">
                        <p class="text-[10px] text-gray-400">{{ props.news[0].date }}</p>
                        <h3 class="mt-1.5 text-base font-extrabold leading-5 text-[#00351F]">{{ props.news[0].title }}</h3>
                        <p class="mt-1.5 line-clamp-3 text-xs leading-5 text-gray-500">{{ props.news[0].excerpt }}</p>
                    </div>
                </a>
                <div class="space-y-3">
                    <a v-for="item in props.news.slice(1)" :key="item.id" :href="`/news/${item.slug}`" class="group flex gap-3">
                        <div class="h-[65px] w-[78px] shrink-0 overflow-hidden rounded-md"><img :src="item.image" :alt="item.title" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" /></div>
                        <div class="min-w-0"><p class="text-[9px] text-gray-400">{{ item.date }}</p><h4 class="mt-1 line-clamp-3 text-xs font-bold leading-4 text-[#00351F]">{{ item.title }}</h4></div>
                    </a>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm lg:col-span-4">
            <div class="mb-2 flex items-start justify-between"><div><p class="text-[10px] font-bold uppercase tracking-[0.18em] text-[#006b3c]">Calendar</p><h3 class="mt-1 text-xl font-extrabold text-[#00351F]">Upcoming Events</h3></div><a href="/events" class="mt-1 text-[11px] font-bold text-[#006b3c]">View all</a></div>
            <div v-if="!props.events.length" class="py-8 text-sm text-gray-500">No upcoming published events.</div>
            <div v-else class="divide-y divide-gray-100">
                <a v-for="event in props.events" :key="event.id" :href="`/events/${event.slug}`" class="flex gap-3 py-3 first:pt-2">
                    <div class="flex h-[55px] w-[48px] shrink-0 flex-col items-center justify-center rounded-lg bg-[#006b3c] text-white"><span class="text-[9px] font-bold tracking-wide text-[#D4A72C]">{{ event.month }}</span><span class="text-xl font-extrabold leading-none">{{ event.day }}</span></div>
                    <div class="min-w-0 flex-1"><h4 class="text-xs font-bold leading-4 text-[#00351F]">{{ event.title }}</h4><div class="mt-1 space-y-0.5 text-[10px] text-gray-500"><div class="flex items-center gap-1.5"><Clock3 :size="11" /><span>{{ event.time }}</span></div><div class="flex items-center gap-1.5"><MapPin :size="11" /><span>{{ event.location || 'Location TBA' }}</span></div></div></div>
                </a>
            </div>
            <a href="/events" class="mt-2 flex items-center justify-center gap-1.5 border-t border-gray-100 pt-3 text-[9px] font-bold uppercase tracking-wide text-[#006b3c]"><CalendarDays :size="12" /> View Events</a>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm lg:col-span-3">
            <div v-if="!props.projects.length" class="flex min-h-[310px] flex-col items-center justify-center p-6 text-center"><p class="text-sm text-gray-500">No published projects yet.</p><a href="/projects" class="mt-3 text-xs font-bold text-[#006b3c]">View Projects</a></div>
            <template v-else>
                <div class="relative h-[170px] overflow-hidden sm:h-[180px]"><img :src="props.projects[0].image" :alt="props.projects[0].title" class="h-full w-full object-cover" /><div class="absolute inset-0 bg-gradient-to-t from-[#00351F]/85 via-[#00351F]/10 to-transparent"></div><div class="absolute bottom-3 left-3 rounded-md bg-[#00351F]/80 px-2 py-1 text-[9px] font-bold uppercase tracking-wider text-white">Featured Project</div></div>
                <div class="p-4"><h3 class="text-base font-extrabold leading-5 text-[#00351F]">{{ props.projects[0].title }}</h3><p class="mt-2 line-clamp-3 text-xs leading-5 text-gray-500">{{ props.projects[0].excerpt }}</p><div class="mt-4"><div class="mb-1.5 flex justify-between text-[10px]"><span class="font-semibold text-gray-600">{{ props.projects[0].progress }}% Funded</span><span class="font-bold text-[#006b3c]">₦{{ props.projects[0].raised }} raised</span></div><div class="h-1.5 overflow-hidden rounded-full bg-gray-100"><div class="h-full rounded-full bg-[#006b3c]" :style="{ width: props.projects[0].progress + '%' }"></div></div><div class="mt-1 text-right text-[9px] text-gray-400">Target: ₦{{ props.projects[0].target }}</div></div><a :href="`/projects/${props.projects[0].slug}`" class="mt-4 flex items-center justify-center gap-2 rounded-lg bg-[#006b3c] px-3 py-2.5 text-xs font-bold text-white">View Project <ExternalLink :size="13" /></a></div>
            </template>
        </div>
    </div>
</div>
</section>
</template>
