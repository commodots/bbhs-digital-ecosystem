<script setup>
import { router, usePage } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import { Bell, CheckCheck, Trash2, Inbox, ArrowRight } from 'lucide-vue-next';

const props = defineProps({ notifications: { type: Object, default: () => ({ data: [], links: [], total: 0 }) }, filter: { type: String, default: 'all' } });
const page = usePage();
const applyFilter = value => router.get('/notifications', { filter: value }, { preserveState: true, replace: true });
const markAllRead = () => router.post('/notifications/read-all', {}, { preserveScroll: true });
const markRead = id => router.post(`/notifications/${id}/read`, {}, { preserveScroll: true });
const remove = id => { if (confirm('Delete this notification?')) router.delete(`/notifications/${id}`, { preserveScroll: true }); };
const categoryLabel = category => ({ membership: 'Membership', event: 'Events', announcement: 'Announcement', general: 'General' }[category] || 'General');
const dateLabel = value => value ? new Date(value).toLocaleString() : '';
</script>
<template>
<PublicLayout>
    <section class="bg-[#00351F]"><div class="mx-auto max-w-6xl px-5 py-10 lg:px-8"><p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">Member Portal</p><h1 class="mt-2 flex items-center gap-3 text-3xl font-extrabold text-white"><Bell :size="28" /> Notifications</h1><p class="mt-2 text-sm text-white/75">Your BBHS membership, administration and community updates.</p></div></section>
    <section class="min-h-[55vh] bg-[#F7F9F7] py-9"><div class="mx-auto max-w-6xl space-y-5 px-5 lg:px-8">
        <div v-if="page.props.flash?.success" class="rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ page.props.flash.success }}</div>
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-gray-200 bg-white p-4"><div class="flex gap-2"><button @click="applyFilter('all')" class="rounded-lg px-4 py-2 text-sm font-bold" :class="filter === 'all' ? 'bg-[#006B3C] text-white' : 'bg-gray-100 text-gray-600'">All</button><button @click="applyFilter('unread')" class="rounded-lg px-4 py-2 text-sm font-bold" :class="filter === 'unread' ? 'bg-[#006B3C] text-white' : 'bg-gray-100 text-gray-600'">Unread</button></div><button v-if="notifications.data?.some(item => !item.read_at)" @click="markAllRead" class="inline-flex items-center gap-2 text-sm font-bold text-[#006B3C]"><CheckCheck :size="16" /> Mark all as read</button></div>
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white"><div v-if="!notifications.data?.length" class="p-14 text-center"><Inbox class="mx-auto text-gray-300" :size="42" /><h2 class="mt-3 font-bold text-[#00351F]">You're all caught up</h2><p class="mt-1 text-sm text-gray-500">There are no notifications to display.</p></div>
            <div v-else class="divide-y divide-gray-100"><article v-for="item in notifications.data" :key="item.id" class="flex gap-4 p-5" :class="!item.read_at ? 'bg-emerald-50/40' : ''"><span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full" :class="!item.read_at ? 'bg-[#D4A72C]' : 'bg-gray-200'"></span><div class="min-w-0 flex-1"><div class="flex flex-wrap items-center gap-2"><h2 class="font-bold text-[#00351F]">{{ item.data?.title || 'BBHS Notification' }}</h2><span class="rounded-full bg-gray-100 px-2 py-1 text-[10px] font-bold uppercase text-gray-500">{{ categoryLabel(item.data?.category) }}</span></div><p class="mt-1 whitespace-pre-line text-sm leading-6 text-gray-600">{{ item.data?.message }}</p><p class="mt-2 text-xs text-gray-400">{{ dateLabel(item.created_at) }}</p><div class="mt-3 flex flex-wrap gap-4 text-xs font-bold"><a :href="`/notifications/${item.id}/open`" class="inline-flex items-center gap-1 text-[#006B3C]">Open <ArrowRight :size="13" /></a><button v-if="!item.read_at" @click="markRead(item.id)" class="text-gray-500 hover:text-[#006B3C]">Mark as read</button><button @click="remove(item.id)" class="inline-flex items-center gap-1 text-red-600"><Trash2 :size="13" /> Delete</button></div></div></article></div>
        </div>
        <div v-if="notifications.links?.length > 3" class="flex flex-wrap justify-center gap-2"><a v-for="link in notifications.links" :key="link.label" :href="link.url || '# '" @click.prevent="link.url && router.visit(link.url)" class="rounded-lg border px-3 py-2 text-sm" :class="link.active ? 'border-[#006B3C] bg-[#006B3C] text-white' : 'border-gray-200 bg-white text-gray-600'" v-html="link.label"></a></div>
    </div></section>
</PublicLayout>
</template>
