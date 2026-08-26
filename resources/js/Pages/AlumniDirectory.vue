<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { Link } from '@inertiajs/vue3';
import { Search, MapPin, ArrowRight } from 'lucide-vue-next';

const props = defineProps({
    alumni: { type: Object, default: () => ({ data: [] }) },
    filters: { type: Object, default: () => ({ search: '', class_set: '', country: '' }) },
});

const rows = props.alumni.data ?? [];
</script>

<template>
<PublicLayout>
<section class="bg-[#00351F]"><div class="mx-auto max-w-[1200px] px-5 py-14 lg:px-8"><p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">Alumni Network</p><h1 class="mt-2 text-4xl font-extrabold text-white sm:text-5xl">Find an Old Boy.</h1><p class="mt-3 max-w-2xl text-sm leading-6 text-white/70">Search and connect with members of the BBHS alumni community.</p></div></section>

<section class="bg-[#F7F9F7] py-10">
    <div class="mx-auto max-w-[1200px] px-5 lg:px-8">
        <form method="get" action="/alumni-directory" class="grid gap-3 rounded-2xl border border-gray-200 bg-white p-4 sm:grid-cols-[1fr_180px_160px_auto]">
            <div class="relative"><Search :size="17" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" /><input name="search" :value="filters.search" class="w-full rounded-lg border-gray-200 py-3 pl-10 text-sm" placeholder="Search name..." /></div>
            <input name="class_set" :value="filters.class_set" class="rounded-lg border-gray-200 px-3 py-3 text-sm" placeholder="Class / Set" />
            <input name="country" :value="filters.country" class="rounded-lg border-gray-200 px-3 py-3 text-sm" placeholder="Country" />
            <button class="rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white">SEARCH</button>
        </form>

        <div v-if="!rows.length" class="mt-8 rounded-2xl bg-white p-10 text-center text-sm text-gray-500">No approved alumni profiles found.</div>

        <div v-else class="mt-8 grid gap-5 md:grid-cols-3">
            <article v-for="person in rows" :key="person.id" class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
                <div class="h-[260px] bg-[#EEF8F2]"><img v-if="person.photo" :src="person.photo" :alt="`${person.first_name} ${person.last_name}`" class="h-full w-full object-cover" /></div>
                <div class="p-5">
                    <h2 class="text-lg font-extrabold text-[#00351F]">{{ person.first_name }} {{ person.last_name }}</h2>
                    <p v-if="person.class_set" class="mt-1 text-sm font-semibold text-[#006B3C]">{{ person.class_set }}</p>
                    <p v-if="person.city || person.country" class="mt-3 flex items-center gap-2 text-xs text-gray-500"><MapPin :size="14" /> {{ [person.city, person.country].filter(Boolean).join(', ') }}</p>
                    <a href="#" class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-[#006B3C]">View Profile <ArrowRight :size="15" /></a>
                </div>
            </article>
        </div>
    </div>
</section>
</PublicLayout>
</template>
