<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    center: Object,
    stats: Object,
    markers: Array,
    subjects: Array,
});

const activeTab = ref('overview');
const showAddStudentModal = ref(false);
const showBulkUploadModal = ref(false);
const uploadFile = ref(null);
const previewData = ref([]);
const showPreview = ref(false);
const isProcessing = ref(false);

const studentForm = useForm({
    full_name: '',
    registration_number: '',
    gender: '',
    date_of_birth: '',
    exam_center_code: props.center.center_code,
    status: 'active',
});

const formatNumber = (num) => new Intl.NumberFormat('en-US').format(num);

const getGradeColor = (grade) => {
    if (!grade) return '';
    const g = grade.toUpperCase();
    if (g === 'A') return 'text-green-700 bg-green-100';
    if (g === 'B') return 'text-blue-700 bg-blue-100';
    if (g === 'C') return 'text-yellow-700 bg-yellow-100';
    if (g === 'D') return 'text-orange-700 bg-orange-100';
    if (g === 'F') return 'text-red-700 bg-red-100';
    return 'text-gray-700';
};

const openAddStudentModal = () => {
    showAddStudentModal.value = true;
};

const closeAddStudentModal = () => {
    showAddStudentModal.value = false;
    resetForm();
};

const resetForm = () => {
    studentForm.reset();
};

const submitStudent = () => {
    // Validate basic fields before submission
    if (!studentForm.full_name || !studentForm.registration_number || !studentForm.gender) {
        alert('Tafadhali jaza Jina Kamili, Namba ya Usajili na Jinsia');
        return;
    }
    
    studentForm.post(route('emas.centers.candidates.store', props.center.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeAddStudentModal();
            window.location.reload();
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join('\n');
            alert('Kuna makosa:\n' + errorMessages);
        },
    });
};

const openBulkUploadModal = () => {
    showBulkUploadModal.value = true;
};

const deleteCandidate = (candidateId, candidateName) => {
    if (confirm(`Una uhakika unataka kufuta ${candidateName}? Hatua hii haiwezi kurudishwa!`)) {
        router.delete(route('emas.centers.candidates.destroy', [props.center.id, candidateId]), {
            preserveScroll: true,
            onSuccess: () => {
                window.location.reload();
            },
        });
    }
};

const closeBulkUploadModal = () => {
    showBulkUploadModal.value = false;
    uploadFile.value = null;
    previewData.value = [];
    showPreview.value = false;
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    
    uploadFile.value = file;
    
    // Parse CSV file to show preview
    const reader = new FileReader();
    reader.onload = (e) => {
        const text = e.target.result;
        const lines = text.split('\n').filter(line => line.trim());
        
        if (lines.length < 2) {
            alert('File haina data ya kutosha!');
            return;
        }
        
        // Parse CSV
        const headers = lines[0].split(',').map(h => h.trim());
        const data = [];
        
        for (let i = 1; i < lines.length; i++) {
            const values = lines[i].split(',').map(v => v.trim());
            const row = {};
            headers.forEach((header, index) => {
                row[header] = values[index] || '';
            });
            data.push(row);
        }
        
        previewData.value = data;
        showPreview.value = true;
    };
    
    reader.readAsText(file);
};

const submitBulkUpload = async () => {
    if (!uploadFile.value) {
        alert('Tafadhali chagua file kwanza!');
        return;
    }
    
    isProcessing.value = true;
    
    const formData = new FormData();
    formData.append('file', uploadFile.value);
    formData.append('center_code', props.center.center_code);
    
    try {
        await router.post(route('emas.centers.candidates.bulk-upload', props.center.id), formData, {
            preserveScroll: true,
            onSuccess: () => {
                closeBulkUploadModal();
                window.location.reload();
            },
            onError: (errors) => {
                alert('Kuna makosa: ' + JSON.stringify(errors));
            },
            onFinish: () => {
                isProcessing.value = false;
            },
        });
    } catch (error) {
        console.error('Upload error:', error);
        alert('Kuna tatizo limetokea wakati wa upload!');
        isProcessing.value = false;
    }
};

