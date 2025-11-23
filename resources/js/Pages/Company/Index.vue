<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, createVNode, ref } from 'vue';
import { usePagination } from 'vue-request';
import axios from 'axios';
import { Modal } from 'ant-design-vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import Form from './Partials/Form.vue';

const formDelete = useForm();

const columns = [
    {
        title: 'Index',
        dataIndex: 'id',
        sorter: true,
    },
    {
        title: 'Name',
        dataIndex: 'name',
        sorter: true,
    },
    {
        title: 'Email',
        dataIndex: 'email',
        sorter: true,
    },
    {
        title: 'Logo',
        dataIndex: 'logo',
    },
    {
        title: 'Website',
        dataIndex: 'website',
        sorter: true,
    },
    {
        title: 'Action',
        dataIndex: 'action',
    },
];

const queryData = async params => {
    const res = await axios.get('/company/list', { params });
    return res.data;
};

const {
    data: dataSource,
    run,
    loading,
    current,
    pageSize,
    total,
} = usePagination(queryData, {
    pagination: {
        currentKey: 'page',
        pageSizeKey: 'results',
        totalKey: 'total',
    },
});

const pagination = computed(() => ({
    current: current.value,
    pageSize: pageSize.value,
    total: total.value,
}));

const handleTableChange = (pag, filters, sorter) => {
    run({
        results: pag.pageSize,
        page: pag?.current,
        sortField: sorter.field,
        sortOrder: sorter.order,
        ...filters,
    });
};

const showConfirm = (id) => {
    Modal.confirm({
        title: 'Do you want to delete this company?',
        icon: createVNode(ExclamationCircleOutlined),
        onOk() {
            formDelete.delete(route('company.destroy', id), {
                preserveScroll: true,
                onSuccess: () => run({ page: current.value, results: pageSize.value }),
            });
        },
    });
};

const open = ref(false);
const editingCompany = ref(null);

const showModal = (record) => {
    editingCompany.value = record;
    open.value = true;
};

const closeModal = () => {
    open.value = false;
    editingCompany.value = null;
};

const handleSuccess = () => {
    closeModal();
    run({ page: current.value, results: pageSize.value });
};
</script>

<template>
    <Head title="Company" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Company
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Link :href="route('company.create')">
                    <a-button type="primary" class="mb-4">Add</a-button>
                </Link>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <a-table
                        :columns="columns"
                        :data-source="dataSource?.results"
                        :loading="loading"
                        :pagination="pagination"
                        :row-key="record => record.id"
                        @change="handleTableChange"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'website'">
                                <a :href="record.website" target="_blank">{{ record.website }}</a>
                            </template>
                            <template v-else-if="column.dataIndex === 'email'">
                                <a :href="`mailto:${record.email}`">{{ record.email }}</a>
                            </template>
                            <template v-else-if="column.dataIndex === 'logo'">
                                <a-image :width="50" :src="`storage/${record.logo}`" fallback="error_image_url" />
                            </template>
                            <template v-else-if="column.dataIndex === 'action'">
                                <span>
                                    <a-button type="primary" @click="showModal(record)">Edit</a-button>
                                    <a-divider type="vertical" />
                                    <a-button type="primary" danger @click="showConfirm(record.id)">Delete</a-button>
                                </span>
                            </template>
                        </template>
                    </a-table>
                </div>
            </div>
        </div>

        <a-modal
            v-model:open="open"
            title="Edit Company"
            :footer="null"
            destroyOnClose
            @cancel="closeModal"
        >
            <Form
                :company="editingCompany"
                :is-modal="true"
                @success="handleSuccess"
                @cancel="closeModal"
            />
        </a-modal>
    </AuthenticatedLayout>
</template>
