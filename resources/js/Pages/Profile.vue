<script setup>
import MemberPortalLayout from '../Layouts/MemberPortalLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { Save, UserCircle, ShieldCheck } from 'lucide-vue-next';

const page = usePage();
const profile = page.props.alumniProfile ?? {};
const photoUrl = page.props.photoUrl ?? null;
import { ref } from 'vue';
const photoPreview = ref(photoUrl);
const onPhotoChange = (event) => { const file = event.target.files?.[0] ?? null; form.profile_photo = file; photoPreview.value = file ? URL.createObjectURL(file) : photoUrl; };
const form = useForm({
    first_name: profile.first_name ?? '',
    profile_photo: null,
    remove_profile_photo: false,
    last_name: profile.last_name ?? '',
    phone: profile.phone ?? '',
    whatsapp_phone: profile.whatsapp_phone ?? '',
    alternate_phone: profile.alternate_phone ?? '',
    preferred_contact: profile.preferred_contact ?? 'whatsapp',
    sms_consent: profile.sms_consent ?? false,
    whatsapp_consent: profile.whatsapp_consent ?? false,
    profession: profile.profession ?? '',
    city: profile.city ?? '',
    country: profile.country ?? '',
    bio: profile.bio ?? '',
    directory_visible: profile.directory_visible ?? false,
    public_photo: profile.public_photo ?? true,
    public_name: profile.public_name ?? true,
    public_graduation_set: profile.public_graduation_set ?? true,
    public_profession: profile.public_profession ?? true,
    public_location: profile.public_location ?? true,
    public_bio: profile.public_bio ?? true,
});
const submit = () => form.transform((data) => ({ ...data, _method: 'put' })).post('/profile', { forceFormData: true, preserveScroll: true });
</script>

