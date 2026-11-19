<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import AdminQuickLinks from '../../../Components/AdminQuickLinks.vue';
import { ArrowLeft, Download, FileSpreadsheet, Upload, CheckCircle2, AlertCircle } from 'lucide-vue-next';

const props = defineProps({
    scope: { type: Object, default: () => ({}) },
    sets: { type: Array, default: () => [] },
    branches: { type: Array, default: () => [] },
    preview: { type: Object, default: null },
});

const upload = useForm({ csv_file: null });
const confirmation = useForm({ token: '' });

const selectedFileName = computed(() => upload.csv_file?.name || 'No CSV file selected');
const previewRows = computed(() => props.preview?.rows ?? []);
const validCount = computed(() => props.preview?.valid_count ?? 0);
const invalidCount = computed(() => previewRows.value.length - validCount.value);

const chooseFile = (event) => {
    upload.csv_file = event.target.files?.[0] ?? null;
    upload.clearErrors();
};

const submitPreview = () => {
    if (!upload.csv_file || upload.processing) return;

    upload.post('/admin/members/import/preview', {
        forceFormData: true,
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            // The server returns this same page with a preview prop.
            upload.clearErrors();
        },
    });
};

const confirmImport = () => {
    if (!props.preview?.token || confirmation.processing) return;

    confirmation.token = props.preview.token;
    confirmation.post('/admin/members/import/confirm', {
        preserveScroll: true,
    });
};

const rowIssues = (row) => Array.isArray(row._issues) ? row._issues : [];
</script>

