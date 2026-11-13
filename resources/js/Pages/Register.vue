<script setup>
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { ArrowRight, LockKeyhole, UserPlus, Users, MapPin } from 'lucide-vue-next';

const props = defineProps({
    graduationSets: { type: Array, default: () => [] },
    branches: { type: Array, default: () => [] },
});

const showPassword = ref(false);
const selectedPhoto = ref(null);
const photoPreview = ref(null);
const onPhotoChange = (event) => { const file = event.target.files?.[0] ?? null; selectedPhoto.value = file; photoPreview.value = file ? URL.createObjectURL(file) : null; form.profile_photo = file; };
const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    profile_photo: null,
    whatsapp_phone: '',
    sms_consent: false,
    whatsapp_consent: false,
    graduation_set_id: '',
    branch_id: '',
    city: '',
    country: 'Nigeria',
    password: '',
    password_confirmation: '',
});

const matchingBranchHint = computed(() => {
    const city = form.city.trim().toLowerCase();
    const country = form.country.trim().toLowerCase();
    if (!city || !country) return [];
    return props.branches.filter(branch =>
        (branch.cities || []).some(item =>
            item.city.toLowerCase() === city && item.country.toLowerCase() === country
        )
    );
});

const submit = () => form.post('/register');
</script>

<template>
<div class="min-h-screen bg-[#F7F9F7]">
    <div class="mx-auto flex min-h-screen max-w-[1100px] items-center justify-center px-5 py-10">
        <div class="w-full max-w-3xl rounded-2xl border border-gray-200 bg-white p-7 shadow-xl sm:p-10">
            <a href="/" class="text-xs font-bold uppercase tracking-[0.18em] text-[#006B3C]">← Back to BBHS</a>
            <div class="mt-8">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#006B3C]">Member Registration</p>
                <h1 class="mt-2 text-3xl font-extrabold text-[#00351F] sm:text-4xl">Join the Brotherhood</h1>
                <p class="mt-2 text-sm text-gray-500">Your graduation set is compulsory. Branch affiliation is optional.</p>
            </div>

            <form class="mt-8 grid gap-5 sm:grid-cols-2" @submit.prevent="submit">
                <div>
                    <input v-model="form.first_name" required class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="First name" />
                    <p v-if="form.errors.first_name" class="mt-1 text-xs text-red-600">{{ form.errors.first_name }}</p>
                </div>
                <div>
                    <input v-model="form.last_name" required class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Last name" />
                    <p v-if="form.errors.last_name" class="mt-1 text-xs text-red-600">{{ form.errors.last_name }}</p>
                </div>
                <div>
                    <input v-model="form.email" required type="email" class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Email address" />
                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                </div>
                <div>
                    <input v-model="form.phone" class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Primary phone number (required)" required />
                    <p v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}</p>
                </div>
                <div class="sm:col-span-2 rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <label class="mb-2 block text-sm font-bold text-[#00351F]">Profile photograph <span class="font-normal text-gray-500">(optional)</span></label>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                        <img v-if="photoPreview" :src="photoPreview" alt="Profile photo preview" class="h-20 w-20 rounded-full object-cover ring-2 ring-[#D4A72C]" />
                        <div v-else class="flex h-20 w-20 items-center justify-center rounded-full bg-white text-3xl text-gray-300"><UserPlus :size="30" /></div>
                        <div class="flex-1">
                            <input type="file" accept="image/jpeg,image/png,image/webp" @change="onPhotoChange" class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-[#006B3C] file:px-4 file:py-2 file:font-semibold file:text-white" />
                            <p class="mt-1 text-xs text-gray-500">JPG, PNG or WebP. Maximum 5 MB.</p>
                            <p v-if="form.errors.profile_photo" class="mt-1 text-xs text-red-600">{{ form.errors.profile_photo }}</p>
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1.5 flex items-center gap-2 text-xs font-bold text-gray-600"><Users :size="15" /> Graduation set (required)</label>
                    <select v-model="form.graduation_set_id" required class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm">
                        <option value="">Select your graduation set</option>
                        <option v-for="set in graduationSets" :key="set.id" :value="set.id">{{ set.name }} — {{ set.graduation_year }}</option>
                    </select>
                    <p v-if="form.errors.graduation_set_id" class="mt-1 text-xs text-red-600">{{ form.errors.graduation_set_id }}</p>
                </div>

                <div>
                    <input v-model="form.whatsapp_phone" class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="WhatsApp number (if different, optional)" />
                </div>
                <div class="sm:col-span-2 rounded-lg bg-[#F7F9F7] p-4">
                    <p class="text-sm font-bold text-[#00351F]">Communication preferences (optional)</p>
                    <label class="mt-2 flex items-start gap-2 text-xs text-gray-600"><input v-model="form.whatsapp_consent" type="checkbox" class="mt-0.5"/> I agree to receive BBHS WhatsApp updates and reminders.</label>
                    <label class="mt-2 flex items-start gap-2 text-xs text-gray-600"><input v-model="form.sms_consent" type="checkbox" class="mt-0.5"/> I agree to receive BBHS SMS updates and reminders.</label>
                    <p class="mt-2 text-xs text-gray-500">You can change these preferences later. Essential account and payment notices are handled separately.</p>
                </div>
                <div>
                    <label class="mb-1.5 flex items-center gap-2 text-xs font-bold text-gray-600"><MapPin :size="15" /> Current city (required)</label>
                    <input v-model="form.city" required class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="e.g. Lagos" />
                    <p v-if="form.errors.city" class="mt-1 text-xs text-red-600">{{ form.errors.city }}</p>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-bold text-gray-600">Country (required)</label>
                    <input v-model="form.country" required class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Country" />
                    <p v-if="form.errors.country" class="mt-1 text-xs text-red-600">{{ form.errors.country }}</p>
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-xs font-bold text-gray-600">Join a geographical branch (optional)</label>
                    <select v-model="form.branch_id" class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm">
                        <option value="">Do not request formal branch membership</option>
                        <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
                    </select>
                    <p v-if="form.errors.branch_id" class="mt-1 text-xs text-red-600">{{ form.errors.branch_id }}</p>
                    <p class="mt-2 rounded-lg bg-[#F1F7F3] p-3 text-xs leading-5 text-gray-600">
                        Branch membership is optional. If your registered city matches a branch location, you may still see that branch's events even if you do not formally join it.
                    </p>
                    <p v-if="matchingBranchHint.length" class="mt-2 text-xs font-semibold text-[#006B3C]">
                        Your city matches: {{ matchingBranchHint.map(item => item.name).join(', ') }}. You will be able to see relevant local branch activities.
                    </p>
                </div>

                <div class="relative sm:col-span-2">
                    <LockKeyhole :size="17" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                    <input v-model="form.password" required :type="showPassword ? 'text' : 'password'" class="w-full rounded-lg border-gray-200 py-3 pl-10 pr-16 text-sm" placeholder="Create password" />
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-500" @click="showPassword = !showPassword">{{ showPassword ? 'Hide' : 'Show' }}</button>
                    <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
                </div>

                <input v-model="form.password_confirmation" required type="password" class="rounded-lg border-gray-200 px-4 py-3 text-sm sm:col-span-2" placeholder="Confirm password" />

                <label class="flex gap-2 text-sm text-gray-600 sm:col-span-2">
                    <input type="checkbox" required class="mt-1 rounded border-gray-300 text-[#006B3C]" />
                    I agree to the BBHS Old Boys Association membership terms and privacy policy.
                </label>

                <button :disabled="form.processing" type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#006B3C] px-6 py-3.5 text-sm font-extrabold text-white disabled:opacity-60 sm:col-span-2">
                    <UserPlus :size="16" /> {{ form.processing ? 'CREATING ACCOUNT...' : 'CREATE MEMBER ACCOUNT' }} <ArrowRight :size="16" />
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Already registered?
                <a href="/login" class="font-bold text-[#006B3C]">Sign in</a>
            </p>
        </div>
    </div>
</div>
</template>
