<script setup>
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import AdminQuickLinks from '../../../Components/AdminQuickLinks.vue';
import { router, usePage, useForm } from '@inertiajs/vue3';
import { reactive } from 'vue';
import { Plus, Pencil, Trash2, Archive, Search, RotateCcw } from 'lucide-vue-next';
const props=defineProps({archives:Object,categories:{type:Array,default:()=>[]},filters:{type:Object,default:()=>({})}});
const page=usePage();
const filters=reactive({search:props.filters?.search||'',category:props.filters?.category||'',status:props.filters?.status||''});
const apply=()=>router.get('/admin/archives',filters,{preserveState:true,replace:true});
const clear=()=>{filters.search='';filters.category='';filters.status='';apply();};
const del=id=>{if(confirm('Delete this historical archive record?'))useForm({}).delete(`/admin/archives/${id}`);};
</script>
<template><PublicLayout>
<section class="bg-[#00351F] text-white"><div class="mx-auto max-w-7xl px-5 py-12"><p class="text-xs font-bold uppercase tracking-widest text-[#D4A72C]">Administration</p><div class="mt-2 flex flex-wrap items-end justify-between gap-4"><div><h1 class="text-4xl font-extrabold">Historical Archives</h1><p class="mt-2 text-white/70">Manage heritage, honours and historical records.</p></div><a href="/admin/archives/create" class="rounded-lg bg-[#D4A72C] px-5 py-3 font-bold text-[#00351F]"><Plus class="mr-1 inline" :size="17"/> Add Record</a></div></div><AdminQuickLinks />
    </section>
<section class="bg-[#F7F9F7] py-10"><div class="mx-auto max-w-7xl px-5">
<div v-if="page.props.flash?.success" class="mb-5 rounded bg-green-50 p-3 text-green-700">{{page.props.flash.success}}</div>
<div class="mb-5 grid gap-3 rounded-2xl border border-gray-200 bg-white p-4 md:grid-cols-[minmax(220px,1fr)_240px_180px_auto_auto]">
<input v-model="filters.search" @keyup.enter="apply" placeholder="Search title, summary or content..." class="rounded-lg border-gray-200"/>
<select v-model="filters.category" class="rounded-lg border-gray-200"><option value="">All categories</option><option v-for="category in categories" :key="category" :value="category">{{category}}</option></select>
<select v-model="filters.status" class="rounded-lg border-gray-200"><option value="">All statuses</option><option value="published">Published</option><option value="draft">Draft</option></select>
<button @click="apply" class="rounded-lg bg-[#006B3C] px-4 py-2 font-bold text-white"><Search class="mr-1 inline" :size="15"/> Search</button><button @click="clear" class="rounded-lg border border-gray-200 px-4 py-2 font-bold text-gray-600"><RotateCcw class="mr-1 inline" :size="15"/> Clear</button></div>
<p class="mb-3 text-sm text-gray-500">{{archives.total??archives.data?.length??0}} archive records</p>
<div class="overflow-hidden rounded-2xl border bg-white"><div v-if="!archives.data?.length" class="p-12 text-center text-gray-500">No archive records match these filters.</div>
<div v-for="item in archives.data" :key="item.id" class="flex flex-col justify-between gap-4 border-b p-5 sm:flex-row sm:items-center"><div class="flex gap-3"><div class="rounded-lg bg-[#EEF8F2] p-3 text-[#006B3C]"><Archive/></div><div><h2 class="font-extrabold text-[#00351F]">{{item.title}}</h2><p class="text-xs text-gray-500">{{item.category}} · {{item.record_year||'No year'}} · {{item.is_published?'Published':'Draft'}} · {{item.is_featured?'Featured':'Standard'}}</p><p class="mt-1 max-w-2xl text-sm text-gray-500">{{item.summary}}</p></div></div><div class="flex shrink-0 gap-2"><a :href="`/admin/archives/${item.id}/edit`" class="rounded border px-3 py-2 text-xs font-bold"><Pencil class="mr-1 inline" :size="14"/> Edit</a><a v-if="item.is_published" :href="`/archives/${item.slug}`" target="_blank" class="rounded border border-[#006B3C]/30 px-3 py-2 text-xs font-bold text-[#006B3C]">View</a><button @click="del(item.id)" class="rounded bg-red-50 px-3 py-2 text-xs font-bold text-red-700"><Trash2 class="mr-1 inline" :size="14"/> Delete</button></div></div></div>
<div v-if="archives.links?.length>3" class="mt-6 flex flex-wrap gap-2"><a v-for="link in archives.links" :key="link.label" :href="link.url||'#'" @click.prevent="link.url&&router.visit(link.url)" v-html="link.label" class="rounded-lg border bg-white px-3 py-2 text-sm" :class="link.active?'border-[#006B3C] bg-[#006B3C] text-white':'border-gray-200 text-gray-600'"/></div>
</div></section></PublicLayout></template>
