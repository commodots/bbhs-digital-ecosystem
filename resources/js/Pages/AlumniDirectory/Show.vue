<script setup>
import MemberPortalLayout from '../../Layouts/MemberPortalLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, MapPin, Briefcase, GraduationCap, UserCircle } from 'lucide-vue-next';

const props = defineProps({ person: { type: Object, required: true } });
const page = usePage();
const canStartChat = () => {
    const user = page.props.auth?.user;
    return !!user && (user.is_admin || (user.member_status === 'approved'));
};
</script>

<template>
<MemberPortalLayout>
    <section class="bg-[#00351F]"><div class="mx-auto max-w-[1000px] px-5 py-12 lg:px-8"><Link href="/alumni-directory" class="inline-flex items-center gap-2 text-sm font-semibold text-white/75 hover:text-white"><ArrowLeft :size="16" /> Back to directory</Link><p class="mt-8 text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">BBHS Alumni Network</p><h1 class="mt-2 text-3xl font-extrabold text-white sm:text-4xl">{{ person.first_name }} {{ person.last_name }}</h1><p v-if="person.class_set" class="mt-2 text-sm font-semibold text-white/70">{{ person.class_set }}<span v-if="person.graduation_year"> · Class of {{ person.graduation_year }}</span></p></div></section>
    <section class="bg-[#F7F9F7] py-12"><div class="mx-auto max-w-[1000px] px-5 lg:px-8"><div class="grid gap-8 rounded-2xl border border-gray-200 bg-white p-6 sm:grid-cols-[260px_1fr] sm:p-9"><div class="flex min-h-[250px] items-center justify-center overflow-hidden rounded-xl bg-[#EEF8F2]"><img v-if="person.photo" :src="person.photo" :alt="`${person.first_name} ${person.last_name}`" class="h-full w-full object-cover" /><UserCircle v-else :size="100" class="text-[#006B3C]/30" /></div><div><div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-xl font-extrabold text-[#00351F]">About</h2>
                        <Link v-if="canStartChat() && person.id" href="/messages/start" method="post" :data="{ recipient_profile_id: person.id }" as="button" class="rounded-lg bg-[#006B3C] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#00552F]">Connect / Message</Link>
                        <Link v-else-if="!page.props.auth?.user" href="/login" class="rounded-lg border border-[#006B3C] px-4 py-2.5 text-sm font-bold text-[#006B3C]">Sign in to message</Link>
                    <p class="mt-3 text-xs text-gray-500">Members of your graduation set can message you directly. Members from other sets must be approved through a connection request first.</p></div><p v-if="person.bio" class="mt-4 whitespace-pre-line text-sm leading-7 text-gray-600">{{ person.bio }}</p><p v-else class="mt-4 text-sm text-gray-500">This member has not added a biography yet.</p><div class="mt-5 rounded-lg bg-gray-50 p-4 text-sm text-gray-600"><p v-if="person.phone">Phone: <a href="/login" class="font-semibold text-[#006B3C] underline">{{ person.phone }}</a></p><p v-if="person.email" class="mt-1">Email: <a href="/login" class="font-semibold text-[#006B3C] underline">{{ person.email }}</a></p></div><div class="mt-7 space-y-4 border-t border-gray-100 pt-6"><p v-if="person.profession" class="flex items-center gap-3 text-sm text-gray-600"><Briefcase :size="17" class="text-[#006B3C]" /> {{ person.profession }}</p><p v-if="person.class_set" class="flex items-center gap-3 text-sm text-gray-600"><GraduationCap :size="17" class="text-[#006B3C]" /> {{ person.class_set }}</p><p v-if="person.city || person.country" class="flex items-center gap-3 text-sm text-gray-600"><MapPin :size="17" class="text-[#006B3C]" /> {{ [person.city, person.country].filter(Boolean).join(', ') }}</p></div><p class="mt-8 text-xs leading-5 text-gray-400">Contact details are not displayed publicly. Please use the Association's approved channels to connect with members.</p></div></div></div></section>
</MemberPortalLayout>
</template>