const downloadSampleFile = () => {
    // Create sample CSV data
    const headers = ['REGISTRATION_NUMBER', 'FULL_NAME', 'GENDER', 'DATE_OF_BIRTH', 'STATUS'];
    const sampleData = [
        ['S3364-0001', 'JOHN DOE MWANGI', 'M', '2005-05-15', 'active'],
        ['S3364-0002', 'MARY JANE KAMAU', 'F', '2005-08-20', 'active'],
        ['S3364-0003', 'PETER PAUL NJOROGE', 'M', '2005-03-10', 'active'],
    ];
    
    const csvContent = [
        headers.join(','),
        ...sampleData.map(row => row.join(','))
    ].join('\n');
    
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `sample_students_${props.center.center_code}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
};
</script>

<template>
    <Head :title="`${center.center_name} - EMAS`" />

    <EmasLayout>
        <!-- Success Message -->
        <div v-if="$page.props.flash.success" class="mb-6 rounded-lg bg-emerald-50 border-2 border-emerald-200 p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-sm font-bold text-emerald-900">{{ $page.props.flash.success }}</p>
            </div>
        </div>

        <!-- Error Message -->
        <div v-if="$page.props.errors.error" class="mb-6 rounded-lg bg-red-50 border-2 border-red-200 p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-500">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <p class="text-sm font-bold text-red-900">{{ $page.props.errors.error }}</p>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Link :href="route('emas.centers.index')" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ center.center_name }}</h1>
                        <p class="mt-1 text-sm text-gray-600">{{ center.center_code }} · {{ center.district }}, {{ center.region }}</p>
                    </div>
                </div>
                <Link :href="route('emas.centers.edit', center.id)" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Hariri Kituo
                </Link>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-lg bg-white p-6 shadow-sm border-l-4 border-emerald-500">
                    <p class="text-sm font-medium text-gray-600">Wanafunzi</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-700">{{ formatNumber(stats.total_candidates) }}</p>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm border-l-4 border-blue-500">
                    <p class="text-sm font-medium text-gray-600">Mitihani</p>
                    <p class="mt-2 text-3xl font-bold text-blue-700">{{ formatNumber(stats.total_exams) }}</p>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm border-l-4 border-purple-500">
                    <p class="text-sm font-medium text-gray-600">Uwezo</p>
                    <p class="mt-2 text-3xl font-bold text-purple-700">{{ formatNumber(center.capacity) }}</p>
                </div>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex gap-6">
                    <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500'" class="border-b-2 py-3 text-sm font-bold">Muhtasari</button>
                    <button @click="activeTab = 'students'" :class="activeTab === 'students' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500'" class="border-b-2 py-3 text-sm font-bold">Wanafunzi ({{ stats.total_candidates }})</button>
                    <button @click="activeTab = 'coordinator'" :class="activeTab === 'coordinator' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500'" class="border-b-2 py-3 text-sm font-bold">Msimamizi</button>
                    <button @click="activeTab = 'markers'" :class="activeTab === 'markers' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500'" class="border-b-2 py-3 text-sm font-bold">Wakaguzi ({{ markers.length }})</button>
                    <button @click="activeTab = 'subjects'" :class="activeTab === 'subjects' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500'" class="border-b-2 py-3 text-sm font-bold">Masomo ({{ subjects.length }})</button>
                </nav>
            </div>

            <!-- Overview -->
            <div v-if="activeTab === 'overview'" class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold mb-4">Taarifa za Kituo</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between py-2 border-b"><dt class="text-gray-600">Jina</dt><dd class="font-bold">{{ center.center_name }}</dd></div>
                        <div class="flex justify-between py-2 border-b"><dt class="text-gray-600">Code</dt><dd class="font-bold">{{ center.center_code }}</dd></div>
                        <div class="flex justify-between py-2 border-b"><dt class="text-gray-600">Mkoa</dt><dd class="font-bold">{{ center.region }}</dd></div>
                        <div class="flex justify-between py-2 border-b"><dt class="text-gray-600">Wilaya</dt><dd class="font-bold">{{ center.district }}</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-gray-600">Hali</dt><dd><span :class="center.status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800'" class="px-2 py-1 rounded-full text-xs font-bold">{{ center.status }}</span></dd></div>
                    </dl>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold mb-4">Mawasiliano</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="border-b pb-3"><dt class="text-gray-600 text-xs">Anuani</dt><dd class="font-bold mt-1">{{ center.address }}</dd></div>
                        <div class="border-b pb-3"><dt class="text-gray-600 text-xs">Simu</dt><dd class="font-bold mt-1">{{ center.phone }}</dd></div>
                        <div v-if="center.email"><dt class="text-gray-600 text-xs">Email</dt><dd class="font-bold mt-1">{{ center.email }}</dd></div>
                    </dl>
                </div>
            </div>

            <!-- Students Tab -->
            <div v-if="activeTab === 'students'" class="space-y-4">
                <!-- Students Header -->
                <div class="rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-black">Orodha ya Wanafunzi</h3>
                            <p class="text-emerald-100 mt-1">{{ center.center_name }}</p>
                            <p class="text-sm text-emerald-200 mt-1">Jumla: {{ formatNumber(stats.total_candidates) }} wanafunzi</p>
                        </div>
                        <div class="flex gap-3">
                            <Link :href="route('emas.centers.results', center.id)" class="rounded-lg bg-purple-600 hover:bg-purple-700 px-6 py-3 text-white font-black shadow-lg transition-colors flex items-center gap-2">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                View Results
                            </Link>
                            <button @click="openBulkUploadModal" class="rounded-lg bg-blue-500 hover:bg-blue-600 px-6 py-3 text-white font-black shadow-lg transition-colors flex items-center gap-2">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                Bulk Upload
                            </button>
                            <button @click="openAddStudentModal" class="rounded-lg bg-yellow-400 hover:bg-yellow-500 px-6 py-3 text-emerald-900 font-black shadow-lg transition-colors flex items-center gap-2">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                Ongeza Mwanafunzi
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="rounded-lg bg-white p-4 shadow-sm">
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <input type="text" placeholder="Tafuta mwanafunzi kwa jina au namba..." class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                        </div>
                        <select class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-emerald-500">
                            <option value="">Jinsia Zote</option>
                            <option value="M">Wanaume</option>
                            <option value="F">Wanawake</option>
                        </select>
                        <select class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-emerald-500">
                            <option value="">Hali Zote</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <button class="rounded-lg bg-emerald-600 hover:bg-emerald-700 px-6 py-2 text-white font-bold text-sm transition-colors flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Export Excel
                        </button>
                    </div>
                </div>

                <!-- Students Table -->
                <div class="rounded-lg bg-white shadow-sm overflow-hidden border-2 border-emerald-200">
                    <div v-if="center.candidates && center.candidates.length > 0" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300 border-collapse">
                            <thead>
                                <tr>
                                    <th colspan="4" class="bg-emerald-700 px-4 py-3 text-center text-sm font-black text-white uppercase border border-gray-300">
                                        {{ center.center_name }} - EMAS EXAMINATION RESULTS
                                    </th>
                                    <th v-for="subject in subjects" :key="subject" :colspan="2" class="bg-blue-600 px-3 py-2 text-center text-xs font-black text-white uppercase border border-gray-300">
                                        {{ subject }}
                                    </th>
                                    <th rowspan="2" class="bg-emerald-700 px-2 py-3 text-center text-xs font-black text-white uppercase border border-gray-300">Action</th>
                                </tr>
                                <tr>
                                    <th class="bg-gray-100 px-2 py-2 text-center text-xs font-black text-gray-700 uppercase border border-gray-300 sticky left-0 z-10">#</th>
                                    <th class="bg-gray-100 px-3 py-2 text-left text-xs font-black text-gray-700 uppercase border border-gray-300 min-w-[120px]">Reg No</th>
                                    <th class="bg-gray-100 px-4 py-2 text-left text-xs font-black text-gray-700 uppercase border border-gray-300 min-w-[250px]">Candidate Full Name</th>
                                    <th class="bg-gray-100 px-2 py-2 text-center text-xs font-black text-gray-700 uppercase border border-gray-300">Sex</th>
                                    <template v-for="subject in subjects" :key="subject">
                                        <th class="bg-gray-50 px-2 py-2 text-center text-xs font-bold text-gray-600 uppercase border border-gray-300 w-12">Score</th>
                                        <th class="bg-gray-50 px-2 py-2 text-center text-xs font-bold text-gray-600 uppercase border border-gray-300 w-12">Grade</th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <tr v-for="(candidate, index) in center.candidates" :key="candidate.id" class="hover:bg-emerald-50 transition-colors">
                                    <td class="px-2 py-3 text-center text-sm font-bold text-gray-900 border border-gray-300 sticky left-0 bg-white z-10">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="px-3 py-3 text-left text-sm font-black text-emerald-700 border border-gray-300 bg-emerald-50">
                                        {{ candidate.registration_number }}
                                    </td>
                                    <td class="px-4 py-3 text-left text-sm font-bold text-gray-900 border border-gray-300">
                                        {{ candidate.full_name }}
                                    </td>
                                    <td class="px-2 py-3 text-center border border-gray-300">
                                        <span :class="candidate.gender === 'M' ? 'text-blue-700' : 'text-pink-700'" class="text-sm font-black">
                                            {{ candidate.gender }}
                                        </span>
                                    </td>
                                    <template v-for="subject in subjects" :key="subject">
                                        <td class="px-2 py-3 text-center text-sm font-bold border border-gray-300">
                                            {{ candidate.results?.[subject]?.score || '-' }}
                                        </td>
                                        <td class="px-2 py-3 text-center text-sm font-black border border-gray-300" :class="getGradeColor(candidate.results?.[subject]?.grade)">
                                            {{ candidate.results?.[subject]?.grade || '-' }}
                                        </td>
                                    </template>
                                    <td class="px-2 py-3 text-center border border-gray-300">
                                        <button 
                                            @click="deleteCandidate(candidate.id, candidate.full_name)"
                                            class="rounded-lg bg-red-600 hover:bg-red-700 px-3 py-1.5 text-xs font-black text-white transition-colors flex items-center gap-1 mx-auto"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Futa
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="p-12 text-center">
                        <div class="mx-auto h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Hakuna Wanafunzi</h3>
                        <p class="text-gray-600 mb-4">Anza kuongeza wanafunzi wa kituo hiki</p>
                        <button @click="openAddStudentModal" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-6 py-3 text-sm font-bold text-white hover:bg-emerald-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Ongeza Mwanafunzi wa Kwanza
                        </button>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="center.candidates && center.candidates.length > 0" class="rounded-lg bg-white px-6 py-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Kuonyesha <span class="font-bold">1</span> hadi <span class="font-bold">{{ center.candidates.length }}</span> kati ya <span class="font-bold">{{ formatNumber(stats.total_candidates) }}</span> wanafunzi
                        </div>
                        <div class="flex gap-2">
                            <button class="rounded-lg border-2 border-gray-300 px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">Nyuma</button>
                            <button class="rounded-lg border-2 border-gray-300 px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">Mbele</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coordinator -->
            <div v-if="activeTab === 'coordinator'" class="rounded-lg bg-white p-6 shadow-sm">
                <div class="flex justify-between mb-6">
                    <h3 class="text-lg font-bold">Msimamizi wa Kituo</h3>
                    <Link :href="route('emas.centers.edit', center.id)" class="text-sm font-bold text-emerald-600">Hariri</Link>
                </div>
                <div v-if="center.coordinator_name" class="flex gap-4">
                    <div class="h-16 w-16 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <svg class="h-8 w-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold">{{ center.coordinator_name }}</h4>
                        <p class="text-sm text-gray-600">Msimamizi</p>
                        <p v-if="center.coordinator_phone" class="mt-2 font-bold text-gray-900">{{ center.coordinator_phone }}</p>
                    </div>
                </div>
                <div v-else class="text-center py-12">
                    <p class="text-gray-500">Hakuna msimamizi</p>
                    <Link :href="route('emas.centers.edit', center.id)" class="mt-4 inline-block rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white">Ongeza Msimamizi</Link>
                </div>
            </div>

            <!-- Markers -->
            <div v-if="activeTab === 'markers'" class="rounded-lg bg-white shadow-sm">
                <div class="p-6 border-b flex justify-between">
                    <h3 class="text-lg font-bold">Wakaguzi</h3>
                    <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white">Ongeza Mkaguzi</button>
                </div>
                <div v-if="markers.length > 0">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Jina</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Masomo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="marker in markers" :key="marker.id">
                                <td class="px-6 py-4">
                                    <div class="font-bold">{{ marker.full_name }}</div>
                                    <div class="text-xs text-gray-500">{{ marker.username }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm">{{ marker.email }}</td>
                                <td class="px-6 py-4">
                                    <span v-for="subject in marker.assigned_subjects" :key="subject" class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-bold mr-1">{{ subject }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="p-12 text-center text-gray-500">Hakuna wakaguzi</div>
            </div>

            <!-- Subjects -->
            <div v-if="activeTab === 'subjects'" class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold mb-4">Masomo Yanayopatikana</h3>
                <div v-if="subjects.length > 0" class="grid gap-3 sm:grid-cols-3">
                    <div v-for="subject in subjects" :key="subject" class="rounded-lg border-2 border-emerald-200 bg-emerald-50 p-4">
                        <div class="flex gap-2 items-center">
                            <div class="rounded-lg bg-emerald-500 p-2">
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <span class="font-bold">{{ subject }}</span>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-12 text-gray-500">Hakuna masomo</div>
            </div>
        </div>

        <!-- Add Student Modal -->
        <Teleport to="body">
            <div v-if="showAddStudentModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeAddStudentModal">
                <div class="flex min-h-screen items-center justify-center p-4">
                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-black/50 transition-opacity" @click="closeAddStudentModal"></div>
                    
                    <!-- Modal Content -->
                    <div class="relative w-full max-w-2xl transform rounded-2xl bg-white shadow-2xl transition-all">
                        <!-- Modal Header -->
                        <div class="rounded-t-2xl bg-gradient-to-r from-emerald-600 to-teal-600 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-2xl font-black text-white">Ongeza Mwanafunzi Mpya</h3>
                                    <p class="text-emerald-100 text-sm mt-1">{{ center.center_name }}</p>
                                </div>
                                <button @click="closeAddStudentModal" class="rounded-lg bg-white/20 hover:bg-white/30 p-2 transition-colors">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Body -->
                        <form @submit.prevent="submitStudent" class="p-6 space-y-6">
                            <!-- Full Name -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Jina Kamili <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="studentForm.full_name"
                                    type="text"
                                    required
                                    placeholder="Mfano: JOHN DOE MWANGI"
                                    class="w-full rounded-lg border-2 border-gray-300 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-200 transition-all"
                                />
                                <div v-if="studentForm.errors.full_name" class="mt-1 text-sm text-red-600">{{ studentForm.errors.full_name }}</div>
                            </div>

                            <!-- Registration Number & Gender -->
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">
                                        Namba ya Usajili <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="studentForm.registration_number"
                                        type="text"
                                        required
                                        placeholder="S3364-0001"
                                        class="w-full rounded-lg border-2 border-gray-300 px-4 py-3 text-sm uppercase focus:border-emerald-500 focus:ring-4 focus:ring-emerald-200 transition-all"
                                    />
                                    <div v-if="studentForm.errors.registration_number" class="mt-1 text-sm text-red-600">{{ studentForm.errors.registration_number }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">
                                        Jinsia <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        v-model="studentForm.gender"
                                        required
                                        class="w-full rounded-lg border-2 border-gray-300 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-200 transition-all"
                                    >
                                        <option value="">Chagua Jinsia</option>
                                        <option value="M">Mwanaume (M)</option>
                                        <option value="F">Mwanamke (F)</option>
                                    </select>
                                    <div v-if="studentForm.errors.gender" class="mt-1 text-sm text-red-600">{{ studentForm.errors.gender }}</div>
                                </div>
                            </div>

                            <!-- Date of Birth & Center Code -->
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">
                                        Tarehe ya Kuzaliwa
                                    </label>
                                    <input
                                        v-model="studentForm.date_of_birth"
                                        type="date"
                                        class="w-full rounded-lg border-2 border-gray-300 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-200 transition-all"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">
                                        Code ya Kituo
                                    </label>
                                    <input
                                        v-model="studentForm.exam_center_code"
                                        type="text"
                                        readonly
                                        class="w-full rounded-lg border-2 border-gray-200 bg-gray-100 px-4 py-3 text-sm text-gray-600 cursor-not-allowed"
                                    />
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Hali</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center cursor-pointer">
                                        <input v-model="studentForm.status" type="radio" value="active" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                                        <span class="ml-2 text-sm font-medium text-gray-700">Active</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input v-model="studentForm.status" type="radio" value="inactive" class="h-4 w-4 text-gray-600 focus:ring-gray-500">
                                        <span class="ml-2 text-sm font-medium text-gray-700">Inactive</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="flex items-center justify-end gap-3 pt-6 border-t">
                                <button
                                    type="button"
                                    @click="closeAddStudentModal"
                                    class="rounded-lg border-2 border-gray-300 px-6 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors"
                                >
                                    Ghairi
                                </button>
                                <button
                                    type="submit"
                                    :disabled="studentForm.processing"
                                    class="rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-3 text-sm font-black text-white shadow-lg hover:from-emerald-700 hover:to-teal-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                                >
                                    <span v-if="!studentForm.processing">Hifadhi Mwanafunzi</span>
                                    <span v-else class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Inahifadhi...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Bulk Upload Modal -->
        <Teleport to="body">
            <div v-if="showBulkUploadModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeBulkUploadModal">
                <div class="flex min-h-screen items-center justify-center p-4">
                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-black/50 transition-opacity" @click="closeBulkUploadModal"></div>
                    
                    <!-- Modal Content -->
                    <div class="relative w-full max-w-3xl transform rounded-2xl bg-white shadow-2xl transition-all">
                        <!-- Modal Header -->
                        <div class="rounded-t-2xl bg-gradient-to-r from-blue-600 to-indigo-600 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-2xl font-black text-white">Bulk Upload Wanafunzi</h3>
                                    <p class="text-blue-100 text-sm mt-1">{{ center.center_name }}</p>
                                </div>
                                <button @click="closeBulkUploadModal" class="rounded-lg bg-white/20 hover:bg-white/30 p-2 transition-colors">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-6 space-y-6">
                            <!-- Instructions -->
                            <div class="rounded-xl bg-blue-50 border-2 border-blue-200 p-6">
                                <div class="flex gap-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-500 flex-shrink-0">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-lg font-black text-blue-900 mb-2">Maelekezo</h4>
                                        <ol class="space-y-2 text-sm text-blue-800">
                                            <li class="flex gap-2">
                                                <span class="font-bold">1.</span>
                                                <span>Download sample file ili kuona format sahihi</span>
                                            </li>
                                            <li class="flex gap-2">
                                                <span class="font-bold">2.</span>
                                                <span>Jaza taarifa za wanafunzi kwenye Excel/CSV file</span>
                                            </li>
                                            <li class="flex gap-2">
                                                <span class="font-bold">3.</span>
                                                <span>Hakikisha format ni CSV (Comma Separated Values)</span>
                                            </li>
                                            <li class="flex gap-2">
                                                <span class="font-bold">4.</span>
                                                <span>Upload file hapa chini</span>
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <!-- Download Sample -->
                            <div class="rounded-xl bg-emerald-50 border-2 border-emerald-200 p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-lg font-bold text-emerald-900">Sample File</h4>
                                        <p class="text-sm text-emerald-700 mt-1">Download sample file ili kuona format</p>
                                    </div>
                                    <button @click="downloadSampleFile" class="rounded-lg bg-emerald-600 hover:bg-emerald-700 px-6 py-3 text-white font-black shadow-lg transition-colors flex items-center gap-2">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Download Sample
                                    </button>
                                </div>
                            </div>

                            <!-- File Upload Area -->
                            <div class="rounded-xl border-4 border-dashed border-gray-300 hover:border-blue-400 transition-colors p-8">
                                <div class="text-center">
                                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <div class="mt-4">
                                        <label class="cursor-pointer">
                                            <span class="text-base font-bold text-blue-600 hover:text-blue-700">Bofya hapa kupakia file</span>
                                            <span class="text-gray-600"> au drag & drop</span>
                                            <input @change="handleFileUpload" type="file" accept=".csv,.xlsx,.xls" class="hidden" />
                                        </label>
                                        <p class="text-xs text-gray-500 mt-2">CSV, XLSX hadi 5MB</p>
                                    </div>
                                    <div v-if="uploadFile" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-blue-100 px-4 py-2">
                                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="text-sm font-bold text-blue-900">{{ uploadFile.name }}</span>
                                        <button @click="uploadFile = null" class="text-blue-600 hover:text-blue-800">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Table -->
                            <div v-if="showPreview && previewData.length > 0" class="rounded-xl bg-white border-2 border-emerald-500 overflow-hidden">
                                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-black text-white">Preview - {{ previewData.length }} Wanafunzi</h4>
                                                <p class="text-emerald-100 text-sm">Angalia data kabla ya ku-extract</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="max-h-96 overflow-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50 sticky top-0">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">#</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Reg Number</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Full Name</th>
                                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Gender</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">DOB</th>
                                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="(row, index) in previewData" :key="index" class="hover:bg-emerald-50">
                                                <td class="px-4 py-3 text-sm font-bold text-gray-900">{{ index + 1 }}</td>
                                                <td class="px-4 py-3 text-sm font-bold text-emerald-700">{{ row.REGISTRATION_NUMBER }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ row.FULL_NAME }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <span :class="row.GENDER === 'M' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800'" class="inline-flex rounded-full px-2 py-1 text-xs font-bold">
                                                        {{ row.GENDER }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-700">{{ row.DATE_OF_BIRTH || '-' }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-bold bg-emerald-100 text-emerald-800">
                                                        {{ row.STATUS || 'active' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Format Info -->
                            <div v-if="!showPreview" class="rounded-lg bg-gray-100 p-4">
                                <h5 class="text-sm font-bold text-gray-900 mb-2">Required Columns (CSV Format):</h5>
                                <div class="flex flex-wrap gap-2">
                                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-bold text-gray-700 border border-gray-300">REGISTRATION_NUMBER</span>
                                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-bold text-gray-700 border border-gray-300">FULL_NAME</span>
                                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-bold text-gray-700 border border-gray-300">GENDER (M/F)</span>
                                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-bold text-gray-700 border border-gray-300">DATE_OF_BIRTH</span>
                                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-bold text-gray-700 border border-gray-300">STATUS</span>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="flex items-center justify-end gap-3 pt-4 border-t">
                                <button
                                    type="button"
                                    @click="closeBulkUploadModal"
                                    class="rounded-lg border-2 border-gray-300 px-6 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors"
                                >
                                    Ghairi
                                </button>
                                <button
                                    type="button"
                                    @click="submitBulkUpload"
                                    :disabled="!uploadFile || isProcessing"
                                    class="rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-3 text-sm font-black text-white shadow-lg hover:from-emerald-700 hover:to-teal-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                                >
                                    <span v-if="!isProcessing" class="flex items-center gap-2">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        {{ showPreview ? 'Extract Wanafunzi' : 'Upload Wanafunzi' }}
                                    </span>
                                    <span v-else class="flex items-center gap-2">
                                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Inaingiza...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </EmasLayout>
</template>
