<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';

defineProps({
    executives: { type: Array, default: () => [] },
    termLabel: { type: String, default: 'Serving until January 2027' },
});

const photoUrl = (photo) => !photo ? null : (photo.startsWith('/') ? photo : `/storage/${photo}`);
</script>

<template>
    <PublicLayout>
        <section class="bg-[#00351F]"><div class="mx-auto max-w-[1200px] px-5 py-16 lg:px-8"><div class="max-w-3xl"><p class="text-xs font-bold uppercase tracking-[0.28em] text-[#E5C65B]">Leadership · Service · Brotherhood</p><h1 class="mt-4 text-4xl font-extrabold text-white sm:text-5xl">National Executive Council</h1><p class="mt-5 max-w-2xl text-base leading-7 text-white/75">Meet the national officers serving the Baptist Boys’ High School Old Boys’ Association.</p><div class="mt-7 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white"><span class="h-2 w-2 rounded-full bg-[#E5C65B]"></span> Current Executive Committee · {{ termLabel }}</div></div></div></section>
        <section class="bg-[#F7F9F7] py-14 sm:py-16"><div class="mx-auto max-w-[1200px] px-5 lg:px-8"><div class="mb-10 max-w-2xl"><p class="text-xs font-bold uppercase tracking-[0.2em] text-[#087A3A]">National Leadership</p><h2 class="mt-2 text-2xl font-extrabold text-[#00351F] sm:text-3xl">Serving the BBHS Old Boys’ Community</h2><p class="mt-3 text-sm leading-6 text-gray-600">Executive appointments are connected to member records and maintained by the Association’s administrators.</p></div>
            <div v-if="executives.length" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <article v-for="(person, index) in executives" :key="person.id" :class="['rounded-2xl border bg-white p-5 text-center shadow-sm', index === 0 ? 'border-[#D4A72C]/50 sm:col-span-2 lg:col-span-1' : 'border-gray-100']">
                    <div class="mx-auto h-32 w-32 overflow-hidden rounded-full border-4 border-[#087A3A]/15 bg-gray-100"><img v-if="photoUrl(person.photo)" :src="photoUrl(person.photo)" :alt="person.name" class="h-full w-full object-cover" loading="lazy" /><div v-else class="flex h-full w-full items-center justify-center text-3xl font-extrabold text-[#087A3A]">{{ person.name?.split(' ').map(n => n[0]).slice(0,2).join('') }}</div></div>
                    <p class="mt-4 text-sm font-bold text-[#087A3A]">{{ person.position }}</p><a v-if="person.profile_url" :href="person.profile_url" class="mt-2 inline-block text-base font-extrabold leading-snug text-[#00351F] hover:text-[#087A3A] hover:underline">{{ person.name }}</a><h3 v-else class="mt-2 text-base font-extrabold leading-snug text-[#00351F]">{{ person.name }}</h3><p class="mt-2 text-xs font-semibold text-gray-500">{{ person.set ? `Graduation Set ${person.set}` : 'Graduation set not recorded' }}</p><a v-if="person.profile_url" :href="person.profile_url" class="mt-3 inline-flex text-xs font-bold text-[#087A3A] hover:underline">View Member Profile →</a>
                </article>
            </div>
            <div v-else class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center"><h3 class="text-lg font-bold text-[#00351F]">Executive appointments are being configured</h3><p class="mt-2 text-sm text-gray-500">Please check back as the Association completes its member-linked executive records.</p></div>
            <p class="mt-10 text-center text-xs leading-5 text-gray-500">The executive roster reflects the Association’s confirmed committee and may be updated by authorised administrators.</p>
        </div></section>
    </PublicLayout>
</template>
