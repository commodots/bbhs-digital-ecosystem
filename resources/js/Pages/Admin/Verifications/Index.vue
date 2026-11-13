<script setup>
import { router, useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import AdminQuickLinks from '../../../Components/AdminQuickLinks.vue';
import { CheckCircle2, XCircle, ShieldCheck, Filter, MapPin, Users, BellRing } from 'lucide-vue-next';

const props = defineProps({
    verifications: { type: Object, default: () => ({ data: [], links: [], total: 0 }) },
    scopes: { type: Object, default: () => ({ sets: [], branches: [] }) },
    filters: { type: Object, default: () => ({}) },
    isGlobalAdmin: { type: Boolean, default: false },
    reminderGroups: { type: Array, default: () => [] },
});

const page = usePage();
const filterForm = useForm({
    scope_type: props.filters.scope_type || '',
    scope_id: props.filters.scope_id || '',
});

const scopeOptions = () => filterForm.scope_type === 'set' ? props.scopes.sets : props.scopes.branches;

const applyFilters = () => router.get('/admin/verifications', filterForm.data(), {
    preserveState: true,
    replace: true,
});

const sendReminder = group => {
    const typeLabel = group.scope_type === 'set' ? 'graduation set' : 'branch';
    if (group.cooldown_active) return;
    if (confirm(`Send a reminder to the assigned executives for ${group.scope_name}? There are ${group.pending_count} pending registrations.`)) {
        router.post('/admin/verifications/remind', { scope_type: group.scope_type, scope_id: group.scope_id }, { preserveScroll: true });
    }
};

const approve = id => {
    if (confirm('Approve this verification?')) {
        router.post(`/admin/verifications/${id}/approve`, {}, { preserveScroll: true });
    }
};

const reject = id => {
    const notes = window.prompt('Please provide a reason for rejecting this verification:');
    if (notes && notes.trim()) {
        router.post(`/admin/verifications/${id}/reject`, { review_notes: notes.trim() }, { preserveScroll: true });
    }
};

const location = profile => [profile?.city, profile?.country].filter(Boolean).join(', ') || 'Location not supplied';
</script>

<template>
<PublicLayout>
    <section class="bg-[#00351F]">
        <div class="mx-auto max-w-[1400px] px-5 py-10 lg:px-8">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">Membership Administration</p>
            <h1 class="mt-2 text-3xl font-extrabold text-white sm:text-4xl">Pending Verifications</h1>
            <p class="mt-2 text-sm text-white/75">Review graduation set membership and local branch verification separately.</p>
        </div>
    <AdminQuickLinks />
    </section>

    <section class="bg-[#F7F9F7] py-9">
        <div class="mx-auto max-w-[1400px] space-y-6 px-5 lg:px-8">
            <div v-if="page.props.flash?.success" class="rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ page.props.flash.success }}</div>
            <div v-if="page.props.flash?.error" class="rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ page.props.flash.error }}</div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5">
                <div class="mb-4 flex items-center gap-2 font-extrabold text-[#00351F]"><Filter :size="18" /> Filter verification queue</div>
                <form class="grid gap-3 md:grid-cols-[1fr_1fr_auto]" @submit.prevent="applyFilters">
                    <select v-model="filterForm.scope_type" class="rounded-lg border-gray-200 px-3 py-3 text-sm">
                        <option value="">All my scopes</option>
                        <option value="set">Graduation sets</option>
                        <option value="branch">Branches</option>
                    </select>
                    <select v-model="filterForm.scope_id" class="rounded-lg border-gray-200 px-3 py-3 text-sm">
                        <option value="">All {{ filterForm.scope_type === 'set' ? 'sets' : filterForm.scope_type === 'branch' ? 'branches' : 'scopes' }}</option>
                        <option v-for="scope in scopeOptions()" :key="scope.id" :value="scope.id">{{ scope.name }}</option>
                    </select>
                    <button class="rounded-lg bg-[#006B3C] px-6 py-3 text-sm font-bold text-white">APPLY FILTER</button>
                </form>
                <p class="mt-3 text-xs text-gray-500" v-if="!isGlobalAdmin">You can only see registrations for the set(s) or branch(es) where you hold an active executive appointment.</p>
                <p class="mt-3 text-xs text-gray-500" v-else>Global administrator view: all pending set and branch verifications.</p>
            </div>

            <div v-if="isGlobalAdmin && reminderGroups.length" class="rounded-2xl border border-amber-200 bg-white p-5">
                <div class="mb-4 flex items-start gap-3">
                    <div class="rounded-xl bg-amber-50 p-2 text-amber-700"><BellRing :size="20" /></div>
                    <div><h2 class="font-extrabold text-[#00351F]">Executive follow-up reminders</h2><p class="mt-1 text-sm text-gray-500">Send each responsible Set or Branch executive a summary of their outstanding registrations. Reminders do not change application status.</p></div>
                </div>
                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                    <div v-for="group in reminderGroups" :key="`${group.scope_type}-${group.scope_id}`" class="rounded-xl border border-gray-200 p-4">
                        <div class="flex items-center justify-between gap-2">
                            <div><p class="text-xs font-bold uppercase tracking-wide text-gray-400">{{ group.scope_type === 'set' ? 'Graduation set' : 'Branch' }}</p><h3 class="mt-1 font-extrabold text-[#00351F]">{{ group.scope_name }}</h3></div>
                            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-800">{{ group.pending_count }} pending</span>
                        </div>
                        <p class="mt-3 text-xs text-gray-500">{{ group.recipient_count }} active executive recipient(s)</p>
                        <p class="mt-1 text-xs text-gray-400" v-if="group.recipient_names?.length">{{ group.recipient_names.join(', ') }}</p>
                        <p class="mt-2 text-xs text-gray-400" v-if="group.last_reminder_at">Last reminder: {{ new Date(group.last_reminder_at).toLocaleString() }}</p>
                        <button type="button" class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-xs font-bold text-white disabled:cursor-not-allowed disabled:bg-gray-300" :class="group.cooldown_active || !group.recipient_count ? 'bg-gray-300' : 'bg-[#006B3C] hover:bg-[#00552f]'" :disabled="group.cooldown_active || !group.recipient_count" @click="sendReminder(group)">
                            <BellRing :size="14" /> {{ group.cooldown_active ? 'REMINDER SENT (24H)' : !group.recipient_count ? 'NO EXECUTIVE ASSIGNED' : 'SEND REMINDER' }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
                <div class="flex items-center justify-between border-b border-gray-100 p-5">
                    <div><h2 class="font-extrabold text-[#00351F]">Pending applications</h2><p class="mt-1 text-xs text-gray-500">{{ verifications.total ?? verifications.data?.length ?? 0 }} pending records</p></div>
                    <ShieldCheck class="text-[#006B3C]" :size="24" />
                </div>

                <div v-if="!verifications.data?.length" class="p-12 text-center">
                    <CheckCircle2 class="mx-auto text-green-600" :size="36" />
                    <p class="mt-3 font-bold text-[#00351F]">No pending verifications</p>
                    <p class="mt-1 text-sm text-gray-500">New registrations requiring your review will appear here.</p>
                </div>

                <div v-else class="divide-y divide-gray-100">
                    <article v-for="item in verifications.data" :key="item.id" class="p-5">
                        <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-start">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-extrabold text-[#00351F]">{{ item.user?.name }}</h3>
                                    <span class="rounded-full px-2.5 py-1 text-[10px] font-extrabold uppercase"
                                        :class="item.verification_type === 'set' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700'">
                                        {{ item.verification_type === 'set' ? 'Set approval' : 'Branch verification' }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">{{ item.user?.email }}</p>
                                <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-xs text-gray-600">
                                    <span class="inline-flex items-center gap-1"><Users :size="13" /> {{ item.user?.alumni_profile?.graduation_set?.name || item.user?.alumni_profile?.class_set || 'Set not recorded' }}</span>
                                    <span class="inline-flex items-center gap-1"><MapPin :size="13" /> {{ location(item.user?.alumni_profile) }}</span>
                                    <span class="font-semibold text-[#006B3C]">{{ item.scope_name }}</span>
                                </div>
                                <p class="mt-2 text-xs text-gray-400">Registered {{ new Date(item.created_at).toLocaleString() }}</p>
                            </div>
                            <div class="flex shrink-0 flex-wrap gap-2">
                                <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-4 py-2.5 text-xs font-bold text-white" @click="approve(item.id)">
                                    <CheckCircle2 :size="14" /> {{ item.verification_type === 'set' ? 'APPROVE MEMBER' : 'VERIFY LOCAL MEMBER' }}
                                </button>
                                <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-red-200 px-4 py-2.5 text-xs font-bold text-red-700" @click="reject(item.id)">
                                    <XCircle :size="14" /> REJECT
                                </button>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
</PublicLayout>
</template>
