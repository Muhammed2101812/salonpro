<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Yetenek ve Uzmanlıklar</h1>
        <p class="mt-2 text-sm text-gray-600">Çalışanların hizmet yeteneklerini ve uzmanlık seviyelerini yönetin</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="toggleView"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <component :is="viewMode === 'list' ? Squares2X2Icon : ListBulletIcon" class="h-5 w-5 mr-2 text-gray-500" />
          {{ viewMode === 'list' ? 'Matris Görünümü' : 'Liste Görünümü' }}
        </button>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 transition-colors"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Yetenek Ekle
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-rose-100">
            <SparklesIcon class="h-6 w-6 text-rose-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Yetenek</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.totalSkills }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100">
            <StarIcon class="h-6 w-6 text-purple-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Uzman Seviye</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.expertLevel }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <UserGroupIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Yetenekli Çalışan</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.skilledEmployees }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <ChartBarIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Ort. Yetenek/Çalışan</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.avgSkillsPerEmployee }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtreler -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
      <div class="flex flex-wrap gap-3 items-center">
        <!-- Çalışan Filtresi -->
        <select
          v-model="filters.employeeId"
          class="rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm"
        >
          <option value="">Tüm Çalışanlar</option>
          <option v-for="emp in employees" :key="emp.id" :value="emp.id">
            {{ emp.first_name }} {{ emp.last_name }}
          </option>
        </select>

        <!-- Kategori Filtresi -->
        <select
          v-model="filters.category"
          class="rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm"
        >
          <option value="">Tüm Kategoriler</option>
          <option v-for="cat in skillCategories" :key="cat.value" :value="cat.value">
            {{ cat.label }}
          </option>
        </select>

        <!-- Seviye Filtresi -->
        <div class="flex rounded-lg border border-gray-200 overflow-hidden">
          <button
            v-for="level in skillLevels"
            :key="level.value"
            @click="filters.level = filters.level === level.value ? '' : level.value"
            :class="[
              'px-3 py-2 text-xs font-medium transition-colors',
              filters.level === level.value
                ? level.activeClass
                : 'bg-white text-gray-700 hover:bg-gray-50'
            ]"
          >
            {{ level.label }}
          </button>
        </div>
      </div>
    </div>

    <!-- Matris Görünümü -->
    <div v-if="viewMode === 'matrix'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-4 border-b border-gray-200">
        <h3 class="font-semibold text-gray-900">Yetenek Matrisi</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10">
                Çalışan
              </th>
              <th
                v-for="skill in uniqueSkillNames"
                :key="skill"
                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[100px]"
              >
                {{ skill }}
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="emp in employeeSkillMatrix" :key="emp.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 whitespace-nowrap sticky left-0 bg-white z-10">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-br from-rose-500 to-pink-500 flex items-center justify-center text-white text-xs font-medium">
                    {{ getInitials(emp.first_name, emp.last_name) }}
                  </div>
                  <span class="ml-2 text-sm font-medium text-gray-900">{{ emp.first_name }}</span>
                </div>
              </td>
              <td
                v-for="skill in uniqueSkillNames"
                :key="skill"
                class="px-4 py-3 text-center"
              >
                <div v-if="emp.skills[skill]" class="flex justify-center">
                  <div
                    :class="[
                      'px-2 py-1 rounded-full text-xs font-medium',
                      getLevelBadgeColor(emp.skills[skill])
                    ]"
                  >
                    {{ getLevelShortLabel(emp.skills[skill]) }}
                  </div>
                </div>
                <span v-else class="text-gray-300">-</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Liste Görünümü - Çalışan Kartları -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="emp in employeeSkillCards"
        :key="emp.id"
        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow"
      >
        <div class="p-5">
          <div class="flex items-center gap-3 mb-4">
            <div class="flex-shrink-0 h-14 w-14 rounded-full bg-gradient-to-br from-rose-500 to-pink-500 flex items-center justify-center text-white font-bold text-lg">
              {{ getInitials(emp.first_name, emp.last_name) }}
            </div>
            <div>
              <h4 class="font-semibold text-gray-900">{{ emp.first_name }} {{ emp.last_name }}</h4>
              <p class="text-sm text-gray-500">{{ emp.position || 'Çalışan' }}</p>
            </div>
          </div>

          <div class="space-y-3">
            <div
              v-for="skill in emp.skills"
              :key="skill.id"
              class="flex items-center justify-between p-2 rounded-lg bg-gray-50"
            >
              <div class="flex items-center gap-2">
                <div :class="['w-2 h-2 rounded-full', getCategoryColor(skill.category)]"></div>
                <span class="text-sm font-medium text-gray-700">{{ skill.name }}</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="flex">
                  <StarIcon
                    v-for="star in 5"
                    :key="star"
                    class="h-4 w-4"
                    :class="star <= getLevelStars(skill.level) ? 'text-yellow-400 fill-current' : 'text-gray-300'"
                  />
                </div>
                <button
                  @click="editSkill(skill)"
                  class="p-1 text-gray-400 hover:text-rose-600 rounded transition-colors"
                >
                  <PencilIcon class="h-3 w-3" />
                </button>
              </div>
            </div>
          </div>

          <button
            @click="addSkillToEmployee(emp)"
            class="mt-4 w-full py-2 text-sm font-medium text-rose-600 hover:bg-rose-50 rounded-lg border border-dashed border-rose-300 transition-colors"
          >
            + Yetenek Ekle
          </button>
        </div>
      </div>
    </div>

    <!-- Boş Durum -->
    <div v-if="filteredSkills.length === 0 && !loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <SparklesIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
      <p class="text-gray-500">Yetenek kaydı bulunamadı</p>
      <button
        @click="openCreateModal"
        class="mt-4 text-rose-600 hover:text-rose-700 font-medium"
      >
        Yetenek ekleyin
      </button>
    </div>

    <!-- Yükleniyor -->
    <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-rose-600 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Yetenek Ekleme/Düzenleme Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">
            {{ editingSkill ? 'Yetenek Düzenle' : 'Yeni Yetenek' }}
          </h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="saveSkill" class="p-6 space-y-5">
          <!-- Çalışan Seçimi -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Çalışan *</label>
            <select
              v-model="formData.employee_id"
              required
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
            >
              <option value="">Çalışan Seçin</option>
              <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                {{ emp.first_name }} {{ emp.last_name }}
              </option>
            </select>
          </div>

          <!-- Yetenek Adı -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Yetenek Adı *</label>
            <input
              v-model="formData.name"
              type="text"
              required
              placeholder="Örn: Saç Kesimi"
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
            />
          </div>

          <!-- Kategori -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
            <div class="grid grid-cols-2 gap-2">
              <button
                v-for="cat in skillCategories"
                :key="cat.value"
                type="button"
                @click="formData.category = cat.value"
                :class="[
                  'p-3 rounded-lg border text-left transition-colors',
                  formData.category === cat.value
                    ? 'border-rose-500 bg-rose-50'
                    : 'border-gray-200 hover:border-rose-300'
                ]"
              >
                <div class="flex items-center gap-2">
                  <div :class="['w-3 h-3 rounded-full', cat.color]"></div>
                  <span class="text-sm font-medium text-gray-900">{{ cat.label }}</span>
                </div>
              </button>
            </div>
          </div>

          <!-- Seviye -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Uzmanlık Seviyesi *</label>
            <div class="space-y-2">
              <button
                v-for="level in skillLevels"
                :key="level.value"
                type="button"
                @click="formData.level = level.value"
                :class="[
                  'w-full p-3 rounded-lg border text-left transition-colors',
                  formData.level === level.value
                    ? level.selectedClass
                    : 'border-gray-200 hover:border-rose-300'
                ]"
              >
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="flex">
                      <StarIcon
                        v-for="star in 5"
                        :key="star"
                        class="h-4 w-4"
                        :class="star <= level.stars ? 'text-yellow-400 fill-current' : 'text-gray-300'"
                      />
                    </div>
                    <span class="text-sm font-medium text-gray-900">{{ level.label }}</span>
                  </div>
                  <span class="text-xs text-gray-500">{{ level.description }}</span>
                </div>
              </button>
            </div>
          </div>

          <!-- Notlar -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
            <textarea
              v-model="formData.notes"
              rows="2"
              placeholder="Ek bilgiler..."
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
            ></textarea>
          </div>

          <!-- Form Butonları -->
          <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors"
            >
              İptal
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="px-6 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-medium disabled:opacity-50 transition-colors"
            >
              {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import {
  PlusIcon,
  SparklesIcon,
  StarIcon,
  UserGroupIcon,
  ChartBarIcon,
  PencilIcon,
  XMarkIcon,
  Squares2X2Icon,
  ListBulletIcon
} from '@heroicons/vue/24/outline'
import { useEmployeeSkillStore } from '@/stores/employeeskill'

interface Skill {
  id: string
  employee_id: string
  employee?: {
    id: string
    first_name: string
    last_name: string
    position?: string
  }
  name: string
  category: string
  level: string
  notes?: string
}

interface Employee {
  id: string
  first_name: string
  last_name: string
  position?: string
}

const store = useEmployeeSkillStore()

// State
const loading = ref(true)
const saving = ref(false)
const showModal = ref(false)
const editingSkill = ref<Skill | null>(null)
const viewMode = ref<'list' | 'matrix'>('list')

const skills = ref<Skill[]>([])
const employees = ref<Employee[]>([])

const filters = ref({
  employeeId: '',
  category: '',
  level: ''
})

const stats = ref({
  totalSkills: 0,
  expertLevel: 0,
  skilledEmployees: 0,
  avgSkillsPerEmployee: 0
})

const skillCategories = [
  { value: 'hair', label: 'Saç', color: 'bg-pink-500' },
  { value: 'nails', label: 'Tırnak', color: 'bg-purple-500' },
  { value: 'skin', label: 'Cilt', color: 'bg-green-500' },
  { value: 'makeup', label: 'Makyaj', color: 'bg-rose-500' },
  { value: 'massage', label: 'Masaj', color: 'bg-blue-500' },
  { value: 'other', label: 'Diğer', color: 'bg-gray-500' }
]

const skillLevels = [
  { value: 'beginner', label: 'Başlangıç', stars: 1, description: 'Yeni öğreniyor', activeClass: 'bg-gray-600 text-white', selectedClass: 'border-gray-500 bg-gray-50' },
  { value: 'intermediate', label: 'Orta', stars: 2, description: 'Temel işlemleri yapıyor', activeClass: 'bg-blue-600 text-white', selectedClass: 'border-blue-500 bg-blue-50' },
  { value: 'advanced', label: 'İleri', stars: 3, description: 'Bağımsız çalışabiliyor', activeClass: 'bg-green-600 text-white', selectedClass: 'border-green-500 bg-green-50' },
  { value: 'expert', label: 'Uzman', stars: 4, description: 'Tüm tekniklere hakim', activeClass: 'bg-purple-600 text-white', selectedClass: 'border-purple-500 bg-purple-50' },
  { value: 'master', label: 'Usta', stars: 5, description: 'Eğitim verebilir', activeClass: 'bg-rose-600 text-white', selectedClass: 'border-rose-500 bg-rose-50' }
]

const formData = ref({
  employee_id: '',
  name: '',
  category: 'hair',
  level: 'intermediate',
  notes: ''
})

// Computed
const filteredSkills = computed(() => {
  let result = skills.value

  if (filters.value.employeeId) {
    result = result.filter(s => s.employee_id === filters.value.employeeId)
  }

  if (filters.value.category) {
    result = result.filter(s => s.category === filters.value.category)
  }

  if (filters.value.level) {
    result = result.filter(s => s.level === filters.value.level)
  }

  return result
})

const uniqueSkillNames = computed(() => {
  const names = new Set(skills.value.map(s => s.name))
  return Array.from(names).sort()
})

const employeeSkillMatrix = computed(() => {
  return employees.value.map(emp => {
    const empSkills = skills.value.filter(s => s.employee_id === emp.id)
    const skillMap: Record<string, string> = {}
    empSkills.forEach(s => {
      skillMap[s.name] = s.level
    })
    return {
      ...emp,
      skills: skillMap
    }
  })
})

const employeeSkillCards = computed(() => {
  return employees.value.map(emp => {
    const empSkills = filteredSkills.value.filter(s => s.employee_id === emp.id)
    return {
      ...emp,
      skills: empSkills
    }
  }).filter(emp => emp.skills.length > 0 || !filters.value.employeeId)
})

// Helpers
const getInitials = (firstName: string, lastName: string) => {
  return `${firstName?.charAt(0) || ''}${lastName?.charAt(0) || ''}`.toUpperCase()
}

const getLevelStars = (level: string) => {
  return skillLevels.find(l => l.value === level)?.stars || 1
}

const getLevelBadgeColor = (level: string) => {
  const colors: Record<string, string> = {
    beginner: 'bg-gray-100 text-gray-700',
    intermediate: 'bg-blue-100 text-blue-700',
    advanced: 'bg-green-100 text-green-700',
    expert: 'bg-purple-100 text-purple-700',
    master: 'bg-rose-100 text-rose-700'
  }
  return colors[level] || 'bg-gray-100 text-gray-700'
}

const getLevelShortLabel = (level: string) => {
  const labels: Record<string, string> = {
    beginner: 'B',
    intermediate: 'O',
    advanced: 'İ',
    expert: 'U',
    master: 'M'
  }
  return labels[level] || level.charAt(0).toUpperCase()
}

const getCategoryColor = (category: string) => {
  return skillCategories.find(c => c.value === category)?.color || 'bg-gray-500'
}

// Actions
const toggleView = () => {
  viewMode.value = viewMode.value === 'list' ? 'matrix' : 'list'
}

const openCreateModal = () => {
  editingSkill.value = null
  formData.value = {
    employee_id: '',
    name: '',
    category: 'hair',
    level: 'intermediate',
    notes: ''
  }
  showModal.value = true
}

const addSkillToEmployee = (emp: Employee) => {
  editingSkill.value = null
  formData.value = {
    employee_id: emp.id,
    name: '',
    category: 'hair',
    level: 'intermediate',
    notes: ''
  }
  showModal.value = true
}

const editSkill = (skill: Skill) => {
  editingSkill.value = skill
  formData.value = {
    employee_id: skill.employee_id,
    name: skill.name,
    category: skill.category,
    level: skill.level,
    notes: skill.notes || ''
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingSkill.value = null
}

// CRUD Operations
const loadData = async () => {
  loading.value = true
  try {
    // Çalışanları yükle
    const empResponse = await fetch('/api/employees')
    if (empResponse.ok) {
      const data = await empResponse.json()
      employees.value = data.data || []
    }

    // Yetenekleri yükle
    const response = await store.fetchAll()
    skills.value = response?.data || []

    updateStats()
  } catch (error) {
    console.error('Veri yükleme hatası:', error)
  } finally {
    loading.value = false
  }
}

const updateStats = () => {
  stats.value.totalSkills = skills.value.length
  stats.value.expertLevel = skills.value.filter(s => s.level === 'expert' || s.level === 'master').length
  
  const employeeIds = new Set(skills.value.map(s => s.employee_id))
  stats.value.skilledEmployees = employeeIds.size
  stats.value.avgSkillsPerEmployee = employeeIds.size > 0 
    ? Math.round(skills.value.length / employeeIds.size * 10) / 10 
    : 0
}

const saveSkill = async () => {
  saving.value = true
  try {
    if (editingSkill.value) {
      await store.update(editingSkill.value.id, formData.value)
    } else {
      await store.create(formData.value)
    }
    closeModal()
    await loadData()
  } catch (error) {
    console.error('Kaydetme hatası:', error)
    alert('Yetenek kaydedilemedi')
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  loadData()
})
</script>