<template>
    <Head title="Bulk Member Import" />

    <PublicLayout>
        <!-- Match the existing Members administration page header -->
        <section class="bg-[#00351F]">
            <div class="mx-auto max-w-[1400px] px-5 py-10 lg:px-8">
                <Link href="/admin/members" class="inline-flex items-center gap-2 text-xs font-bold text-white/70 hover:text-white">
                    <ArrowLeft :size="14" /> BACK TO MEMBERS
                </Link>

                <div class="mt-6 flex flex-wrap items-end justify-between gap-5">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">
                            Membership Administration
                        </p>
                        <h1 class="mt-2 text-3xl font-extrabold text-white sm:text-4xl">
                            Bulk Member Import
                        </h1>
                        <p class="mt-2 text-sm text-white/70">
                            Upload a CSV, review the records, then confirm the import.
                        </p>
                        <p v-if="scope.label" class="mt-2 text-xs font-semibold text-[#F1D36A]">
                            {{ scope.label }}
                        </p>
                    </div>

                    <a href="/admin/members/import/template"
                       class="inline-flex items-center gap-2 rounded-lg bg-[#D4A72C] px-4 py-3 text-xs font-extrabold text-[#00351F] hover:bg-[#E5B93F]">
                        <Download :size="15" /> DOWNLOAD CSV TEMPLATE
                    </a>
                </div>
            </div>
            <AdminQuickLinks />
        </section>

        <section class="bg-[#F7F9F7] py-9">
            <div class="mx-auto max-w-[1400px] space-y-6 px-5 lg:px-8">

                <div class="rounded-xl border border-blue-100 bg-blue-50 p-5 text-sm text-blue-900">
                    <h2 class="font-extrabold">How the import works</h2>
                    <ol class="mt-2 list-decimal space-y-1 pl-5">
                        <li>Download the CSV template and fill in the member details.</li>
                        <li>Choose the completed CSV file and click Preview CSV.</li>
                        <li>Review valid and invalid rows below.</li>
                        <li>Confirm only when the preview is correct. Members will remain pending approval and receive a secure password setup email.</li>
                    </ol>
                </div>

                <div class="grid gap-6 lg:grid-cols-[1.15fr_.85fr]">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#EEF8F2] text-[#006B3C]">
                                <FileSpreadsheet :size="22" />
                            </div>
                            <div>
                                <h2 class="font-extrabold text-[#00351F]">Upload membership CSV</h2>
                                <p class="text-xs text-gray-500">CSV format only · Maximum file size 5MB</p>
                            </div>
                        </div>

                        <form class="mt-6 space-y-4" @submit.prevent="submitPreview">
                            <div>
                                <label for="member-csv-file" class="mb-2 block text-sm font-bold text-gray-700">
                                    Select CSV file
                                </label>
                                <input
                                    id="member-csv-file"
                                    type="file"
                                    accept=".csv,text/csv"
                                    required
                                    class="block w-full rounded-lg border border-gray-300 bg-white text-sm text-gray-700 file:mr-4 file:border-0 file:bg-gray-100 file:px-4 file:py-3 file:text-sm file:font-bold file:text-gray-700 hover:file:bg-gray-200"
                                    @input="chooseFile"
                                />
                                <p class="mt-2 text-xs text-gray-500">
                                    Selected: <strong>{{ selectedFileName }}</strong>
                                </p>
                                <p v-if="upload.errors.csv_file" class="mt-2 text-sm font-semibold text-red-600">
                                    {{ upload.errors.csv_file }}
                                </p>
                            </div>

                            <div v-if="upload.progress" class="space-y-1">
                                <div class="flex justify-between text-xs text-gray-600">
                                    <span>Uploading file…</span>
                                    <span>{{ upload.progress.percentage }}%</span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                                    <div class="h-full rounded-full bg-[#006B3C] transition-all"
                                         :style="{ width: `${upload.progress.percentage}%` }"></div>
                                </div>
                            </div>

                            <button
                                type="submit"
                                :disabled="!upload.csv_file || upload.processing"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#006B3C] px-6 py-3 text-sm font-extrabold text-white hover:bg-[#00552F] disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <Upload :size="16" />
                                {{ upload.processing ? 'UPLOADING & PREPARING PREVIEW…' : 'PREVIEW CSV' }}
                            </button>
                        </form>

                        <div v-if="upload.recentlySuccessful" class="mt-4 rounded-lg bg-green-50 p-3 text-sm text-green-800">
                            File submitted. Review the preview below.
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="font-extrabold text-[#00351F]">CSV columns required</h2>
                        <p class="mt-1 text-xs text-gray-500">Use these exact headings in the first row.</p>
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full text-left text-sm">
                                <thead class="border-b bg-gray-50 text-xs uppercase text-gray-500">
                                    <tr><th class="px-3 py-2">Column</th><th class="px-3 py-2">Meaning</th></tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr v-for="field in [
                                        ['first_name', 'First name'],
                                        ['last_name', 'Surname'],
                                        ['email', 'Unique email address'],
                                        ['phone', 'Telephone number'],
                                        ['set', 'Existing graduation set name or year'],
                                        ['branch', 'Existing branch name'],
                                    ]" :key="field[0]">
                                        <td class="px-3 py-2 font-mono text-xs font-bold text-[#006B3C]">{{ field[0] }}</td>
                                        <td class="px-3 py-2 text-gray-600">{{ field[1] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 rounded-lg bg-amber-50 p-3 text-xs leading-5 text-amber-900">
                            <strong>Branch administrators:</strong> members imported by you will be assigned to your own branch. The branch column must be blank or match your branch.
                        </div>
                        <div class="mt-4 text-xs text-gray-500">
                            <p class="font-bold text-gray-700">Active sets in this system</p>
                            <p class="mt-1">{{ sets.map(item => item.name).join(', ') || 'No active sets were supplied.' }}</p>
                            <p class="mt-3 font-bold text-gray-700">Active branches in this system</p>
                            <p class="mt-1">{{ branches.map(item => item.name).join(', ') || 'No active branches were supplied.' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Preview is returned by the controller after a successful POST. -->
                <div v-if="preview" class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 p-5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-extrabold text-[#00351F]">CSV preview</h2>
                                <p class="mt-1 text-xs text-gray-500">Check every row before creating member accounts.</p>
                            </div>
                            <div class="flex gap-3 text-sm">
                                <span class="rounded-lg bg-green-50 px-3 py-2 font-bold text-green-700">{{ validCount }} valid</span>
                                <span class="rounded-lg bg-red-50 px-3 py-2 font-bold text-red-700">{{ invalidCount }} need attention</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-3 border-b border-gray-100 bg-gray-50 p-5 sm:grid-cols-3">
                        <div><p class="text-xs text-gray-500">Rows detected</p><p class="mt-1 text-2xl font-extrabold text-gray-900">{{ previewRows.length }}</p></div>
                        <div><p class="text-xs text-gray-500">Ready to import</p><p class="mt-1 text-2xl font-extrabold text-green-700">{{ validCount }}</p></div>
                        <div><p class="text-xs text-gray-500">Invalid / duplicate</p><p class="mt-1 text-2xl font-extrabold text-red-700">{{ invalidCount }}</p></div>
                    </div>

                    <div v-if="!previewRows.length" class="p-8 text-center text-sm text-gray-500">
                        The CSV was uploaded, but no member rows were found. Check that the file contains data beneath the headings.
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-left text-xs font-bold uppercase text-gray-500">
                                <tr>
                                    <th class="px-4 py-3">Line</th>
                                    <th class="px-4 py-3">Member</th>
                                    <th class="px-4 py-3">Email / Phone</th>
                                    <th class="px-4 py-3">Set</th>
                                    <th class="px-4 py-3">Branch</th>
                                    <th class="px-4 py-3">Validation</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="row in previewRows" :key="row._line" class="align-top">
                                    <td class="px-4 py-3 text-gray-500">{{ row._line }}</td>
                                    <td class="px-4 py-3 font-semibold text-gray-900">{{ row.first_name }} {{ row.last_name }}</td>
                                    <td class="px-4 py-3 text-gray-600">
                                        <div>{{ row.email }}</div><div class="text-xs">{{ row.phone }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ row.set }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ row.branch || '—' }}</td>
                                    <td class="px-4 py-3">
                                        <span v-if="!rowIssues(row).length" class="inline-flex items-center gap-1 font-bold text-green-700">
                                            <CheckCircle2 :size="14" /> Valid
                                        </span>
                                        <span v-else class="inline-flex items-center gap-1 font-bold text-red-700">
                                            <AlertCircle :size="14" /> {{ rowIssues(row).length }} issue(s)
                                        </span>
                                        <ul v-if="rowIssues(row).length" class="mt-1 list-disc pl-4 text-xs text-red-600">
                                            <li v-for="issue in rowIssues(row)" :key="issue">{{ issue }}</li>
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 p-5">
                        <p class="max-w-2xl text-xs text-gray-500">
                            Only valid rows will be imported. Invalid rows will be skipped and shown in the final report.
                        </p>
                        <button
                            type="button"
                            :disabled="validCount === 0 || confirmation.processing"
                            @click="confirmImport"
                            class="rounded-lg bg-[#006B3C] px-6 py-3 text-sm font-extrabold text-white hover:bg-[#00552F] disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {{ confirmation.processing ? 'IMPORTING…' : `CONFIRM IMPORT (${validCount})` }}
                        </button>
                    </div>
                </div>

            </div>
        </section>
    </PublicLayout>
</template>
