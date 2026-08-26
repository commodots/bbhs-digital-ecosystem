<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { Image as ImageIcon, ArrowRight } from 'lucide-vue-next';

const images = [
    { src: '/images/bbhs/school/bbhs-school-gate.jpg', title: 'BBHS Heritage', category: 'School' },
    { src: '/images/bbhs/students/bbhs-students-01.jpg', title: 'The Next Generation', category: 'Students' },
    { src: '/images/bbhs/students/bbhs-students-02.jpg', title: 'BBHS Students', category: 'Students' },
    { src: '/images/bbhs/students/bbhs-students-03.jpg', title: 'School Life', category: 'Students' },
    { src: '/images/bbhs/events/reunion-01.jpg', title: 'Old Boys Reunion', category: 'Reunion' },
    { src: '/images/bbhs/events/reunion-02.jpg', title: 'Brotherhood', category: 'Events' },
    { src: '/images/bbhs/alumni/oyewole-seun.jpg', title: 'Old Boys', category: 'Alumni' },
    { src: '/images/bbhs/alumni/akinkunmi-akinlade.jpg', title: 'Alumni Community', category: 'Alumni' },
    { src: '/images/bbhs/alumni/otunba-wole-okunowo.jpg', title: 'BBHS Alumni', category: 'Alumni' },
];
</script>

<template>
<PublicLayout>
    <section class="bg-[#00351F]">
        <div class="mx-auto flex min-h-[350px] max-w-[1440px] items-center px-5 py-16 lg:px-10">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F1D36A]">BBHS Gallery</p>
                <h1 class="mt-4 text-4xl font-extrabold text-white sm:text-5xl lg:text-6xl">Memories That <span class="text-[#16A34A]">Connect Us.</span></h1>
                <p class="mt-5 max-w-2xl text-base leading-7 text-white/80 sm:text-lg">A visual collection celebrating BBHS, our students, Old Boys, reunions and shared heritage.</p>
            </div>
        </div>
    </section>

    <section class="bg-white py-12 lg:py-14">
        <div class="mx-auto max-w-[1200px] px-5 lg:px-8">
            <div class="mb-8 flex items-end justify-between">
                <div><p class="text-xs font-bold uppercase tracking-[0.22em] text-[#006B3C]">Our Memories</p><h2 class="mt-2 text-3xl font-extrabold text-[#00351F]">BBHS Gallery</h2></div>
                <ImageIcon class="hidden text-[#D4A72C] sm:block" :size="28" />
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <article v-for="image in images" :key="image.src" class="group overflow-hidden rounded-2xl bg-gray-100">
                    <div class="relative h-[260px] overflow-hidden">
                        <img :src="image.src" :alt="image.title" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-[#00351F]/90 to-transparent p-5 pt-16">
                            <p class="text-[9px] font-bold uppercase tracking-[0.18em] text-[#D4A72C]">{{ image.category }}</p>
                            <h3 class="mt-1 text-lg font-extrabold text-white">{{ image.title }}</h3>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="bg-[#F7F9F7] py-12">
        <div class="mx-auto max-w-[900px] px-5 text-center">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#006B3C]">Keep The Memories Growing</p>
            <h2 class="mt-2 text-3xl font-extrabold text-[#00351F]">Have BBHS Photos to Share?</h2>
            <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-gray-500">The future gallery will allow authorised members and administrators to upload and organise BBHS memories.</p>
            <a href="/contact" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-6 py-3 text-sm font-bold text-white">CONTACT US <ArrowRight :size="16" /></a>
        </div>
    </section>
</PublicLayout>
</template>
