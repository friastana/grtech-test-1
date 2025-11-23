<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Form from './Partials/Form.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Add Employee" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Add Employee
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Form />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
