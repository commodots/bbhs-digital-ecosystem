<script setup>
import { computed, watch } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import AdminQuickLinks from '../../../Components/AdminQuickLinks.vue';
import { Network, Users, MapPin, ShieldCheck, UserCog, CalendarClock, XCircle } from 'lucide-vue-next';

const props = defineProps({
    sets: { type: Array, default: () => [] },
    branches: { type: Array, default: () => [] },
    positions: { type: Array, default: () => [] },
    members: { type: Array, default: () => [] },
    assignments: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({}) },
});

const page = usePage();

const appointment = useForm({
    scope_type: 'global',
    scope_id: '',
    executive_position_id: '',
    user_id: '',
    starts_at: '',
    ends_at: '',
    notes: '',
});

const branchForm = useForm({ name: '', description: '', city: '', country: 'Nigeria' });
const positionForm = useForm({ scope_type: 'global', name: '' });

const availablePositions = computed(() => props.positions.filter(position => position.scope_type === appointment.scope_type));
const availableScopes = computed(() => appointment.scope_type === 'set' ? props.sets : props.branches);

watch(() => appointment.scope_type, () => {
    appointment.scope_id = '';
    appointment.executive_position_id = '';
});

const submitAppointment = () => appointment.post('/admin/organisation/assignments', { preserveScroll: true, onSuccess: () => appointment.reset('user_id', 'starts_at', 'ends_at', 'notes') });
const submitBranch = () => branchForm.post('/admin/organisation/branches', { preserveScroll: true, onSuccess: () => branchForm.reset() });
const submitPosition = () => positionForm.post('/admin/organisation/positions', { preserveScroll: true, onSuccess: () => positionForm.reset('name') });
const endAppointment = id => {
    if (confirm('End this executive appointment? The appointment history will be retained.')) {
        router.delete(`/admin/organisation/assignments/${id}`, { preserveScroll: true });
    }
};

const scopeLabel = assignment => assignment.scope_name || (assignment.scope_type === 'global' ? 'Global BBHS' : '');
</script>

