<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { CalendarDays, MapPin, Clock3, ArrowRight } from 'lucide-vue-next';
const image = '/images/bbhs/events/reunion-02.jpg';
</script>

<template>
<PublicLayout>
<section class="relative min-h-[430px] overflow-hidden bg-[#00351F]">
    <img :src="image" alt="BBHS event" class="absolute inset-0 h-full w-full object-cover" />
    <div class="absolute inset-0 bg-[#00351F]/80"></div>
    <div class="relative mx-auto flex min-h-[430px] max-w-[1200px] items-end px-5 py-14 lg:px-8">
        <div><p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">BBHS Event</p><h1 class="mt-3 max-w-4xl text-4xl font-extrabold text-white sm:text-5xl">Global BBHS Alumni Reunion</h1></div>
    </div>
</section>

<section class="bg-white py-12">
    <div class="mx-auto max-w-[1100px] px-5 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[1fr_320px]">
            <article>
                <p class="text-sm leading-7 text-gray-600">Join fellow Old Boys for a special gathering celebrating friendship, shared memories and the enduring BBHS brotherhood.</p>
                <h2 class="mt-8 text-2xl font-extrabold text-[#00351F]">About the Event</h2>
                <p class="mt-3 text-sm leading-7 text-gray-600">This event page will later contain the full event description, programme, speakers, registration information and updates from the association.</p>
            </article>
            <aside class="rounded-2xl bg-[#F7F9F7] p-6">
                <div class="space-y-5">
                    <div class="flex gap-3"><CalendarDays class="text-[#006B3C]" :size="20" /><div><b class="text-sm text-[#00351F]">Date</b><p class="text-xs text-gray-500">22 July 2026</p></div></div>
                    <div class="flex gap-3"><Clock3 class="text-[#006B3C]" :size="20" /><div><b class="text-sm text-[#00351F]">Time</b><p class="text-xs text-gray-500">4:00 PM</p></div></div>
                    <div class="flex gap-3"><MapPin class="text-[#006B3C]" :size="20" /><div><b class="text-sm text-[#00351F]">Location</b><p class="text-xs text-gray-500">Lagos, Nigeria</p></div></div>
                </div>
                <button class="mt-7 w-full rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white">REGISTER FOR EVENT <ArrowRight :size="15" class="inline" /></button>
            </aside>
        </div>
    </div>
</section>
</PublicLayout>
</template>
