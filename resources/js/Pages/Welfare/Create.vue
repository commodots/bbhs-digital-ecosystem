<script setup>
import PublicLayout from '../../Layouts/PublicLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
defineProps({ categories: { type: Array, default: () => [] } });
const form = useForm({ category: '', subject: '', description: '', amount_requested: '', attachments: [] });
const submit = () => form.post('/welfare', { forceFormData: true });
const filesChanged = e => form.attachments = Array.from(e.target.files || []);
</script>
<template>
<PublicLayout>
<section class="bg-[#00351F]"><div class="mx-auto max-w-[900px] px-5 py-10 lg:px-8"><p class="text-xs font-bold uppercase tracking-widest text-[#D4A72C]">Confidential member service</p><h1 class="mt-2 text-3xl font-extrabold text-white">Welfare Application</h1><p class="mt-2 text-sm text-white/70">Your application and supporting documents are private and visible only to you and authorised administrators.</p></div></section>
<section class="bg-[#F7F9F7] py-10"><div class="mx-auto max-w-[900px] px-5 lg:px-8"><form @submit.prevent="submit" class="space-y-5 rounded-2xl border bg-white p-6 shadow-sm sm:p-8">
<div><label class="mb-1 block text-sm font-bold">Request category</label><select v-model="form.category" required class="w-full rounded-lg border-gray-300"><option value="" disabled>Select category</option><option v-for="item in categories" :key="item">{{ item }}</option></select><p v-if="form.errors.category" class="mt-1 text-sm text-red-600">{{ form.errors.category }}</p></div>
<div><label class="mb-1 block text-sm font-bold">Subject</label><input v-model="form.subject" required maxlength="255" class="w-full rounded-lg border-gray-300" placeholder="Brief description of your request"/><p v-if="form.errors.subject" class="mt-1 text-sm text-red-600">{{ form.errors.subject }}</p></div>
<div><label class="mb-1 block text-sm font-bold">Explain your request</label><textarea v-model="form.description" required minlength="20" rows="7" class="w-full rounded-lg border-gray-300" placeholder="Please provide relevant details to help the welfare committee review your application."></textarea><p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p></div>
<div><label class="mb-1 block text-sm font-bold">Amount requested (₦, optional)</label><input v-model="form.amount_requested" type="number" min="0" step="0.01" class="w-full rounded-lg border-gray-300" placeholder="e.g. 150000"/></div>
<div><label class="mb-1 block text-sm font-bold">Supporting documents (optional)</label><input type="file" multiple accept=".pdf,.jpg,.jpeg,.png,.webp" @change="filesChanged" class="w-full rounded-lg border border-gray-300 p-3 text-sm"/><p class="mt-1 text-xs text-gray-500">Up to 5 files, 5MB each. PDF or image formats.</p><p v-if="form.errors.attachments" class="mt-1 text-sm text-red-600">{{ form.errors.attachments }}</p></div>
<div class="flex flex-wrap gap-3"><button :disabled="form.processing" class="rounded-lg bg-[#006B3C] px-6 py-3 text-sm font-bold text-white disabled:opacity-50">Submit confidential application</button><Link href="/welfare" class="rounded-lg border px-6 py-3 text-sm font-bold text-gray-700">Cancel</Link></div>
</form></div></section>
</PublicLayout>
</template>
