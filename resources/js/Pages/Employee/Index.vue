<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, createVNode, ref, h } from 'vue';
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
        title: 'Full Name',
        dataIndex: 'full_name',
        sorter: true,
    },
    {
        title: 'Company',
        dataIndex: 'company',
        sorter: true,
    },
    {
        title: 'Email',
        dataIndex: 'email',
        sorter: true,
    },
    {
        title: 'Phone',
        dataIndex: 'phone',
        sorter: true,
    },
    {
        title: 'Action',
        dataIndex: 'action',
    },
];

const queryData = async params => {
    const res = await axios.get('/employee/list', { params });
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
        title: 'Do you want to delete this employee?',
        icon: createVNode(ExclamationCircleOutlined),
        onOk() {
            formDelete.delete(route('employee.destroy', id), {
                preserveScroll: true,
                onSuccess: () => run({ page: current.value, results: pageSize.value }),
            });
        },
    });
};

const open = ref(false);
const editingEmployee = ref(null);

const showModal = (record) => {
    editingEmployee.value = record;
    open.value = true;
};

const closeModal = () => {
    open.value = false;
    editingEmployee.value = null;
};

const handleSuccess = () => {
    closeModal();
    run({ page: current.value, results: pageSize.value });
};

const showCompany = (record) => {
    Modal.info({
        title: record.name,
        content: h('div', [
            h('img', {
                src: `storage/${record.logo}`,
                class: 'w-[100px]',
            }),
            h('p', [
                'Email: ',
                h('a', {
                    href: `mailto:${record.email}`,
                    class: 'text-blue-500 hover:text-blue-700 underline transition duration-150'
                }, record.email),
            ]),
            h('p', [
                'Website: ',
                h('a', {
                    href: record.website,
                    target: '_blank',
                    rel: 'noopener noreferrer',
                    class: 'text-blue-500 hover:text-blue-700 underline transition duration-150'
                }, record.website),
            ]),
        ]),
    });
};
</script>

<template>
    <Head title="Employee" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Employee
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Link :href="route('employee.create')">
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
                            <template v-if="column.dataIndex === 'company'">
                                <a @click="showCompany(record.company)">{{ record.company.name }}</a>
                            </template>
                            <template v-else-if="column.dataIndex === 'email'">
                                <a :href="`mailto:${record.email}`">{{ record.email }}</a>
                            </template>
                            <template v-else-if="column.dataIndex === 'phone'">
                                <a :href="`tel:${record.phone}`">{{ record.phone }}</a>
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
            title="Edit Employee"
            :footer="null"
            destroyOnClose
            @cancel="closeModal"
        >
            <Form
                :employee="editingEmployee"
                :is-modal="true"
                @success="handleSuccess"
                @cancel="closeModal"
            />
        </a-modal>
    </AuthenticatedLayout>
</template>
