<script setup>
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import AdminQuickLinks from '../../../Components/AdminQuickLinks.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2, PauseCircle, UserCircle, Save, KeyRound, MailCheck } from 'lucide-vue-next';

const props = defineProps({ member: { type: Object, required: true } });
const page = usePage();

const action = (url) => useForm({}).post(url, { preserveScroll: true });
const emailForm = useForm({ email: props.member.email || '' });
const profileForm = useForm({
    first_name: props.member.alumni_profile?.first_name || (props.member.name || '').split(' ')[0] || '',
    last_name: props.member.alumni_profile?.last_name || (props.member.name || '').split(' ').slice(1).join(' '),
    phone: props.member.alumni_profile?.phone || '',
});
const photoForm = useForm({ profile_photo: null, remove_profile_photo: false });
const saveProfile = () => profileForm.patch(`/admin/members/${props.member.id}/profile`, { preserveScroll: true });
const savePhoto = () => photoForm.post(`/admin/members/${props.member.id}/photo`, { forceFormData: true, preserveScroll: true, onSuccess: () => { photoForm.reset('profile_photo', 'remove_profile_photo'); } });
const removePhoto = () => { photoForm.profile_photo = null; photoForm.remove_profile_photo = true; savePhoto(); };
const saveEmail = () => emailForm.patch(`/admin/members/${props.member.id}/email`, { preserveScroll: true });
const sendPasswordLink = () => useForm({}).post(`/admin/members/${props.member.id}/send-password-link`, { preserveScroll: true });
</script>

<template>
<PublicLayout>
<section class="bg-[#00351F]">
    <div class="mx-auto max-w-[1200px] px-5 py-12 lg:px-8">
        <a href="/admin/members" class="inline-flex items-center gap-2 text-xs font-bold text-white/70 hover:text-white"><ArrowLeft :size="14" /> BACK TO MEMBERS</a>
        <p class="mt-7 text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">Member Details</p>
        <h1 class="mt-2 text-3xl font-extrabold text-white">{{ member.name }}</h1>
    </div>
<AdminQuickLinks />
    </section>

