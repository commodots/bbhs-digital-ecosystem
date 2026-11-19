<script setup>
import { computed, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import MemberPortalLayout from '../../../Layouts/MemberPortalLayout.vue';

const props = defineProps({ candidates: { type: Array, default: () => [] } });
const selected = ref(props.candidates.filter(x => x.selected).map(x => x.id));
const form = useForm({ user_ids: selected.value });
const count = computed(() => selected.value.length);
function save() { form.user_ids = [...selected.value]; form.put('/admin/welfare-team'); }
</script>

<template>
<MemberPortalLayout>
    <Head title="Welfare Team" />
    <section class="bg-[#00351F] text-white">
        <div class="mx-auto max-w-6xl px-5 py-10">
            <p class="text-xs font-bold uppercase tracking-widest text-[#D4A72C]">Super Admin</p>
            <h1 class="mt-2 text-3xl font-extrabold">Welfare Team Access</h1>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-white/75">Select any approved BBHS members who should review and manage confidential welfare applications. Only selected users receive Welfare Team access; they do not receive general administrator privileges.</p>
        </div>
        <AdminQuickLinks />
    </section>
    <main class="mx-auto max-w-6xl px-5 py-9">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div><h2 class="text-xl font-bold text-[#00351F]">Approved Members</h2><p class="text-sm text-gray-500">{{ count }} selected</p></div>
            <button @click="save" :disabled="form.processing" class="rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white disabled:opacity-50">{{ form.processing ? 'Saving...' : 'Save Welfare Team' }}</button>
        </div>
        <div v-if="!candidates.length" class="rounded-xl border bg-gray-50 p-8 text-center text-sm text-gray-600">No approved members are currently available for Welfare Team selection.</div>
        <div v-else class="overflow-hidden rounded-xl border bg-white">
            <label v-for="person in candidates" :key="person.id" class="flex cursor-pointer items-center gap-4 border-b p-4 last:border-0 hover:bg-gray-50">
                <input v-model="selected" type="checkbox" :value="person.id" class="h-5 w-5 rounded border-gray-300 text-[#006B3C] focus:ring-[#006B3C]" />
                <span class="min-w-0 flex-1"><strong class="block text-sm text-gray-900">{{ person.name }}</strong><span class="block text-xs text-gray-500">{{ person.email }} · {{ person.set || 'Graduation set' }}</span><span class="mt-1 block text-xs text-[#087A3A]">{{ person.set || 'Approved BBHS Member' }}</span></span>
                <span v-if="person.selected" class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">Current team</span>
            </label>
        </div>
        <p v-if="form.errors.user_ids" class="mt-3 text-sm text-red-600">{{ form.errors.user_ids }}</p>
    </main>
</MemberPortalLayout>
</template>
