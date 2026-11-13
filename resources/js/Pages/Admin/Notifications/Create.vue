<script setup>
import { computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import AdminQuickLinks from '../../../Components/AdminQuickLinks.vue';
import { Send, BellRing } from 'lucide-vue-next';
const props = defineProps({ sets: { type: Array, default: () => [] }, branches: { type: Array, default: () => [] } });
const page = usePage();
const form = useForm({ title: '', message: '', category: 'announcement', audience: 'all', scope_id: '' });
const needsScope = computed(() => ['set', 'branch'].includes(form.audience));
const scopeOptions = computed(() => form.audience === 'set' ? props.sets : props.branches);
const submit = () => form.post('/admin/notifications', { preserveScroll: true, onSuccess: () => form.reset('title', 'message', 'scope_id') });
</script>
<template>
<PublicLayout>
    <section class="bg-[#00351F]"><div class="w-full px-6 py-10 lg:px-6"><p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">Super Admin</p><h1 class="mt-2 flex items-center gap-3 text-3xl font-extrabold text-white"><BellRing :size="27" /> Send a notification</h1><p class="mt-2 text-sm text-white/75">Publish an announcement or targeted update to BBHS users.</p></div><AdminQuickLinks />
    </section>
    <section class="bg-[#F7F9F7] py-10"><div class="w-full px-6 lg:px-6"><div v-if="page.props.flash?.success" class="mb-5 rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ page.props.flash.success }}</div>
        <form @submit.prevent="submit" class="space-y-5 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div><label class="mb-2 block text-sm font-bold text-gray-700">Notification title</label><input v-model="form.title" maxlength="160" required class="w-full rounded-lg border-gray-300" placeholder="e.g. Annual General Meeting Announcement"/><p v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p></div>
            <div><label class="mb-2 block text-sm font-bold text-gray-700">Message</label><textarea v-model="form.message" rows="5" maxlength="3000" required class="w-full rounded-lg border-gray-300" placeholder="Write the notification message..."></textarea><p v-if="form.errors.message" class="mt-1 text-xs text-red-600">{{ form.errors.message }}</p></div>
            <div class="grid gap-4 md:grid-cols-2"><div><label class="mb-2 block text-sm font-bold text-gray-700">Category</label><select v-model="form.category" class="w-full rounded-lg border-gray-300"><option value="announcement">Announcement</option><option value="event">Event</option><option value="membership">Membership</option><option value="general">General</option></select></div><div><label class="mb-2 block text-sm font-bold text-gray-700">Recipients</label><select v-model="form.audience" class="w-full rounded-lg border-gray-300"><option value="all">All registered users</option><option value="members">Members</option><option value="administrators">Administrators</option><option value="set">One graduation set</option><option value="branch">One branch (approved affiliates)</option></select></div></div>
            <div v-if="needsScope"><label class="mb-2 block text-sm font-bold text-gray-700">{{ form.audience === 'set' ? 'Graduation set' : 'Branch' }}</label><select v-model="form.scope_id" required class="w-full rounded-lg border-gray-300"><option value="">Select {{ form.audience === 'set' ? 'set' : 'branch' }}</option><option v-for="item in scopeOptions" :key="item.id" :value="item.id">{{ item.name }}</option></select><p v-if="form.errors.scope_id" class="mt-1 text-xs text-red-600">{{ form.errors.scope_id }}</p></div>
            <p class="rounded-lg bg-amber-50 p-3 text-xs leading-5 text-amber-800">Notifications are saved in each recipient's BBHS notification centre. Email delivery can be enabled separately after SMTP testing.</p>
            <div class="flex justify-end"><button :disabled="form.processing" class="inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-6 py-3 text-sm font-bold text-white disabled:opacity-50"><Send :size="16" /> {{ form.processing ? 'SENDING...' : 'SEND NOTIFICATION' }}</button></div>
        </form>
    </div></section>
</PublicLayout>
</template>
