<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
    employee: {
        type: Object,
        default: null,
    },
    isModal: {
        type: Boolean,
        default: false,
    }
});

const emit = defineEmits(['success', 'cancel']);

const form = useForm({
    full_name: '',
    company_id: '',
    email: '',
    phone: '',
    _method: 'POST',
});

const companyOptions = ref([]);

const fetchCompanyOptions = async () => {
    try {
        const response = await axios.get('/company/list');

        companyOptions.value = response.data.results.map(company => ({
            label: company.name,
            value: company.id
        }));
    } catch (error) {}
};

onMounted(() => {
    fetchCompanyOptions();
});

watch(
    () => props.employee,
    (newValue) => {
        if (newValue) {
            form.full_name = newValue.full_name;
            form.company_id = newValue.company_id;
            form.email = newValue.email;
            form.phone = newValue.phone;
            form._method = 'PUT';
        } else {
            form.reset();
            form._method = 'POST';
        }
    },
    { immediate: true }
);

const onFinish = () => {
    if (props.employee) {
        form.post(route('employee.update', props.employee.id), {
            forceFormData: true,
            onSuccess: () => {
                form.reset();
                emit('success');
            },
        });
    } else {
        form.post(route('employee.store'), {
            forceFormData: true,
            onSuccess: () => {
                form.reset();
                emit('success');
            },
        });
    }
};

const handleCancel = () => {
    emit('cancel');
};
</script>

<template>
    <a-form
        name="basic"
        :label-col="{ span: 8 }"
        :model="form"
        :wrapper-col="{ span: 16 }"
        @finish="onFinish"
    >
        <a-form-item
            label="Full Name"
            name="full_name"
            :help="form.errors.full_name"
            :rules="[{ required: true, message: 'Please input the employee full name!' }]"
        >
            <a-input v-model:value="form.full_name" />
        </a-form-item>

        <a-form-item
            label="Company"
            name="company_id"
            :help="form.errors.company_id"
            :rules="[{ required: true, message: 'Please select the employee company!' }]
        ">
            <a-select
                v-model:value="form.company_id"
                :options="companyOptions"
             />
        </a-form-item>

        <a-form-item
            label="Email"
            name="email"
            :help="form.errors.email"
            :rules="[{ type: 'email', message: 'Please enter a valid email!' }]"
        >
            <a-input v-model:value="form.email" />
        </a-form-item>

        <a-form-item
            label="Phone"
            name="phone"
            :help="form.errors.phone"
        >
            <a-input v-model:value="form.phone" />
        </a-form-item>

        <a-form-item :wrapper-col="{ offset: 8, span: 16 }">
            <a-button type="primary" html-type="submit" :loading="form.processing">Submit</a-button>

            <a-button v-if="isModal" style="margin-left: 10px" @click="handleCancel">Cancel</a-button>

            <Link v-else :href="route('employee.index')">
                <a-button type="default" class="ml-4">Back</a-button>
            </Link>
        </a-form-item>
    </a-form>
</template>
