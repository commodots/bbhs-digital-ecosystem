<script setup>
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import AdminQuickLinks from '../../../Components/AdminQuickLinks.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    scope: Object, summary: Object, bySet: Array, byBranch: Array,
    trend: Array, filters: Object, availableScopes: Object,
});
const scopeType = ref(props.filters.scope_type || 'global');
const scopeId = ref(props.filters.scope_id ? String(props.filters.scope_id) : '');
function applyScope() {
    router.get('/admin/membership-reports', {
        scope_type: scopeType.value,
        scope_id: scopeType.value === 'global' ? '' : scopeId.value,
    }, { preserveState: true, replace: true });
}
function exportCsv() {
    const params = new URLSearchParams({ scope_type: scopeType.value, scope_id: scopeType.value === 'global' ? '' : scopeId.value });
    window.location.href = '/admin/membership-reports/export?' + params.toString();
}
const cards = [
    { key: 'total', label: 'Total members', color: 'text-[#00351F]' },
    { key: 'approved', label: 'Approved members', color: 'text-green-700' },
    { key: 'pending', label: 'Pending members', color: 'text-amber-700' },
    { key: 'pending_verifications', label: 'Pending verification items', color: 'text-orange-700' },
];
</script>

<template>
<PublicLayout>
    <Head title="Membership Reports" />
    <section class="bg-[#00351F]">
        <div class="mx-auto max-w-[1400px] px-5 py-8">
            <p class="text-xs font-bold uppercase tracking-widest text-[#D4A72C]">Administration</p>
            <h1 class="mt-2 text-3xl font-extrabold text-white">Membership Reports</h1>
            <p class="mt-2 text-sm text-white/75">{{ scope.label }} · Membership distribution, approval status and registration trends.</p>
        </div>
        <AdminQuickLinks />
    </section>

    <main class="mx-auto max-w-7xl px-5 py-8">
        <div class="flex flex-wrap items-end justify-between gap-3 rounded-xl border bg-white p-4">
            <div v-if="availableScopes?.sets" class="flex flex-wrap gap-3">
                <div>
                    <label class="mb-1 block text-xs font-bold text-gray-500">Report scope</label>
                    <select v-model="scopeType" class="rounded-lg border-gray-300 text-sm" @change="scopeId = ''">
                        <option value="global">All members</option>
                        <option value="set">Graduation set</option>
                        <option value="branch">Branch</option>
                    </select>
                </div>
                <div v-if="scopeType !== 'global'">
                    <label class="mb-1 block text-xs font-bold text-gray-500">{{ scopeType === 'set' ? 'Graduation set' : 'Branch' }}</label>
                    <select v-model="scopeId" class="min-w-48 rounded-lg border-gray-300 text-sm">
                        <option value="">Select {{ scopeType }}</option>
                        <option v-for="item in (scopeType === 'set' ? availableScopes.sets : availableScopes.branches)" :key="item.id" :value="String(item.id)">{{ item.name }}</option>
                    </select>
                </div>
                <button @click="applyScope" :disabled="scopeType !== 'global' && !scopeId" class="rounded-lg bg-[#006B3C] px-4 py-2 text-sm font-bold text-white disabled:opacity-40">Apply</button>
            </div>
            <div class="ml-auto">
                <button @click="exportCsv" class="rounded-lg border px-4 py-2 text-sm font-bold text-gray-700">Export Members CSV</button>
            </div>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div v-for="card in cards" :key="card.key" class="rounded-xl border bg-white p-5">
                <p class="text-sm text-gray-500">{{ card.label }}</p>
                <p class="mt-2 text-3xl font-extrabold" :class="card.color">{{ Number(summary[card.key] || 0).toLocaleString() }}</p>
            </div>
        </div>

        <div class="mt-6 grid gap-5 lg:grid-cols-2">
            <section class="rounded-xl border bg-white p-5">
                <h2 class="font-extrabold text-[#00351F]">Members by graduation set</h2>
                <div class="mt-4 divide-y">
                    <div v-for="item in bySet" :key="item.label" class="flex items-center justify-between gap-3 py-3 text-sm">
                        <span class="font-semibold text-gray-700">{{ item.label }}</span>
                        <span class="font-extrabold text-[#00351F]">{{ Number(item.total).toLocaleString() }}</span>
                    </div>
                    <p v-if="!bySet.length" class="py-4 text-sm text-gray-500">No membership records found for this scope.</p>
                </div>
            </section>
            <section class="rounded-xl border bg-white p-5">
                <h2 class="font-extrabold text-[#00351F]">Members by branch</h2>
                <div class="mt-4 divide-y">
                    <div v-for="item in byBranch" :key="item.label" class="flex items-center justify-between gap-3 py-3 text-sm">
                        <span class="font-semibold text-gray-700">{{ item.label }}</span>
                        <span class="font-extrabold text-[#00351F]">{{ Number(item.total).toLocaleString() }}</span>
                    </div>
                    <p v-if="!byBranch.length" class="py-4 text-sm text-gray-500">No branch affiliations found for this scope.</p>
                </div>
            </section>
        </div>

        <section class="mt-6 rounded-xl border bg-white p-5">
            <h2 class="font-extrabold text-[#00351F]">New registrations – last 12 months</h2>
            <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-6">
                <div v-for="item in trend" :key="item.month" class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">{{ item.month }}</p>
                    <p class="mt-1 text-xl font-extrabold text-[#006B3C]">{{ Number(item.total).toLocaleString() }}</p>
                </div>
            </div>
            <p class="mt-3 text-xs text-gray-500">Registration totals are based on account creation dates. Branch totals include pending and approved branch affiliations.</p>
        </section>
    </main>
</PublicLayout>
</template>
