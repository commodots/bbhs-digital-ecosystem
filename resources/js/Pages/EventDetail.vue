<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { ArrowLeft, CalendarDays, Clock3, MapPin } from 'lucide-vue-next';

defineProps({ event: { type: Object, required: true } });
</script>

<template>
<PublicLayout>
<section class="relative min-h-[430px] overflow-hidden bg-[#00351F]">
<img v-if="event.image" :src="event.image" :alt="event.title" class="absolute inset-0 h-full w-full object-cover" />
<div class="absolute inset-0 bg-[#00351F]/80"></div>
<div class="relative mx-auto flex min-h-[430px] max-w-[1200px] items-end px-5 py-14 lg:px-8">
<div><p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">BBHS Event</p><h1 class="mt-3 max-w-4xl text-4xl font-extrabold text-white sm:text-5xl">{{ event.title }}</h1></div>
</div>
</section>
<section class="bg-white py-12">
<div class="mx-auto max-w-[1100px] px-5 lg:px-8">
<div class="grid gap-8 lg:grid-cols-[1fr_320px]">
<article>
<p v-if="event.excerpt" class="text-lg leading-8 text-gray-600">{{ event.excerpt }}</p>
<h2 class="mt-8 text-2xl font-extrabold text-[#00351F]">About the Event</h2>
<p class="mt-3 whitespace-pre-line text-sm leading-7 text-gray-600">{{ event.description }}</p>
<a href="/events" class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-[#006B3C]"><ArrowLeft :size="16" /> Back to Events</a>
</article>
<aside class="rounded-2xl bg-[#F7F9F7] p-6">
<div class="space-y-5">
<div v-if="event.starts_at" class="flex gap-3"><CalendarDays class="text-[#006B3C]" :size="20" /><div><b class="text-sm text-[#00351F]">Starts</b><p class="text-xs text-gray-500">{{ event.starts_at }}</p></div></div>
<div v-if="event.ends_at" class="flex gap-3"><Clock3 class="text-[#006B3C]" :size="20" /><div><b class="text-sm text-[#00351F]">Ends</b><p class="text-xs text-gray-500">{{ event.ends_at }}</p></div></div>
<div v-if="event.venue || event.location" class="flex gap-3"><MapPin class="text-[#006B3C]" :size="20" /><div><b class="text-sm text-[#00351F]">Location</b><p class="text-xs text-gray-500">{{ event.venue }}<span v-if="event.venue && event.location"> · </span>{{ event.location }}</p></div></div>
</div>
<div v-if="event.registration_open" class="mt-7 rounded-lg bg-[#00351F] p-4 text-sm font-semibold text-white">Registration is open for this event.</div>
</aside>
</div>
</div>
</section>
</PublicLayout>
</template>