<template>
<MemberPortalLayout>
    <section class="bg-[#00351F]"><div class="mx-auto max-w-[1200px] px-5 py-12 lg:px-8"><p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">Member Portal</p><h1 class="mt-2 text-3xl font-extrabold text-white sm:text-4xl">My Profile</h1><p class="mt-2 text-sm text-white/70">Manage your contact information and directory privacy.</p></div></section>
    <section class="bg-[#F7F9F7] py-12"><div class="mx-auto max-w-[1000px] px-5 lg:px-8">
        <form class="rounded-2xl border border-gray-200 bg-white p-7 sm:p-9" @submit.prevent="submit">
            <div class="flex items-center gap-4 border-b border-gray-100 pb-6"><div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-full bg-[#EEF8F2] text-[#006B3C]"><img v-if="photoPreview" :src="photoPreview" alt="Member profile photograph" class="h-full w-full object-cover" /><UserCircle v-else :size="38" /></div><div><h2 class="text-xl font-extrabold text-[#00351F]">{{ page.props.auth?.user?.name ?? 'Alumni Profile' }}</h2><p class="text-sm text-gray-500">Keep your information current.</p></div></div>
            <div class="mt-6 rounded-xl border border-gray-200 bg-gray-50 p-4">
                <h3 class="font-bold text-[#00351F]">Profile photograph</h3>
                <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-center">
                    <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-full bg-white text-[#006B3C] ring-1 ring-gray-200"><img v-if="photoPreview" :src="photoPreview" alt="Profile preview" class="h-full w-full object-cover" /><UserCircle v-else :size="48" /></div>
                    <div class="flex-1">
                        <input type="file" accept="image/jpeg,image/png,image/webp" @change="onPhotoChange" class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-[#006B3C] file:px-4 file:py-2 file:font-semibold file:text-white" />
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG or WebP. Maximum 5 MB.</p>
                        <p v-if="form.errors.profile_photo" class="mt-1 text-xs text-red-600">{{ form.errors.profile_photo }}</p>
                        <label v-if="photoUrl" class="mt-3 flex items-center gap-2 text-xs text-gray-600"><input v-model="form.remove_profile_photo" type="checkbox" class="rounded border-gray-300" /> Remove current photograph</label>
                    </div>
                </div>
            </div>
            <div class="mt-7"><h3 class="font-bold text-[#00351F]">Personal information</h3><div class="mt-4 grid gap-5 sm:grid-cols-2">
                <div><input v-model="form.first_name" class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="First name" /><p v-if="form.errors.first_name" class="mt-1 text-xs text-red-600">{{ form.errors.first_name }}</p></div>
                <div><input v-model="form.last_name" class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Last name" /><p v-if="form.errors.last_name" class="mt-1 text-xs text-red-600">{{ form.errors.last_name }}</p></div>
                <div><input v-model="form.phone" required class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Primary phone number (required)" /><p v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}</p></div>
                <input v-model="form.whatsapp_phone" class="rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="WhatsApp number (if different)" />
                <input v-model="form.alternate_phone" class="rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Alternative phone number (optional)" />
                <select v-model="form.preferred_contact" class="rounded-lg border-gray-200 px-4 py-3 text-sm"><option value="whatsapp">Preferred: WhatsApp</option><option value="sms">Preferred: SMS</option><option value="phone">Preferred: Phone call</option><option value="email">Preferred: Email</option></select>
                <input v-model="form.profession" class="rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Profession" />
                <input v-model="form.city" class="rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Current city" />
                <input v-model="form.country" class="rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Current country" />
                <textarea v-model="form.bio" class="min-h-[120px] rounded-lg border-gray-200 px-4 py-3 text-sm sm:col-span-2" placeholder="Short biography"></textarea>
            </div></div>
            <div class="mt-8 rounded-xl border border-gray-100 bg-gray-50 p-5"><div class="flex items-start gap-3"><ShieldCheck :size="21" class="mt-0.5 text-[#006B3C]" /><div><h3 class="font-bold text-[#00351F]">Membership and privacy</h3><p class="mt-1 text-sm leading-6 text-gray-600">Your graduation set is {{ profile.graduation_set?.name ?? profile.class_set ?? 'recorded by the Association' }}. To protect membership verification, changes to your graduation set must be requested through the Association and cannot be made from this profile form.</p></div></div>
                <label class="mt-5 flex cursor-pointer items-start gap-3"><input v-model="form.directory_visible" type="checkbox" class="mt-1 rounded border-gray-300 text-[#006B3C] focus:ring-[#006B3C]" /><span><span class="block text-sm font-semibold text-gray-800">Show my profile in the public alumni directory</span><span class="mt-1 block text-xs leading-5 text-gray-500">When enabled, your approved profile can appear in the public directory. You can choose which profile fields are visible below. Phone and email are always masked.</span></span></label>
                <div v-if="form.directory_visible" class="mt-5 grid gap-3 border-t border-gray-200 pt-5 sm:grid-cols-2">
                    <label v-for="item in [
                        { key: 'public_photo', label: 'Profile photograph' },
                        { key: 'public_name', label: 'Full name' },
                        { key: 'public_graduation_set', label: 'Graduation set' },
                        { key: 'public_profession', label: 'Profession' },
                        { key: 'public_location', label: 'City and country' },
                        { key: 'public_bio', label: 'Biography' },
                    ]" :key="item.key" class="flex items-center gap-2 text-sm text-gray-700"><input v-model="form[item.key]" type="checkbox" class="rounded border-gray-300 text-[#006B3C]" /> Show {{ item.label }} publicly</label>
                    <p class="text-xs leading-5 text-gray-500 sm:col-span-2">Phone and email remain masked on public pages. Clicking either masked contact detail asks visitors to log in. Your actual contact information is never sent to the public directory.</p>
                </div>
            </div>
            <div class="mt-6 rounded-xl border border-gray-100 bg-gray-50 p-5">
                <h3 class="font-bold text-[#00351F]">Communication preferences</h3>
                <p class="mt-1 text-xs text-gray-500">Your contact details remain private and are only available to authorised Association administrators.</p>
                <label class="mt-3 flex items-start gap-2 text-sm text-gray-700"><input v-model="form.whatsapp_consent" type="checkbox" class="mt-1 rounded border-gray-300"/> I agree to receive BBHS WhatsApp updates and reminders.</label>
                <label class="mt-3 flex items-start gap-2 text-sm text-gray-700"><input v-model="form.sms_consent" type="checkbox" class="mt-1 rounded border-gray-300"/> I agree to receive BBHS SMS updates and reminders.</label>
            </div>
            <button :disabled="form.processing" type="submit" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-6 py-3 text-sm font-bold text-white disabled:opacity-60"><Save :size="16" /> {{ form.processing ? 'SAVING...' : 'SAVE PROFILE' }}</button>
            <p v-if="form.recentlySuccessful" class="mt-3 text-sm text-green-700">Profile updated successfully.</p>
        </form>
    </div></section>
</MemberPortalLayout>
</template>
