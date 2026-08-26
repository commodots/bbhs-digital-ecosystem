<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { Mail, MapPin, ArrowRight, Clock3 } from 'lucide-vue-next';

const page = usePage();
const form = useForm({ name: '', email: '', phone: '', subject: '', message: '' });
const submit = () => form.post('/contact');
</script>

<template>
<PublicLayout>
<section class="bg-[#00351F]"><div class="mx-auto flex min-h-[350px] max-w-[1440px] items-center px-5 py-16 lg:px-10"><div class="max-w-3xl"><p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F1D36A]">Get In Touch</p><h1 class="mt-4 text-4xl font-extrabold text-white sm:text-5xl lg:text-6xl">Let's Stay <span class="text-[#16A34A]">Connected.</span></h1><p class="mt-5 max-w-2xl text-base leading-7 text-white/80 sm:text-lg">Have a question, membership enquiry, project idea or message for the BBHS Old Boys Association?</p></div></div></section>

<section class="bg-white py-12 lg:py-14">
<div class="mx-auto max-w-[1200px] px-5 lg:px-8">
<div class="grid gap-8 lg:grid-cols-[.8fr_1.2fr]">
<div>
<p class="text-xs font-bold uppercase tracking-[0.22em] text-[#006B3C]">Contact BBHS OBA</p>
<h2 class="mt-2 text-3xl font-extrabold text-[#00351F]">We'd Love to Hear From You.</h2>
<div class="mt-7 space-y-5">
<div class="flex gap-4"><MapPin class="shrink-0 text-[#006B3C]" :size="20" /><div><b class="text-[#00351F]">Location</b><p class="mt-1 text-sm text-gray-500">Abeokuta, Ogun State, Nigeria</p></div></div>
<div class="flex gap-4"><Mail class="shrink-0 text-[#006B3C]" :size="20" /><div><b class="text-[#00351F]">Email</b><p class="mt-1 text-sm text-gray-500">Association contact details will be configured here.</p></div></div>
<div class="flex gap-4"><Clock3 class="shrink-0 text-[#006B3C]" :size="20" /><div><b class="text-[#00351F]">Enquiries</b><p class="mt-1 text-sm text-gray-500">Membership, events, projects and general enquiries.</p></div></div>
</div>
</div>

<form class="rounded-2xl border border-gray-200 bg-[#F7F9F7] p-6 sm:p-8" @submit.prevent="submit">
<h3 class="text-2xl font-extrabold text-[#00351F]">Send a Message</h3>
<div v-if="page.props.flash?.success" class="mt-4 rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ page.props.flash.success }}</div>
<div class="mt-6 grid gap-4 sm:grid-cols-2">
<div><input v-model="form.name" class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Full name" /><p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p></div>
<div><input v-model="form.email" type="email" class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Email address" /><p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p></div>
<input v-model="form.phone" class="rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Phone number" />
<input v-model="form.subject" class="rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Subject" />
</div>
<textarea v-model="form.message" class="mt-4 min-h-[140px] w-full rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Your message"></textarea>
<p v-if="form.errors.message" class="mt-1 text-xs text-red-600">{{ form.errors.message }}</p>
<button :disabled="form.processing" type="submit" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-6 py-3 text-sm font-bold text-white disabled:opacity-60">{{ form.processing ? 'SENDING...' : 'SEND MESSAGE' }} <ArrowRight :size="16" /></button>
</form>
</div>
</div>
</section>
</PublicLayout>
</template>
