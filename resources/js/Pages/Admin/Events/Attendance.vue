<script setup>
import { computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import AdminQuickLinks from '../../../Components/AdminQuickLinks.vue';

const props = defineProps({
    event: { type: Object, required: true },
    attendances: { type: Array, default: () => [] },
    minutes: { type: Object, default: null },
    resolutions: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({ registered: 0, present: 0, absent: 0, excused: 0 }) },
});
const page = usePage();
const attendanceForms = {};
const minutesForm = useForm({
    summary: props.minutes?.summary || '',
    minutes: props.minutes?.minutes || '',
    recorded_by_name: props.minutes?.recorded_by_name || '',
});
const resolutionForm = useForm({
    resolution: '', responsible_person: '', due_date: '', status: 'open', notes: '',
});
const percent = computed(() => props.stats.registered ? Math.round(props.stats.present / props.stats.registered * 100) : 0);
const updateAttendance = (row, status) => {
    useForm({ status, notes: row.notes || '' }).patch(`/admin/events/${props.event.id}/attendance/${row.id}`, { preserveScroll: true });
};
const saveMinutes = () => minutesForm.put(`/admin/events/${props.event.id}/minutes`, { preserveScroll: true });
const addResolution = () => resolutionForm.post(`/admin/events/${props.event.id}/resolutions`, {
    preserveScroll: true,
    onSuccess: () => resolutionForm.reset('resolution', 'responsible_person', 'due_date', 'notes'),
});
const updateResolution = (item, status) => {
    useForm({ status, notes: item.notes || '' }).patch(`/admin/events/${props.event.id}/resolutions/${item.id}`, { preserveScroll: true });
};
</script>

