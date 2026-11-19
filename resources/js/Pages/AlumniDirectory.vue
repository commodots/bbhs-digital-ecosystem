<script setup>
import MemberPortalLayout from '../Layouts/MemberPortalLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { Search, MapPin, ArrowRight, Users, RotateCcw } from 'lucide-vue-next';
import { reactive } from 'vue';

const props = defineProps({
    alumni: { type: Object, default: () => ({ data: [], links: [] }) },
    filters: { type: Object, default: () => ({}) },
    graduationSets: { type: Array, default: () => [] },
    branches: { type: Array, default: () => [] },
    countries: { type: Array, default: () => [] },
});

const form = reactive({
    search: props.filters.search ?? '',
    graduation_set_id: props.filters.graduation_set_id ?? '',
    country: props.filters.country ?? '',
    city: props.filters.city ?? '',
    branch_id: props.filters.branch_id ?? '',
    profession: props.filters.profession ?? '',
});

const submit = () => router.get('/alumni-directory', form, { preserveState: true, preserveScroll: true });
const reset = () => router.get('/alumni-directory');
</script>

<template>
<MemberPortalLayout>
    <section class="bg-[#00351F]"><div class="mx-auto max-w-[1200px] px-5 py-14 lg:px-8"><p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">Alumni Network</p><h1 class="mt-2 text-4xl font-extrabold text-white sm:text-5xl">Find an Old Boy.</h1><p class="mt-3 max-w-2xl text-sm leading-6 text-white/70">Discover approved members of the BBHS alumni community by name, graduation set and location.</p></div></section>

    <section class="bg-[#F7F9F7] py-10">
        <div class="mx-auto max-w-[1200px] px-5 lg:px-8">
            <form @submit.prevent="submit" class="grid gap-3 rounded-2xl border border-gray-200 bg-white p-5 sm:grid-cols-2 lg:grid-cols-3">
                <label class="text-xs font-semibold text-gray-600">Name<input v-model="form.search" class="mt-2 w-full rounded-lg border-gray-200 px-3 py-3 text-sm font-normal" placeholder="Search first or last name" /></label>
                <label class="text-xs font-semibold text-gray-600">Graduation set<select v-model="form.graduation_set_id" class="mt-2 w-full rounded-lg border-gray-200 px-3 py-3 text-sm font-normal"><option value="">All sets</option><option v-for="set in graduationSets" :key="set.id" :value="String(set.id)">{{ set.name }} ({{ set.graduation_year }})</option></select></label>
                <label class="text-xs font-semibold text-gray-600">Country<select v-model="form.country" class="mt-2 w-full rounded-lg border-gray-200 px-3 py-3 text-sm font-normal"><option value="">All countries</option><option v-for="country in countries" :key="country" :value="country">{{ country }}</option></select></label>
                <label class="text-xs font-semibold text-gray-600">City<input v-model="form.city" class="mt-2 w-full rounded-lg border-gray-200 px-3 py-3 text-sm font-normal" placeholder="e.g. Lagos" /></label>
                <label class="text-xs font-semibold text-gray-600">Branch area<select v-model="form.branch_id" class="mt-2 w-full rounded-lg border-gray-200 px-3 py-3 text-sm font-normal"><option value="">All branch areas</option><option v-for="branch in branches" :key="branch.id" :value="String(branch.id)">{{ branch.name }}</option></select></label>
                <label class="text-xs font-semibold text-gray-600">Profession<input v-model="form.profession" class="mt-2 w-full rounded-lg border-gray-200 px-3 py-3 text-sm font-normal" placeholder="e.g. Engineer" /></label>
                <div class="flex flex-wrap items-center gap-3 sm:col-span-2 lg:col-span-3"><button class="inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-6 py-3 text-sm font-bold text-white"><Search :size="16" /> SEARCH DIRECTORY</button><button type="button" @click="reset" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-600"><RotateCcw :size="15" /> Clear filters</button></div>
            </form>

            <div class="mt-7 flex items-center justify-between"><p class="text-sm text-gray-600"><Users :size="16" class="mr-1 inline" /> {{ alumni.total ?? alumni.data.length }} approved member{{ (alumni.total ?? alumni.data.length) === 1 ? '' : 's' }} listed</p></div>
            <div v-if="!alumni.data?.length" class="mt-5 rounded-2xl bg-white p-10 text-center"><p class="font-bold text-[#00351F]">No matching alumni found</p><p class="mt-2 text-sm text-gray-500">Try changing your search or clearing one or more filters.</p></div>
            <div v-else class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <Link v-for="person in alumni.data" :key="person.id" :href="`/alumni-directory/${person.id}`" class="group block overflow-hidden rounded-2xl border border-gray-200 bg-white transition hover:-translate-y-1 hover:border-[#006B3C] hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#006B3C]">
                    <div class="flex h-[230px] items-center justify-center bg-[#EEF8F2]"><img v-if="person.photo" :src="person.photo" :alt="`${person.first_name} ${person.last_name}`" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.02]" /><span v-else class="text-5xl font-extrabold text-[#006B3C]/30">{{ person.first_name?.charAt(0) }}{{ person.last_name?.charAt(0) }}</span></div>
                    <div class="p-5"><h2 class="text-lg font-extrabold text-[#00351F] group-hover:text-[#006B3C]">{{ person.first_name }} {{ person.last_name }}</h2><p v-if="person.graduation_set?.name || person.class_set" class="mt-1 text-sm font-semibold text-[#006B3C]">{{ person.graduation_set?.name ?? person.class_set }}</p><p v-if="person.phone" class="mt-2 text-xs text-gray-500">Phone: {{ person.phone }}</p><p v-if="person.email" class="text-xs text-gray-500">Email: {{ person.email }}</p><p v-if="person.profession" class="mt-2 text-sm text-gray-600">{{ person.profession }}</p><p v-if="person.city || person.country" class="mt-3 flex items-center gap-2 text-xs text-gray-500"><MapPin :size="14" /> {{ [person.city, person.country].filter(Boolean).join(', ') }}</p><span class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-[#006B3C]">View Profile <ArrowRight :size="15" /></span></div>
                </Link>
            </div>
            <div v-if="alumni.links?.length > 3" class="mt-8 flex flex-wrap justify-center gap-2"><template v-for="link in alumni.links" :key="link.label"><Link v-if="link.url" :href="link.url" preserve-scroll class="rounded-lg border px-4 py-2 text-sm" :class="link.active ? 'border-[#006B3C] bg-[#006B3C] text-white' : 'border-gray-200 bg-white text-gray-700'" v-html="link.label" /><span v-else class="rounded-lg border border-gray-100 px-4 py-2 text-sm text-gray-300" v-html="link.label" /></template></div>
        </div>
    </section>
</MemberPortalLayout>
</template>