<section class="bg-[#F7F9F7] py-10">
<div class="mx-auto max-w-[1000px] px-5 lg:px-8">
    <div v-if="page.props.flash?.success" class="mb-5 rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ page.props.flash.success }}</div>
    <div v-if="page.props.flash?.error" class="mb-5 rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ page.props.flash.error }}</div>

    <div class="rounded-2xl border border-gray-200 bg-white p-7 sm:p-9">
        <div class="flex flex-col gap-5 border-b border-gray-100 pb-7 sm:flex-row sm:items-center">
            <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-full bg-[#EEF8F2] text-[#006B3C]"><img v-if="member.alumni_profile?.photo" :src="member.alumni_profile.photo.startsWith('/') ? member.alumni_profile.photo : `/storage/${member.alumni_profile.photo}`" alt="Member photograph" class="h-full w-full object-cover" /><UserCircle v-else :size="48" /></div>
            <div>
                <h2 class="text-2xl font-extrabold text-[#00351F]">{{ member.name }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ member.email }}</p>
                <span class="mt-3 inline-block rounded-full bg-amber-50 px-3 py-1 text-xs font-bold uppercase text-amber-700">{{ member.member_status }}</span>
            </div>
        </div>

        <form class="mt-6 rounded-xl border border-gray-100 bg-gray-50 p-5" @submit.prevent="savePhoto">
            <h3 class="font-bold text-[#00351F]">Display Picture</h3>
            <p class="mt-1 text-xs text-gray-500">Upload or replace this member's photograph (JPG, PNG or WebP, maximum 5MB).</p>
            <div class="mt-3 flex flex-wrap items-center gap-3">
                <input type="file" accept="image/jpeg,image/png,image/webp" class="block max-w-full text-sm" @input="photoForm.profile_photo = $event.target.files[0]" />
                <button type="submit" :disabled="photoForm.processing || !photoForm.profile_photo" class="rounded-lg bg-[#006B3C] px-4 py-2.5 text-sm font-bold text-white disabled:opacity-50">Upload / Replace Photo</button>
                <button v-if="member.alumni_profile?.photo" type="button" :disabled="photoForm.processing" @click="removePhoto" class="rounded-lg border border-red-200 px-4 py-2.5 text-sm font-bold text-red-700 disabled:opacity-50">Remove Photo</button>
            </div>
            <p v-if="photoForm.errors.profile_photo" class="mt-2 text-xs text-red-600">{{ photoForm.errors.profile_photo }}</p>
        </form>

        <form class="mt-7 rounded-xl border border-gray-100 bg-gray-50 p-5" @submit.prevent="saveEmail">
            <h3 class="font-bold text-[#00351F]">Member Email Address</h3>
            <p class="mt-1 text-xs leading-5 text-gray-500">Administrators can update this address. For executive members, this may be the assigned BBHS position email.</p>
            <div class="mt-3 flex flex-col gap-3 sm:flex-row">
                <input v-model="emailForm.email" type="email" required class="min-w-0 flex-1 rounded-lg border-gray-200 text-sm" />
                <button type="submit" :disabled="emailForm.processing" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white disabled:opacity-50"><Save :size="16" /> Save Email</button>
            </div>
            <p v-if="emailForm.errors.email" class="mt-2 text-xs text-red-600">{{ emailForm.errors.email }}</p>
        </form>

        <div class="mt-6 rounded-xl border border-blue-100 bg-blue-50/60 p-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-bold text-[#00351F]">Account Access & Password</h3>
                    <p class="mt-1 text-xs leading-5 text-gray-600">Send a secure, time-limited password setup or recovery link to the member's current email address.</p>
                    <p class="mt-2 text-xs font-semibold" :class="member.email_verified_at ? 'text-green-700' : 'text-amber-700'">
                        {{ member.email_verified_at ? 'Email verified' : 'Not yet verified / password setup may be pending' }}
                    </p>
                </div>
                <button type="button" @click="sendPasswordLink" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-[#00351F] px-4 py-3 text-xs font-bold text-white hover:bg-[#006B3C]">
                    <KeyRound :size="15" /> Send Password Link
                </button>
            </div>
        </div>

        <form class="mt-7 rounded-xl border border-gray-100 bg-gray-50 p-5" @submit.prevent="saveProfile">
            <h3 class="font-bold text-[#00351F]">Member Personal Details</h3>
            <p class="mt-1 text-xs text-gray-500">Update the member's first name, last name and phone number. The account display name will be updated too.</p>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <label class="block text-sm font-semibold text-gray-700">First Name
                    <input v-model="profileForm.first_name" type="text" required maxlength="100" class="mt-1 w-full rounded-lg border-gray-200 text-sm" />
                    <span v-if="profileForm.errors.first_name" class="mt-1 block text-xs text-red-600">{{ profileForm.errors.first_name }}</span>
                </label>
                <label class="block text-sm font-semibold text-gray-700">Last Name
                    <input v-model="profileForm.last_name" type="text" required maxlength="100" class="mt-1 w-full rounded-lg border-gray-200 text-sm" />
                    <span v-if="profileForm.errors.last_name" class="mt-1 block text-xs text-red-600">{{ profileForm.errors.last_name }}</span>
                </label>
                <label class="block text-sm font-semibold text-gray-700 sm:col-span-2">Phone Number
                    <input v-model="profileForm.phone" type="tel" required maxlength="40" class="mt-1 w-full rounded-lg border-gray-200 text-sm" />
                    <span v-if="profileForm.errors.phone" class="mt-1 block text-xs text-red-600">{{ profileForm.errors.phone }}</span>
                </label>
            </div>
            <button type="submit" :disabled="profileForm.processing" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white disabled:opacity-50"><Save :size="16" /> Save Personal Details</button>
        </form>

        <div v-if="member.alumni_profile" class="mt-7 grid gap-5 sm:grid-cols-2">
            <div><p class="text-xs font-bold uppercase text-gray-400">Class / Set</p><p class="mt-1 font-semibold">{{ member.alumni_profile.class_set || '—' }}</p></div>
            <div><p class="text-xs font-bold uppercase text-gray-400">Profession</p><p class="mt-1 font-semibold">{{ member.alumni_profile.profession || '—' }}</p></div>
            <div><p class="text-xs font-bold uppercase text-gray-400">Location</p><p class="mt-1 font-semibold">{{ [member.alumni_profile.city, member.alumni_profile.country].filter(Boolean).join(', ') || '—' }}</p></div>
            <div class="sm:col-span-2"><p class="text-xs font-bold uppercase text-gray-400">Biography</p><p class="mt-2 whitespace-pre-line text-sm leading-7 text-gray-600">{{ member.alumni_profile.bio || 'No biography provided.' }}</p></div>
        </div>

        <div v-else class="mt-7 rounded-lg bg-amber-50 p-4 text-sm text-amber-800">This member does not have an alumni profile yet.</div>

        <div class="mt-8 flex flex-wrap gap-3 border-t border-gray-100 pt-7">
            <button v-if="member.member_status !== 'approved'" type="button" class="inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white" @click="action(`/admin/members/${member.id}/approve`)"><CheckCircle2 :size="16" /> APPROVE MEMBER</button>
            <button v-if="member.member_status === 'approved'" type="button" class="inline-flex items-center gap-2 rounded-lg bg-red-50 px-5 py-3 text-sm font-bold text-red-700" @click="action(`/admin/members/${member.id}/suspend`)"><PauseCircle :size="16" /> SUSPEND MEMBER</button>
        </div>
    </div>
</div>
</section>
</PublicLayout>
</template>
