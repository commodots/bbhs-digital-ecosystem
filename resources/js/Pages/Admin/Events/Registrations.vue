<script setup>
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import AdminQuickLinks from '../../../Components/AdminQuickLinks.vue';
import { ArrowLeft, Download, Users } from 'lucide-vue-next';

defineProps({
    event: { type: Object, required: true },
    registrations: { type: Object, default: () => ({ data: [] }) },
    registeredCount: { type: Number, default: 0 },
    cancelledCount: { type: Number, default: 0 },
});
</script>

<template>
    <PublicLayout>
        <section class="bg-[#00351F]">
            <div class="mx-auto max-w-[1200px] px-5 py-12 lg:px-8">
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">Administration</p>
                <h1 class="mt-2 text-3xl font-extrabold text-white">Event Registrations</h1>
                <p class="mt-2 text-sm text-white/70">{{ event.title }}</p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <a :href="`/admin/events/${event.id}/attendance`" class="rounded-lg bg-white px-4 py-2 text-sm font-bold text-[#00351F]">Attendance & minutes</a>
                    <a :href="`/admin/events/${event.id}/edit`" class="rounded-lg border border-white/30 px-4 py-2 text-sm font-semibold text-white">Edit event</a>
                    <a href="/admin/events" class="rounded-lg border border-white/30 px-4 py-2 text-sm font-semibold text-white">All events</a>
                </div>
            </div>
            <AdminQuickLinks />
        </section>

        <section class="bg-[#F7F9F7] py-10">
            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">
                <div class="mb-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border bg-white p-5">
                        <p class="text-sm text-gray-500">Confirmed registrations</p>
                        <p class="mt-1 text-3xl font-extrabold text-[#00351F]">{{ registeredCount }}</p>
                    </div>
                    <div class="rounded-xl border bg-white p-5">
                        <p class="text-sm text-gray-500">Cancelled registrations</p>
                        <p class="mt-1 text-3xl font-extrabold text-gray-700">{{ cancelledCount }}</p>
                    </div>
                </div>

                <div class="mb-5 flex justify-end">
                    <a :href="`/admin/events/${event.id}/registrations/export`" class="inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-4 py-3 text-sm font-bold text-white">
                        <Download :size="16" /> Export CSV
                    </a>
                </div>

                <div class="overflow-hidden rounded-xl border bg-white">
                    <div v-if="!registrations.data?.length" class="p-10 text-center text-sm text-gray-500">No confirmed registrations yet.</div>
                    <table v-else class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-5 py-4">Member</th><th class="px-5 py-4">Email</th><th class="px-5 py-4">Registered</th></tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="item in registrations.data" :key="item.id">
                                <td class="px-5 py-4 font-semibold text-[#00351F]">{{ item.user?.name || 'Member account unavailable' }}</td>
                                <td class="px-5 py-4 text-gray-600">{{ item.user?.email || '—' }}</td>
                                <td class="px-5 py-4 text-gray-600">{{ item.created_at }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="registrations.links?.length > 3" class="mt-5 flex flex-wrap gap-2">
                    <a v-for="link in registrations.links" :key="link.label" :href="link.url || '#'" v-html="link.label" :class="['rounded border px-3 py-2 text-xs', link.active ? 'bg-[#00351F] text-white' : 'bg-white text-gray-600', !link.url ? 'pointer-events-none opacity-40' : '']" />
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
