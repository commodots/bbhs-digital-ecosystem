<script setup>
import PublicLayout from '../../Layouts/PublicLayout.vue';
import { CalendarDays, MapPin, Download } from 'lucide-vue-next';

defineProps({
    events: { type: Array, default: () => [] },
    calendarFeedUrl: { type: String, required: true },
});

const formatDate = (value) => {
    if (!value) return 'Date to be confirmed';
    return new Date(value).toLocaleString(undefined, {
        weekday: 'short', day: 'numeric', month: 'long', year: 'numeric',
        hour: 'numeric', minute: '2-digit'
    });
};
</script>

<template>
<PublicLayout>
    <section class="bg-[#00351F]">
        <div class="mx-auto max-w-[1200px] px-5 py-12 lg:px-8">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">BBHS Old Boys Association</p>
            <div class="mt-3 flex flex-wrap items-end justify-between gap-5">
                <div>
                    <h1 class="text-4xl font-extrabold text-white">Events Calendar</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-white/70">Upcoming association events and gatherings.</p>
                </div>
                <a :href="calendarFeedUrl" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-bold text-[#00351F]">
                    <Download :size="17" /> Download calendar (.ics)
                </a>
            </div>
        </div>
    </section>
    <section class="bg-[#F7F9F7] py-12">
        <div class="mx-auto max-w-[1000px] space-y-4 px-5 lg:px-8">
            <div v-if="!events.length" class="rounded-xl border bg-white p-10 text-center text-gray-500">No upcoming events are currently scheduled.</div>
            <article v-for="event in events" :key="event.id" class="rounded-xl border bg-white p-5 shadow-sm sm:flex sm:items-center sm:justify-between sm:gap-6">
                <div>
                    <h2 class="text-lg font-extrabold text-[#00351F]">{{ event.title }}</h2>
                    <p class="mt-2 flex items-center gap-2 text-sm text-gray-600"><CalendarDays :size="16" class="text-[#006B3C]"/> {{ formatDate(event.starts_at) }}</p>
                    <p v-if="event.venue || event.location" class="mt-1 flex items-center gap-2 text-sm text-gray-500"><MapPin :size="16" /> {{ [event.venue,event.location].filter(Boolean).join(' · ') }}</p>
                </div>
                <a :href="`/events/${event.slug}`" class="mt-4 inline-flex shrink-0 items-center rounded-lg border border-[#006B3C] px-4 py-2 text-sm font-bold text-[#006B3C] sm:mt-0">View event</a>
            </article>
        </div>
    </section>
</PublicLayout>
</template>
