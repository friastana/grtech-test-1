<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { UploadOutlined } from '@ant-design/icons-vue';

const props = defineProps({
    company: {
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
    name: '',
    email: '',
    website: '',
    logo: null,
    _method: 'POST',
});

const fileList = ref([]);

watch(
    () => props.company,
    (newValue) => {
        if (newValue) {
            form.name = newValue.name;
            form.email = newValue.email;
            form.website = newValue.website;
            form._method = 'PUT';
        } else {
            form.reset();
            form._method = 'POST';
        }
        fileList.value = [];
        form.logo = null;
    },
    { immediate: true }
);

const handleBeforeUpload = (file) => {
    fileList.value = [file];
    form.logo = file;

    return false;
};

const handleRemove = () => {
    fileList.value = [];
    form.logo = null;
};

const onFinish = () => {
    if (props.company) {
        form.post(route('company.update', props.company.id), {
            forceFormData: true,
            onSuccess: () => {
                form.reset();
                emit('success');
            },
        });
    } else {
        form.post(route('company.store'), {
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
            label="Name"
            name="name"
            :help="form.errors.name"
            :rules="[{ required: true, message: 'Please input the company name!' }]"
        >
            <a-input v-model:value="form.name" />
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
            label="Logo"
            name="logo"
            :help="form.errors.logo"
        >
            <div v-if="props.company && props.company.logo" class="mb-3">
                <a-image
                    :width="100"
                    :src="`/storage/${props.company.logo}`"
                    class="rounded border border-gray-200 p-1"
                />
            </div>

            <a-upload
                list-type="picture"
                v-model:file-list="fileList"
                :before-upload="handleBeforeUpload"
                :max-count="1"
                @remove="handleRemove"
            >
                <a-button>
                    <template #icon><UploadOutlined /></template>
                    {{ props.company && props.company.logo ? 'Change Image' : 'Select Image' }}
                </a-button>
            </a-upload>
        </a-form-item>

        <a-form-item
            label="Website"
            name="website"
            :help="form.errors.website"
            :rules="[{ type: 'url', message: 'Please enter a valid URL!' }]"
        >
            <a-input v-model:value="form.website" />
        </a-form-item>

        <a-form-item :wrapper-col="{ offset: 8, span: 16 }">
            <a-button type="primary" html-type="submit" :loading="form.processing">Submit</a-button>

            <a-button v-if="isModal" style="margin-left: 10px" @click="handleCancel">Cancel</a-button>

            <Link v-else :href="route('company.index')">
                <a-button type="default" class="ml-4">Back</a-button>
            </Link>
        </a-form-item>
    </a-form>
</template>