<template>
<PublicLayout>
    <section class="bg-[#00351F]">
        <div class="mx-auto max-w-[1200px] px-5 py-10 lg:px-8">
            <p class="text-xs font-bold uppercase tracking-widest text-[#D4A72C]">Event administration</p>
            <h1 class="mt-2 text-3xl font-extrabold text-white">Attendance & Event Report</h1>
            <p class="mt-2 text-white/75">{{ event.title }}</p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a :href="`/admin/events/${event.id}/registrations`" class="rounded-lg border border-white/40 px-4 py-2 text-sm font-semibold text-white">Registrations</a>
                <a :href="`/admin/events/${event.id}/attendance/export`" class="rounded-lg bg-white px-4 py-2 text-sm font-bold text-[#00351F]">Export attendance CSV</a>
                <a href="/admin/events" class="rounded-lg border border-white/40 px-4 py-2 text-sm font-semibold text-white">All events</a>
            </div>
        </div>
        <AdminQuickLinks />
    </section>

    <section class="bg-[#F7F9F7] py-10">
        <div class="mx-auto max-w-[1200px] space-y-8 px-5 lg:px-8">
            <div v-if="page.props.flash?.success" class="rounded-lg bg-green-50 p-4 text-sm text-green-800">{{ page.props.flash.success }}</div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <div v-for="item in [{label:'Registered',value:stats.registered},{label:'Present',value:stats.present},{label:'Absent',value:stats.absent},{label:'Excused',value:stats.excused},{label:'Attendance',value:percent+'%'}]" :key="item.label" class="rounded-xl border bg-white p-5">
                    <p class="text-sm text-gray-500">{{ item.label }}</p><p class="mt-1 text-3xl font-extrabold text-[#00351F]">{{ item.value }}</p>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border bg-white">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b p-5">
                    <div><h2 class="text-xl font-extrabold text-[#00351F]">Attendance register</h2><p class="mt-1 text-sm text-gray-500">Confirmed event registrants are added automatically.</p></div>
                </div>
                <div v-if="!attendances.length" class="p-10 text-center text-gray-500">No confirmed registrants to add to the attendance register yet.</div>
                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="px-5 py-4">Member</th><th class="px-5 py-4">Email</th><th class="px-5 py-4">Status</th><th class="px-5 py-4">Update attendance</th></tr></thead>
                        <tbody class="divide-y">
                            <tr v-for="row in attendances" :key="row.id">
                                <td class="px-5 py-4 font-semibold text-[#00351F]">{{ row.user?.name || 'Unavailable' }}</td>
                                <td class="px-5 py-4 text-gray-600">{{ row.user?.email || '—' }}</td>
                                <td class="px-5 py-4 capitalize">{{ row.status.replace('_',' ') }}</td>
                                <td class="px-5 py-4"><div class="flex flex-wrap gap-2">
                                    <button v-for="status in ['present','absent','excused']" :key="status" @click="updateAttendance(row,status)" :class="['rounded-md px-3 py-2 text-xs font-bold capitalize',row.status===status?'bg-[#00351F] text-white':'border text-gray-700 hover:bg-gray-50']">{{ status }}</button>
                                </div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid gap-8 lg:grid-cols-2">
                <form @submit.prevent="saveMinutes" class="space-y-4 rounded-xl border bg-white p-6">
                    <div><h2 class="text-xl font-extrabold text-[#00351F]">Meeting minutes</h2><p class="text-sm text-gray-500">Save the meeting summary and full minutes.</p></div>
                    <label class="block text-sm font-semibold">Recorded by<input v-model="minutesForm.recorded_by_name" class="mt-1 w-full rounded-lg border-gray-300" placeholder="Name of secretary or recorder"></label>
                    <label class="block text-sm font-semibold">Summary<textarea v-model="minutesForm.summary" rows="3" class="mt-1 w-full rounded-lg border-gray-300"></textarea></label>
                    <label class="block text-sm font-semibold">Full minutes<textarea v-model="minutesForm.minutes" rows="8" class="mt-1 w-full rounded-lg border-gray-300"></textarea></label>
                    <button :disabled="minutesForm.processing" class="rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white disabled:opacity-50">Save minutes</button>
                </form>

                <div class="space-y-5 rounded-xl border bg-white p-6">
                    <div><h2 class="text-xl font-extrabold text-[#00351F]">Resolutions & action points</h2><p class="text-sm text-gray-500">Record decisions, owners and due dates.</p></div>
                    <form @submit.prevent="addResolution" class="space-y-3 border-b pb-5">
                        <textarea v-model="resolutionForm.resolution" required rows="3" class="w-full rounded-lg border-gray-300" placeholder="Resolution / action point"></textarea>
                        <input v-model="resolutionForm.responsible_person" class="w-full rounded-lg border-gray-300" placeholder="Responsible person">
                        <div class="grid gap-3 sm:grid-cols-2"><input v-model="resolutionForm.due_date" type="date" class="w-full rounded-lg border-gray-300"><select v-model="resolutionForm.status" class="w-full rounded-lg border-gray-300"><option value="open">Open</option><option value="in_progress">In progress</option><option value="completed">Completed</option></select></div>
                        <button :disabled="resolutionForm.processing" class="rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white">Add resolution</button>
                    </form>
                    <div v-if="!resolutions.length" class="text-sm text-gray-500">No resolutions recorded yet.</div>
                    <div v-for="item in resolutions" :key="item.id" class="rounded-lg border p-4">
                        <p class="font-semibold text-[#00351F]">{{ item.resolution }}</p>
                        <p class="mt-1 text-xs text-gray-500">{{ item.responsible_person || 'No owner assigned' }}<span v-if="item.due_date"> · Due {{ item.due_date }}</span></p>
                        <div class="mt-3 flex flex-wrap items-center gap-2"><span class="text-xs text-gray-500">Status:</span><select :value="item.status" @change="updateResolution(item,$event.target.value)" class="rounded-md border-gray-300 py-1 text-xs"><option value="open">Open</option><option value="in_progress">In progress</option><option value="completed">Completed</option></select></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</PublicLayout>
</template>
