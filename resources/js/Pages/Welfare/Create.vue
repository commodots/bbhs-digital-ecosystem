<script setup>
import PublicLayout from '../../Layouts/PublicLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({ categories: { type: Array, default: () => [] } });

const form = useForm({
    category: '',
    subject: '',
    description: '',
    amount_requested: '',
    attachments: [],
});
const submissionError = ref('');

const submit = () => {
    submissionError.value = '';
    form.post('/welfare', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            const fileInput = document.getElementById('welfare-attachments');
            if (fileInput) fileInput.value = '';
        },
        onError: errors => {
            if (Object.keys(errors || {}).length === 0) {
                submissionError.value = 'We could not submit your application. Please try again or contact the BBHS administrator.';
            }
        },
        onFinish: () => {
            if (!form.hasErrors && !form.processing) {
                // Keep the form visible only if the server did not redirect after success.
            }
        },
    });
};

const filesChanged = event => {
    form.attachments = Array.from(event.target.files || []);
};
</script>

<template>
<PublicLayout>
    <section class="bg-[#00351F]">
        <div class="mx-auto max-w-[900px] px-5 py-10 lg:px-8">
            <p class="text-xs font-bold uppercase tracking-widest text-[#D4A72C]">Confidential member service</p>
            <h1 class="mt-2 text-3xl font-extrabold text-white">Welfare Application</h1>
            <p class="mt-2 text-sm text-white/70">Your application and supporting documents are private and visible only to you and authorised administrators.</p>
        </div>
    </section>

    <section class="bg-[#F7F9F7] py-10">
        <div class="mx-auto max-w-[900px] px-5 lg:px-8">
            <form @submit.prevent="submit" class="space-y-5 rounded-2xl border bg-white p-6 shadow-sm sm:p-8">
                <div v-if="submissionError || Object.keys(form.errors).length" role="alert" class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <p class="font-bold">Your application was not submitted.</p>
                    <p v-if="submissionError" class="mt-1">{{ submissionError }}</p>
                    <ul v-if="Object.keys(form.errors).length" class="mt-2 list-inside list-disc space-y-1">
                        <li v-for="(message, field) in form.errors" :key="field">{{ message }}</li>
                    </ul>
                </div>

                <div>
                    <label for="welfare-category" class="mb-1 block text-sm font-bold">Request category</label>
                    <select id="welfare-category" v-model="form.category" required class="w-full rounded-lg border-gray-300">
                        <option value="" disabled>Select category</option>
                        <option v-for="item in categories" :key="item" :value="item">{{ item }}</option>
                    </select>
                    <p v-if="form.errors.category" class="mt-1 text-sm text-red-600">{{ form.errors.category }}</p>
                </div>

                <div>
                    <label for="welfare-subject" class="mb-1 block text-sm font-bold">Subject</label>
                    <input id="welfare-subject" v-model="form.subject" required maxlength="255" class="w-full rounded-lg border-gray-300" placeholder="Brief description of your request"/>
                    <p v-if="form.errors.subject" class="mt-1 text-sm text-red-600">{{ form.errors.subject }}</p>
                </div>

                <div>
                    <label for="welfare-description" class="mb-1 block text-sm font-bold">Explain your request</label>
                    <textarea id="welfare-description" v-model="form.description" required minlength="20" rows="7" class="w-full rounded-lg border-gray-300" placeholder="Please provide relevant details to help the welfare committee review your application."></textarea>
                    <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                </div>

                <div>
                    <label for="welfare-amount" class="mb-1 block text-sm font-bold">Amount requested (₦, optional)</label>
                    <input id="welfare-amount" v-model="form.amount_requested" type="number" min="0" step="0.01" class="w-full rounded-lg border-gray-300" placeholder="e.g. 150000"/>
                    <p v-if="form.errors.amount_requested" class="mt-1 text-sm text-red-600">{{ form.errors.amount_requested }}</p>
                </div>

                <div>
                    <label for="welfare-attachments" class="mb-1 block text-sm font-bold">Supporting documents (optional)</label>
                    <input id="welfare-attachments" type="file" multiple accept=".pdf,.jpg,.jpeg,.png,.webp" @change="filesChanged" class="w-full rounded-lg border border-gray-300 p-3 text-sm"/>
                    <p class="mt-1 text-xs text-gray-500">Up to 5 files, 10MB each. PDF or image formats.</p>
                    <p v-if="form.errors.attachments" class="mt-1 text-sm text-red-600">{{ form.errors.attachments }}</p>
                    <p v-if="form.errors['attachments.0']" class="mt-1 text-sm text-red-600">{{ form.errors['attachments.0'] }}</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" :disabled="form.processing" class="inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-6 py-3 text-sm font-bold text-white disabled:cursor-not-allowed disabled:opacity-60">
                        <span v-if="form.processing">Submitting application…</span>
                        <span v-else>Submit confidential application</span>
                    </button>
                    <Link href="/welfare" class="rounded-lg border px-6 py-3 text-sm font-bold text-gray-700">Cancel</Link>
                </div>
            </form>
        </div>
    </section>
</PublicLayout>
</template>