<template>
<PublicLayout>
    <section class="bg-[#00351F]">
        <div class="mx-auto max-w-[1400px] px-5 py-10 lg:px-8">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">Super Administration</p>
            <h1 class="mt-2 text-3xl font-extrabold text-white sm:text-4xl">Organisation & Executives</h1>
            <p class="mt-2 max-w-3xl text-sm text-white/75">Manage BBHS graduation sets, geographical branches and leadership appointments across the association.</p>
        </div>
    <AdminQuickLinks />
    </section>

    <section class="bg-[#F7F9F7] py-9">
        <div class="mx-auto max-w-[1400px] space-y-6 px-5 lg:px-8">
            <div v-if="page.props.flash?.success" class="rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ page.props.flash.success }}</div>
            <div v-if="page.props.flash?.error" class="rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ page.props.flash.error }}</div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <div v-for="card in [
                    { label: 'Graduation sets', value: stats.sets ?? 0, icon: Users },
                    { label: 'Branches', value: stats.branches ?? 0, icon: MapPin },
                    { label: 'Global executives', value: stats.global_executives ?? 0, icon: ShieldCheck },
                    { label: 'Set executives', value: stats.set_executives ?? 0, icon: UserCog },
                    { label: 'Branch executives', value: stats.branch_executives ?? 0, icon: Network },
                ]" :key="card.label" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <component :is="card.icon" :size="20" class="text-[#006B3C]" />
                    <p class="mt-4 text-2xl font-extrabold text-[#00351F]">{{ card.value }}</p>
                    <p class="mt-1 text-xs font-semibold text-gray-500">{{ card.label }}</p>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(360px,0.85fr)]">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6">
                    <div class="mb-5 flex items-start gap-3">
                        <div class="rounded-xl bg-[#EEF8F2] p-3 text-[#006B3C]"><UserCog :size="21" /></div>
                        <div><h2 class="font-extrabold text-[#00351F]">Appoint an Executive</h2><p class="mt-1 text-xs text-gray-500">Registered members can be appointed, including pending applicants. A set or branch appointment approves only the relevant affiliation.</p></div>
                    </div>
                    <form class="grid gap-4 sm:grid-cols-2" @submit.prevent="submitAppointment">
                        <label class="text-xs font-bold text-gray-600">Organisational level
                            <select v-model="appointment.scope_type" class="mt-1.5 w-full rounded-lg border-gray-200 px-3 py-3 text-sm font-normal">
                                <option value="global">Global BBHS</option><option value="set">Graduation set</option><option value="branch">Geographical branch</option>
                            </select>
                            <span v-if="appointment.errors.scope_type" class="mt-1 block text-xs text-red-600">{{ appointment.errors.scope_type }}</span>
                        </label>
                        <label v-if="appointment.scope_type !== 'global'" class="text-xs font-bold text-gray-600">Select {{ appointment.scope_type === 'set' ? 'graduation set' : 'branch' }}
                            <select v-model="appointment.scope_id" class="mt-1.5 w-full rounded-lg border-gray-200 px-3 py-3 text-sm font-normal">
                                <option value="">Choose...</option>
                                <option v-for="scope in availableScopes" :key="scope.id" :value="scope.id">{{ scope.name }}</option>
                            </select>
                            <span v-if="appointment.errors.scope_id" class="mt-1 block text-xs text-red-600">{{ appointment.errors.scope_id }}</span>
                        </label>
                        <label class="text-xs font-bold text-gray-600">Executive position
                            <select v-model="appointment.executive_position_id" class="mt-1.5 w-full rounded-lg border-gray-200 px-3 py-3 text-sm font-normal">
                                <option value="">Choose position...</option>
                                <option v-for="position in availablePositions" :key="position.id" :value="position.id">{{ position.name }}</option>
                            </select>
                            <span v-if="appointment.errors.executive_position_id" class="mt-1 block text-xs text-red-600">{{ appointment.errors.executive_position_id }}</span>
                        </label>
                        <label class="text-xs font-bold text-gray-600">Select registered member
                            <select v-model="appointment.user_id" class="mt-1.5 w-full rounded-lg border-gray-200 px-3 py-3 text-sm font-normal">
                                <option value="">Choose member...</option>
                                <option v-for="member in members" :key="member.id" :value="member.id">{{ member.name }} — {{ member.email }} ({{ member.member_status || "pending" }})</option>
                            </select>
                            <span v-if="appointment.errors.user_id" class="mt-1 block text-xs text-red-600">{{ appointment.errors.user_id }}</span>
                        </label>
                        <label class="text-xs font-bold text-gray-600">Appointment start date
                            <input v-model="appointment.starts_at" type="date" class="mt-1.5 w-full rounded-lg border-gray-200 px-3 py-3 text-sm font-normal" />
                            <span v-if="appointment.errors.starts_at" class="mt-1 block text-xs text-red-600">{{ appointment.errors.starts_at }}</span>
                        </label>
                        <label class="text-xs font-bold text-gray-600">Appointment end date (optional)
                            <input v-model="appointment.ends_at" type="date" class="mt-1.5 w-full rounded-lg border-gray-200 px-3 py-3 text-sm font-normal" />
                            <span v-if="appointment.errors.ends_at" class="mt-1 block text-xs text-red-600">{{ appointment.errors.ends_at }}</span>
                        </label>
                        <label class="text-xs font-bold text-gray-600 sm:col-span-2">Notes (optional)
                            <textarea v-model="appointment.notes" rows="2" class="mt-1.5 w-full rounded-lg border-gray-200 px-3 py-3 text-sm font-normal" placeholder="Appointment reference or notes"></textarea>
                        </label>
                        <button :disabled="appointment.processing" class="rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-extrabold text-white disabled:opacity-60 sm:col-span-2">{{ appointment.processing ? 'SAVING...' : 'ASSIGN EXECUTIVE' }}</button>
                    </form>
                    <p v-if="!members.length" class="mt-4 rounded-lg bg-amber-50 p-3 text-xs text-amber-800">No registered members are currently available for appointment.</p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6">
                    <div class="mb-5 flex items-start gap-3">
                        <div class="rounded-xl bg-[#EEF8F2] p-3 text-[#006B3C]"><ShieldCheck :size="21" /></div>
                        <div><h2 class="font-extrabold text-[#00351F]">Add Executive Position</h2><p class="mt-1 text-xs text-gray-500">Create additional officer roles for any level.</p></div>
                    </div>
                    <form class="space-y-3" @submit.prevent="submitPosition">
                        <label class="block text-xs font-bold text-gray-600">Organisational level
                            <select v-model="positionForm.scope_type" class="mt-1.5 w-full rounded-lg border-gray-200 px-3 py-3 text-sm font-normal"><option value="global">Global BBHS</option><option value="set">Graduation set</option><option value="branch">Geographical branch</option></select>
                        </label>
                        <label class="block text-xs font-bold text-gray-600">Position title
                            <input v-model="positionForm.name" class="mt-1.5 w-full rounded-lg border-gray-200 px-3 py-3 text-sm font-normal" placeholder="e.g. Sports Secretary" />
                            <span v-if="positionForm.errors.name" class="mt-1 block text-xs text-red-600">{{ positionForm.errors.name }}</span>
                        </label>
                        <button :disabled="positionForm.processing" class="w-full rounded-lg border border-[#006B3C] px-5 py-3 text-sm font-extrabold text-[#006B3C] disabled:opacity-60">{{ positionForm.processing ? 'ADDING...' : 'ADD POSITION' }}</button>
                    </form>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6">
                    <div class="mb-5 flex items-start gap-3">
                        <div class="rounded-xl bg-[#EEF8F2] p-3 text-[#006B3C]"><MapPin :size="21" /></div>
                        <div><h2 class="font-extrabold text-[#00351F]">Add a Branch</h2><p class="mt-1 text-xs text-gray-500">Create a branch and map its first city.</p></div>
                    </div>
                    <form class="space-y-3" @submit.prevent="submitBranch">
                        <label class="block text-xs font-bold text-gray-600">Branch name<input v-model="branchForm.name" class="mt-1.5 w-full rounded-lg border-gray-200 px-3 py-3 text-sm font-normal" placeholder="e.g. Port Harcourt Branch" /><span v-if="branchForm.errors.name" class="mt-1 block text-xs text-red-600">{{ branchForm.errors.name }}</span></label>
                        <label class="block text-xs font-bold text-gray-600">City<input v-model="branchForm.city" class="mt-1.5 w-full rounded-lg border-gray-200 px-3 py-3 text-sm font-normal" placeholder="City covered by branch" /><span v-if="branchForm.errors.city" class="mt-1 block text-xs text-red-600">{{ branchForm.errors.city }}</span></label>
                        <label class="block text-xs font-bold text-gray-600">Country<input v-model="branchForm.country" class="mt-1.5 w-full rounded-lg border-gray-200 px-3 py-3 text-sm font-normal" /><span v-if="branchForm.errors.country" class="mt-1 block text-xs text-red-600">{{ branchForm.errors.country }}</span></label>
                        <label class="block text-xs font-bold text-gray-600">Description<textarea v-model="branchForm.description" rows="2" class="mt-1.5 w-full rounded-lg border-gray-200 px-3 py-3 text-sm font-normal" placeholder="Optional branch description"></textarea></label>
                        <button :disabled="branchForm.processing" class="w-full rounded-lg border border-[#006B3C] px-5 py-3 text-sm font-extrabold text-[#006B3C] disabled:opacity-60">{{ branchForm.processing ? 'CREATING...' : 'CREATE BRANCH' }}</button>
                    </form>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
                <div class="border-b border-gray-100 p-5"><h2 class="font-extrabold text-[#00351F]">Current Executive Appointments</h2><p class="mt-1 text-xs text-gray-500">Appointments are retained when ended, preserving leadership history.</p></div>
                <div v-if="!assignments.length" class="p-8 text-center text-sm text-gray-500">No executive appointments have been made yet.</div>
                <div v-else class="overflow-x-auto">
                    <table class="w-full min-w-[850px] text-left text-sm">
                        <thead class="bg-[#F7F9F7] text-[11px] uppercase tracking-wide text-gray-500"><tr><th class="px-5 py-3">Level</th><th class="px-5 py-3">Scope</th><th class="px-5 py-3">Position</th><th class="px-5 py-3">Appointee</th><th class="px-5 py-3">Term</th><th class="px-5 py-3 text-right">Action</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="item in assignments" :key="item.id">
                                <td class="px-5 py-4 capitalize">{{ item.scope_type }}</td><td class="px-5 py-4 font-semibold text-[#00351F]">{{ scopeLabel(item) }}</td><td class="px-5 py-4">{{ item.position?.name }}</td><td class="px-5 py-4"><p class="font-semibold">{{ item.user?.name }}</p><p class="text-xs text-gray-500">{{ item.user?.email }}</p></td><td class="px-5 py-4 text-xs text-gray-600">{{ item.starts_at || 'Not set' }} – {{ item.ends_at || 'Ongoing' }}</td>
                                <td class="px-5 py-4 text-right"><button @click="endAppointment(item.id)" class="inline-flex items-center gap-1 rounded-lg border border-red-200 px-3 py-2 text-xs font-bold text-red-700 hover:bg-red-50"><XCircle :size="14" /> End appointment</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid gap-5 lg:grid-cols-2">
                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <h2 class="font-extrabold text-[#00351F]">Graduation Sets</h2><p class="mt-1 text-xs text-gray-500">{{ sets.length }} configured sets, 1970–2026.</p>
                    <div class="mt-4 grid max-h-[360px] grid-cols-2 gap-2 overflow-y-auto sm:grid-cols-3">
                        <div v-for="set in sets" :key="set.id" class="rounded-lg border border-gray-100 bg-[#FAFCFA] p-3"><p class="text-sm font-extrabold text-[#00351F]">{{ set.name }}</p><p class="mt-1 text-[11px] text-gray-500">{{ set.leadership_assignments?.length ?? 0 }} active officers</p></div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <h2 class="font-extrabold text-[#00351F]">Geographical Branches</h2><p class="mt-1 text-xs text-gray-500">Branches and their mapped cities.</p>
                    <div class="mt-4 space-y-3">
                        <div v-for="branch in branches" :key="branch.id" class="rounded-xl border border-gray-100 p-4">
                            <div class="flex items-center justify-between gap-3"><p class="font-bold text-[#00351F]">{{ branch.name }}</p><span class="text-[11px] text-gray-500">{{ branch.leadership_assignments?.length ?? 0 }} officers</span></div>
                            <p class="mt-1 text-xs text-gray-500">{{ branch.cities?.map(city => `${city.city}, ${city.country}`).join(' · ') || 'No cities mapped' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</PublicLayout>
</template>
