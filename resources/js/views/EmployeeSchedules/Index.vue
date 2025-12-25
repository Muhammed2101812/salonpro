<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Çalışan Programları</h1>
        <p class="mt-2 text-sm text-gray-600">Çalışanların haftalık çalışma saatlerini ve program şablonlarını yönetin</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="openTemplateModal"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <DocumentDuplicateIcon class="h-5 w-5 mr-2 text-gray-500" />
          Şablon Oluştur
        </button>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 transition-colors"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Program Ekle
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100">
            <CalendarDaysIcon class="h-6 w-6 text-purple-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Program</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.totalSchedules }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <UserGroupIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Aktif Çalışan</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.activeEmployees }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <ClockIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Haftalık Saat</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.weeklyHours }} saat</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-orange-100">
            <DocumentDuplicateIcon class="h-6 w-6 text-orange-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Program Şablonu</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.templates }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtreler ve Görünüm Seçici -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
      <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        <div class="flex flex-wrap gap-3">
          <!-- Çalışan Filtresi -->
          <select
            v-model="filters.employeeId"
            class="rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"
          >
            <option value="">Tüm Çalışanlar</option>
            <option v-for="emp in employees" :key="emp.id" :value="emp.id">
              {{ emp.first_name }} {{ emp.last_name }}
            </option>
          </select>

          <!-- Hafta Seçici -->
          <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
            <button
              @click="previousWeek"
              class="p-1 hover:bg-gray-200 rounded transition-colors"
            >
              <ChevronLeftIcon class="h-5 w-5 text-gray-600" />
            </button>
            <span class="text-sm font-medium text-gray-700 min-w-[200px] text-center">
              {{ formatWeekRange(currentWeekStart) }}
            </span>
            <button
              @click="nextWeek"
              class="p-1 hover:bg-gray-200 rounded transition-colors"
            >
              <ChevronRightIcon class="h-5 w-5 text-gray-600" />
            </button>
            <button
              @click="goToToday"
              class="ml-2 px-2 py-1 text-xs font-medium text-purple-600 hover:bg-purple-50 rounded transition-colors"
            >
              Bugün
            </button>
          </div>

          <!-- Durum Filtresi -->
          <select
            v-model="filters.status"
            class="rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"
          >
            <option value="">Tüm Durumlar</option>
            <option value="active">Aktif</option>
            <option value="inactive">Pasif</option>
            <option value="template">Şablon</option>
          </select>
        </div>

        <!-- Görünüm Seçici -->
        <div class="flex items-center gap-2">
          <div class="flex rounded-lg border border-gray-200 overflow-hidden">
            <button
              @click="viewMode = 'grid'"
              :class="[
                'px-3 py-2 text-sm font-medium transition-colors',
                viewMode === 'grid' ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              <Squares2X2Icon class="h-5 w-5" />
            </button>
            <button
              @click="viewMode = 'calendar'"
              :class="[
                'px-3 py-2 text-sm font-medium transition-colors',
                viewMode === 'calendar' ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              <CalendarIcon class="h-5 w-5" />
            </button>
            <button
              @click="viewMode = 'list'"
              :class="[
                'px-3 py-2 text-sm font-medium transition-colors',
                viewMode === 'list' ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              <ListBulletIcon class="h-5 w-5" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Takvim Görünümü -->
    <div v-if="viewMode === 'calendar'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <!-- Takvim Başlıkları -->
      <div class="grid grid-cols-8 bg-gray-50 border-b border-gray-200">
        <div class="px-4 py-3 text-sm font-semibold text-gray-700">Çalışan</div>
        <div
          v-for="day in weekDays"
          :key="day.date"
          class="px-2 py-3 text-center border-l border-gray-200"
          :class="isToday(day.date) ? 'bg-purple-50' : ''"
        >
          <div class="text-xs font-medium text-gray-500">{{ day.label }}</div>
          <div
            :class="[
              'text-lg font-bold',
              isToday(day.date) ? 'text-purple-600' : 'text-gray-900'
            ]"
          >
            {{ formatDayNumber(day.date) }}
          </div>
        </div>
      </div>

      <!-- Çalışan Satırları -->
      <div v-if="loading" class="p-12 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto"></div>
        <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
      </div>

      <div v-else>
        <div
          v-for="employee in filteredEmployees"
          :key="employee.id"
          class="grid grid-cols-8 border-b border-gray-100 hover:bg-gray-50 transition-colors"
        >
          <!-- Çalışan Bilgisi -->
          <div class="px-4 py-4 flex items-center">
            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white font-medium">
              {{ getInitials(employee.first_name, employee.last_name) }}
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-900">
                {{ employee.first_name }} {{ employee.last_name }}
              </p>
              <p class="text-xs text-gray-500">{{ employee.position || 'Çalışan' }}</p>
            </div>
          </div>

          <!-- Gün Hücreleri -->
          <div
            v-for="day in weekDays"
            :key="`${employee.id}-${day.date}`"
            class="px-2 py-3 border-l border-gray-100 min-h-[100px]"
            :class="isToday(day.date) ? 'bg-purple-50/30' : ''"
          >
            <!-- Çalışma Saatleri -->
            <div v-if="getScheduleForDay(employee.id, day.dayOfWeek)" class="space-y-1">
              <div
                v-for="slot in getScheduleForDay(employee.id, day.dayOfWeek).time_slots"
                :key="slot.id"
                class="px-2 py-1 rounded-lg text-xs bg-green-100 text-green-800"
              >
                <div class="font-medium">{{ slot.start }} - {{ slot.end }}</div>
              </div>
            </div>

            <!-- İzin/Tatil Gösterimi -->
            <div
              v-else-if="isHoliday(day.date) || isOnLeave(employee.id, day.date)"
              class="px-2 py-1 rounded-lg text-xs"
              :class="isHoliday(day.date) ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'"
            >
              {{ isHoliday(day.date) ? 'Tatil' : 'İzinli' }}
            </div>

            <!-- Boş Gün -->
            <div v-else class="text-center text-gray-400 text-xs py-2">
              Çalışmıyor
            </div>

            <!-- Ekleme Butonu -->
            <button
              @click="openScheduleForDay(employee, day)"
              class="mt-2 w-full px-2 py-1 border border-dashed border-gray-300 rounded-lg text-xs text-gray-500 hover:border-purple-500 hover:text-purple-600 transition-colors"
            >
              + Düzenle
            </button>
          </div>
        </div>

        <!-- Boş Durum -->
        <div v-if="filteredEmployees.length === 0" class="p-12 text-center">
          <UserGroupIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
          <p class="text-gray-500">Çalışan bulunamadı</p>
        </div>
      </div>
    </div>

    <!-- Grid Görünümü (Çalışan Kartları) -->
    <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="schedule in filteredSchedules"
        :key="schedule.id"
        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow"
      >
        <!-- Kart Başlığı -->
        <div class="p-4 border-b border-gray-100">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="flex-shrink-0 h-12 w-12 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white font-medium">
                {{ getInitials(schedule.employee?.first_name || 'U', schedule.employee?.last_name || 'N') }}
              </div>
              <div class="ml-3">
                <h3 class="text-lg font-semibold text-gray-900">
                  {{ schedule.employee?.first_name }} {{ schedule.employee?.last_name }}
                </h3>
                <p class="text-sm text-gray-500">{{ schedule.name || 'Haftalık Program' }}</p>
              </div>
            </div>
            <span
              :class="[
                'px-2 py-1 text-xs font-medium rounded-full',
                schedule.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'
              ]"
            >
              {{ schedule.is_active ? 'Aktif' : 'Pasif' }}
            </span>
          </div>
        </div>

        <!-- Haftalık Program -->
        <div class="p-4">
          <h4 class="text-sm font-medium text-gray-700 mb-3">Haftalık Çalışma Saatleri</h4>
          <div class="space-y-2">
            <div
              v-for="(daySchedule, dayKey) in schedule.weekly_schedule"
              :key="dayKey"
              class="flex items-center justify-between py-1"
            >
              <span class="text-sm text-gray-600 w-16">{{ getDayLabel(dayKey) }}</span>
              <div v-if="daySchedule.is_working" class="flex-1 ml-3">
                <div class="flex flex-wrap gap-1">
                  <span
                    v-for="(slot, index) in daySchedule.slots"
                    :key="index"
                    class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded text-xs"
                  >
                    {{ slot.start }} - {{ slot.end }}
                  </span>
                </div>
              </div>
              <span v-else class="text-sm text-gray-400 italic">İzinli</span>
            </div>
          </div>
        </div>

        <!-- Kart Footer -->
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
          <div class="text-sm text-gray-500">
            <ClockIcon class="h-4 w-4 inline mr-1" />
            {{ calculateTotalHours(schedule.weekly_schedule) }} saat/hafta
          </div>
          <div class="flex gap-2">
            <button
              @click="editSchedule(schedule)"
              class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
            >
              <PencilIcon class="h-4 w-4" />
            </button>
            <button
              @click="deleteSchedule(schedule)"
              class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
            >
              <TrashIcon class="h-4 w-4" />
            </button>
          </div>
        </div>
      </div>

      <!-- Boş Durum -->
      <div v-if="filteredSchedules.length === 0" class="col-span-full py-12 text-center">
        <CalendarDaysIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Henüz program oluşturulmamış</p>
        <button
          @click="openCreateModal"
          class="mt-4 text-purple-600 hover:text-purple-700 font-medium"
        >
          İlk programı oluşturun
        </button>
      </div>
    </div>

    <!-- Liste Görünümü -->
    <div v-if="viewMode === 'list'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Çalışan
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Program Adı
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Çalışma Günleri
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Haftalık Saat
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Durum
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              İşlemler
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="schedule in filteredSchedules" :key="schedule.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white font-medium">
                  {{ getInitials(schedule.employee?.first_name || 'U', schedule.employee?.last_name || 'N') }}
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900">
                    {{ schedule.employee?.first_name }} {{ schedule.employee?.last_name }}
                  </p>
                  <p class="text-xs text-gray-500">{{ schedule.employee?.position || 'Çalışan' }}</p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ schedule.name || 'Varsayılan Program' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex gap-1">
                <span
                  v-for="day in getWorkingDays(schedule.weekly_schedule)"
                  :key="day"
                  class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded text-xs"
                >
                  {{ day }}
                </span>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ calculateTotalHours(schedule.weekly_schedule) }} saat
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="[
                  'px-2 py-1 text-xs font-medium rounded-full',
                  schedule.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'
                ]"
              >
                {{ schedule.is_active ? 'Aktif' : 'Pasif' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="editSchedule(schedule)"
                class="text-purple-600 hover:text-purple-900 mr-3"
              >
                Düzenle
              </button>
              <button
                @click="deleteSchedule(schedule)"
                class="text-red-600 hover:text-red-900"
              >
                Sil
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Boş Durum -->
      <div v-if="filteredSchedules.length === 0" class="p-12 text-center">
        <CalendarDaysIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Henüz program oluşturulmamış</p>
      </div>
    </div>

    <!-- Program Oluşturma/Düzenleme Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">
            {{ editingSchedule ? 'Program Düzenle' : 'Yeni Program Oluştur' }}
          </h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="saveSchedule" class="p-6 space-y-6">
          <!-- Çalışan ve Program Adı -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Çalışan *</label>
              <select
                v-model="formData.employee_id"
                required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                :disabled="editingSchedule"
              >
                <option value="">Çalışan Seçin</option>
                <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                  {{ emp.first_name }} {{ emp.last_name }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Program Adı</label>
              <input
                v-model="formData.name"
                type="text"
                placeholder="Örn: Hafta İçi Programı"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
              />
            </div>
          </div>

          <!-- Şablon Seçimi -->
          <div v-if="!editingSchedule">
            <label class="block text-sm font-medium text-gray-700 mb-2">Şablondan Yükle</label>
            <div class="flex gap-2">
              <select
                v-model="selectedTemplate"
                class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
              >
                <option value="">Şablon Seçin (Opsiyonel)</option>
                <option v-for="template in templates" :key="template.id" :value="template.id">
                  {{ template.name }}
                </option>
              </select>
              <button
                type="button"
                @click="applyTemplate"
                :disabled="!selectedTemplate"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium disabled:opacity-50 transition-colors"
              >
                Uygula
              </button>
            </div>
          </div>

          <!-- Haftalık Program -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Haftalık Çalışma Saatleri</label>
            <div class="space-y-3">
              <div
                v-for="(day, index) in daysOfWeek"
                :key="day.key"
                class="flex items-start gap-4 p-4 rounded-lg border border-gray-200"
                :class="formData.weekly_schedule[day.key].is_working ? 'bg-white' : 'bg-gray-50'"
              >
                <!-- Gün Etkin/Devre Dışı -->
                <div class="flex items-center pt-1">
                  <input
                    :id="`day-${day.key}`"
                    type="checkbox"
                    v-model="formData.weekly_schedule[day.key].is_working"
                    class="h-5 w-5 rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                  />
                </div>

                <!-- Gün Adı -->
                <label :for="`day-${day.key}`" class="w-24 pt-1 text-sm font-medium text-gray-700 cursor-pointer">
                  {{ day.label }}
                </label>

                <!-- Çalışma Saatleri -->
                <div v-if="formData.weekly_schedule[day.key].is_working" class="flex-1">
                  <div
                    v-for="(slot, slotIndex) in formData.weekly_schedule[day.key].slots"
                    :key="slotIndex"
                    class="flex items-center gap-2 mb-2"
                  >
                    <input
                      v-model="slot.start"
                      type="time"
                      class="rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"
                    />
                    <span class="text-gray-500">-</span>
                    <input
                      v-model="slot.end"
                      type="time"
                      class="rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"
                    />
                    <button
                      v-if="formData.weekly_schedule[day.key].slots.length > 1"
                      type="button"
                      @click="removeTimeSlot(day.key, slotIndex)"
                      class="p-1 text-red-500 hover:bg-red-50 rounded"
                    >
                      <XMarkIcon class="h-4 w-4" />
                    </button>
                  </div>
                  <button
                    type="button"
                    @click="addTimeSlot(day.key)"
                    class="text-sm text-purple-600 hover:text-purple-700 font-medium"
                  >
                    + Zaman Dilimi Ekle
                  </button>
                </div>

                <div v-else class="flex-1 text-sm text-gray-400 italic pt-1">
                  Bu gün çalışmıyor
                </div>
              </div>
            </div>
          </div>

          <!-- Geçerlilik Tarihleri -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi</label>
              <input
                v-model="formData.start_date"
                type="date"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi</label>
              <input
                v-model="formData.end_date"
                type="date"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
              />
              <p class="mt-1 text-xs text-gray-500">Boş bırakılırsa süresiz geçerli olur</p>
            </div>
          </div>

          <!-- Durum ve Notlar -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="flex items-center">
                <input
                  type="checkbox"
                  v-model="formData.is_active"
                  class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                />
                <span class="ml-2 text-sm font-medium text-gray-700">Aktif Program</span>
              </label>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
            <textarea
              v-model="formData.notes"
              rows="2"
              placeholder="Programa ilişkin notlar..."
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
            ></textarea>
          </div>

          <!-- Toplam Saat Bilgisi -->
          <div class="bg-purple-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-purple-900">Haftalık Toplam Çalışma Saati</span>
              <span class="text-lg font-bold text-purple-700">{{ calculateTotalHours(formData.weekly_schedule) }} saat</span>
            </div>
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
              class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium disabled:opacity-50 transition-colors"
            >
              {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Şablon Oluşturma Modal -->
    <div v-if="showTemplateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-lg w-full">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h2 class="text-xl font-bold text-gray-900">Program Şablonu Oluştur</h2>
          <button @click="showTemplateModal = false" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="saveTemplate" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Şablon Adı *</label>
            <input
              v-model="templateForm.name"
              type="text"
              required
              placeholder="Örn: Tam Zamanlı Çalışan"
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
            <textarea
              v-model="templateForm.description"
              rows="2"
              placeholder="Şablon açıklaması..."
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
            ></textarea>
          </div>

          <!-- Hızlı Şablon Seçenekleri -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Hızlı Şablonlar</label>
            <div class="grid grid-cols-2 gap-2">
              <button
                type="button"
                @click="applyQuickTemplate('fulltime')"
                class="p-3 border border-gray-200 rounded-lg text-left hover:border-purple-500 hover:bg-purple-50 transition-colors"
              >
                <div class="font-medium text-gray-900">Tam Zamanlı</div>
                <div class="text-xs text-gray-500">Pzt-Cum 09:00-18:00</div>
              </button>
              <button
                type="button"
                @click="applyQuickTemplate('parttime')"
                class="p-3 border border-gray-200 rounded-lg text-left hover:border-purple-500 hover:bg-purple-50 transition-colors"
              >
                <div class="font-medium text-gray-900">Yarı Zamanlı</div>
                <div class="text-xs text-gray-500">Pzt-Cum 09:00-13:00</div>
              </button>
              <button
                type="button"
                @click="applyQuickTemplate('weekend')"
                class="p-3 border border-gray-200 rounded-lg text-left hover:border-purple-500 hover:bg-purple-50 transition-colors"
              >
                <div class="font-medium text-gray-900">Hafta Sonu</div>
                <div class="text-xs text-gray-500">Cmt-Paz 10:00-18:00</div>
              </button>
              <button
                type="button"
                @click="applyQuickTemplate('flexible')"
                class="p-3 border border-gray-200 rounded-lg text-left hover:border-purple-500 hover:bg-purple-50 transition-colors"
              >
                <div class="font-medium text-gray-900">Esnek</div>
                <div class="text-xs text-gray-500">Pzt-Cum 10:00-19:00</div>
              </button>
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button
              type="button"
              @click="showTemplateModal = false"
              class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors"
            >
              İptal
            </button>
            <button
              type="submit"
              class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors"
            >
              Şablon Oluştur
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
  CalendarDaysIcon,
  ClockIcon,
  UserGroupIcon,
  DocumentDuplicateIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  Squares2X2Icon,
  CalendarIcon,
  ListBulletIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'
import { useEmployeeScheduleStore } from '@/stores/employeeschedule'

interface TimeSlot {
  start: string
  end: string
}

interface DaySchedule {
  is_working: boolean
  slots: TimeSlot[]
}

interface WeeklySchedule {
  [key: string]: DaySchedule
}

interface Schedule {
  id: string
  employee_id: string
  employee?: {
    id: string
    first_name: string
    last_name: string
    position?: string
  }
  name: string
  weekly_schedule: WeeklySchedule
  start_date?: string
  end_date?: string
  is_active: boolean
  notes?: string
}

interface Employee {
  id: string
  first_name: string
  last_name: string
  position?: string
}

const store = useEmployeeScheduleStore()

// State
const loading = ref(true)
const saving = ref(false)
const showModal = ref(false)
const showTemplateModal = ref(false)
const editingSchedule = ref<Schedule | null>(null)
const viewMode = ref<'grid' | 'calendar' | 'list'>('calendar')
const selectedTemplate = ref('')
const currentWeekStart = ref(getMonday(new Date()))

const schedules = ref<Schedule[]>([])
const employees = ref<Employee[]>([])
const templates = ref<any[]>([])

const filters = ref({
  employeeId: '',
  status: ''
})

const stats = ref({
  totalSchedules: 0,
  activeEmployees: 0,
  weeklyHours: 0,
  templates: 0
})

const daysOfWeek = [
  { key: 'monday', label: 'Pazartesi' },
  { key: 'tuesday', label: 'Salı' },
  { key: 'wednesday', label: 'Çarşamba' },
  { key: 'thursday', label: 'Perşembe' },
  { key: 'friday', label: 'Cuma' },
  { key: 'saturday', label: 'Cumartesi' },
  { key: 'sunday', label: 'Pazar' }
]

const defaultWeeklySchedule: WeeklySchedule = {
  monday: { is_working: true, slots: [{ start: '09:00', end: '18:00' }] },
  tuesday: { is_working: true, slots: [{ start: '09:00', end: '18:00' }] },
  wednesday: { is_working: true, slots: [{ start: '09:00', end: '18:00' }] },
  thursday: { is_working: true, slots: [{ start: '09:00', end: '18:00' }] },
  friday: { is_working: true, slots: [{ start: '09:00', end: '18:00' }] },
  saturday: { is_working: false, slots: [{ start: '09:00', end: '18:00' }] },
  sunday: { is_working: false, slots: [{ start: '09:00', end: '18:00' }] }
}

const formData = ref({
  employee_id: '',
  name: '',
  weekly_schedule: JSON.parse(JSON.stringify(defaultWeeklySchedule)),
  start_date: '',
  end_date: '',
  is_active: true,
  notes: ''
})

const templateForm = ref({
  name: '',
  description: '',
  weekly_schedule: JSON.parse(JSON.stringify(defaultWeeklySchedule))
})

// Computed
const weekDays = computed(() => {
  const days = []
  const labels = ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz']
  const dayKeys = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']
  for (let i = 0; i < 7; i++) {
    const date = new Date(currentWeekStart.value)
    date.setDate(date.getDate() + i)
    days.push({
      date: date.toISOString().split('T')[0],
      label: labels[i],
      dayOfWeek: dayKeys[i]
    })
  }
  return days
})

const filteredEmployees = computed(() => {
  if (!filters.value.employeeId) return employees.value
  return employees.value.filter(emp => emp.id === filters.value.employeeId)
})

const filteredSchedules = computed(() => {
  let result = schedules.value

  if (filters.value.employeeId) {
    result = result.filter(s => s.employee_id === filters.value.employeeId)
  }

  if (filters.value.status === 'active') {
    result = result.filter(s => s.is_active)
  } else if (filters.value.status === 'inactive') {
    result = result.filter(s => !s.is_active)
  }

  return result
})

// Helpers
function getMonday(date: Date): Date {
  const d = new Date(date)
  const day = d.getDay()
  const diff = d.getDate() - day + (day === 0 ? -6 : 1)
  return new Date(d.setDate(diff))
}

const previousWeek = () => {
  const newDate = new Date(currentWeekStart.value)
  newDate.setDate(newDate.getDate() - 7)
  currentWeekStart.value = newDate
}

const nextWeek = () => {
  const newDate = new Date(currentWeekStart.value)
  newDate.setDate(newDate.getDate() + 7)
  currentWeekStart.value = newDate
}

const goToToday = () => {
  currentWeekStart.value = getMonday(new Date())
}

const isToday = (dateStr: string) => {
  return dateStr === new Date().toISOString().split('T')[0]
}

const formatWeekRange = (startDate: Date) => {
  const endDate = new Date(startDate)
  endDate.setDate(endDate.getDate() + 6)
  return `${startDate.toLocaleDateString('tr-TR', { day: '2-digit', month: 'long' })} - ${endDate.toLocaleDateString('tr-TR', { day: '2-digit', month: 'long', year: 'numeric' })}`
}

const formatDayNumber = (date: string) => {
  return new Date(date).getDate()
}

const getInitials = (firstName: string, lastName: string) => {
  return `${firstName?.charAt(0) || ''}${lastName?.charAt(0) || ''}`.toUpperCase()
}

const getDayLabel = (key: string) => {
  const labels: Record<string, string> = {
    monday: 'Pzt',
    tuesday: 'Sal',
    wednesday: 'Çar',
    thursday: 'Per',
    friday: 'Cum',
    saturday: 'Cmt',
    sunday: 'Paz'
  }
  return labels[key] || key
}

const getScheduleForDay = (employeeId: string, dayOfWeek: string) => {
  const schedule = schedules.value.find(s => s.employee_id === employeeId && s.is_active)
  if (!schedule) return null
  
  const daySchedule = schedule.weekly_schedule[dayOfWeek]
  if (!daySchedule || !daySchedule.is_working) return null
  
  return {
    ...daySchedule,
    time_slots: daySchedule.slots.map((slot, index) => ({
      id: index,
      start: slot.start,
      end: slot.end
    }))
  }
}

const isHoliday = (date: string) => {
  // Basit tatil kontrolü - gerçek uygulamada API'dan alınabilir
  return false
}

const isOnLeave = (employeeId: string, date: string) => {
  // İzin kontrolü - gerçek uygulamada API'dan alınabilir
  return false
}

const getWorkingDays = (weeklySchedule: WeeklySchedule) => {
  const days: string[] = []
  if (weeklySchedule.monday?.is_working) days.push('Pzt')
  if (weeklySchedule.tuesday?.is_working) days.push('Sal')
  if (weeklySchedule.wednesday?.is_working) days.push('Çar')
  if (weeklySchedule.thursday?.is_working) days.push('Per')
  if (weeklySchedule.friday?.is_working) days.push('Cum')
  if (weeklySchedule.saturday?.is_working) days.push('Cmt')
  if (weeklySchedule.sunday?.is_working) days.push('Paz')
  return days
}

const calculateTotalHours = (weeklySchedule: WeeklySchedule) => {
  let totalMinutes = 0
  
  Object.values(weeklySchedule).forEach(day => {
    if (day.is_working) {
      day.slots.forEach(slot => {
        const start = slot.start.split(':').map(Number)
        const end = slot.end.split(':').map(Number)
        const startMinutes = start[0] * 60 + start[1]
        const endMinutes = end[0] * 60 + end[1]
        totalMinutes += endMinutes - startMinutes
      })
    }
  })
  
  return Math.round(totalMinutes / 60 * 10) / 10
}

// Time slot management
const addTimeSlot = (dayKey: string) => {
  formData.value.weekly_schedule[dayKey].slots.push({ start: '09:00', end: '18:00' })
}

const removeTimeSlot = (dayKey: string, index: number) => {
  formData.value.weekly_schedule[dayKey].slots.splice(index, 1)
}

// Modal functions
const openCreateModal = () => {
  editingSchedule.value = null
  formData.value = {
    employee_id: '',
    name: '',
    weekly_schedule: JSON.parse(JSON.stringify(defaultWeeklySchedule)),
    start_date: new Date().toISOString().split('T')[0],
    end_date: '',
    is_active: true,
    notes: ''
  }
  selectedTemplate.value = ''
  showModal.value = true
}

const editSchedule = (schedule: Schedule) => {
  editingSchedule.value = schedule
  formData.value = {
    employee_id: schedule.employee_id,
    name: schedule.name,
    weekly_schedule: JSON.parse(JSON.stringify(schedule.weekly_schedule)),
    start_date: schedule.start_date || '',
    end_date: schedule.end_date || '',
    is_active: schedule.is_active,
    notes: schedule.notes || ''
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingSchedule.value = null
}

const openScheduleForDay = (employee: Employee, day: any) => {
  const existingSchedule = schedules.value.find(s => s.employee_id === employee.id && s.is_active)
  
  if (existingSchedule) {
    editSchedule(existingSchedule)
  } else {
    formData.value.employee_id = employee.id
    formData.value.name = `${employee.first_name} ${employee.last_name} - Haftalık Program`
    openCreateModal()
    formData.value.employee_id = employee.id
  }
}

const openTemplateModal = () => {
  templateForm.value = {
    name: '',
    description: '',
    weekly_schedule: JSON.parse(JSON.stringify(defaultWeeklySchedule))
  }
  showTemplateModal.value = true
}

// Template functions
const applyTemplate = () => {
  const template = templates.value.find(t => t.id === selectedTemplate.value)
  if (template) {
    formData.value.weekly_schedule = JSON.parse(JSON.stringify(template.weekly_schedule))
  }
}

const applyQuickTemplate = (type: string) => {
  const templateSchedules: Record<string, WeeklySchedule> = {
    fulltime: {
      monday: { is_working: true, slots: [{ start: '09:00', end: '18:00' }] },
      tuesday: { is_working: true, slots: [{ start: '09:00', end: '18:00' }] },
      wednesday: { is_working: true, slots: [{ start: '09:00', end: '18:00' }] },
      thursday: { is_working: true, slots: [{ start: '09:00', end: '18:00' }] },
      friday: { is_working: true, slots: [{ start: '09:00', end: '18:00' }] },
      saturday: { is_working: false, slots: [{ start: '09:00', end: '18:00' }] },
      sunday: { is_working: false, slots: [{ start: '09:00', end: '18:00' }] }
    },
    parttime: {
      monday: { is_working: true, slots: [{ start: '09:00', end: '13:00' }] },
      tuesday: { is_working: true, slots: [{ start: '09:00', end: '13:00' }] },
      wednesday: { is_working: true, slots: [{ start: '09:00', end: '13:00' }] },
      thursday: { is_working: true, slots: [{ start: '09:00', end: '13:00' }] },
      friday: { is_working: true, slots: [{ start: '09:00', end: '13:00' }] },
      saturday: { is_working: false, slots: [{ start: '09:00', end: '13:00' }] },
      sunday: { is_working: false, slots: [{ start: '09:00', end: '13:00' }] }
    },
    weekend: {
      monday: { is_working: false, slots: [{ start: '10:00', end: '18:00' }] },
      tuesday: { is_working: false, slots: [{ start: '10:00', end: '18:00' }] },
      wednesday: { is_working: false, slots: [{ start: '10:00', end: '18:00' }] },
      thursday: { is_working: false, slots: [{ start: '10:00', end: '18:00' }] },
      friday: { is_working: false, slots: [{ start: '10:00', end: '18:00' }] },
      saturday: { is_working: true, slots: [{ start: '10:00', end: '18:00' }] },
      sunday: { is_working: true, slots: [{ start: '10:00', end: '18:00' }] }
    },
    flexible: {
      monday: { is_working: true, slots: [{ start: '10:00', end: '19:00' }] },
      tuesday: { is_working: true, slots: [{ start: '10:00', end: '19:00' }] },
      wednesday: { is_working: true, slots: [{ start: '10:00', end: '19:00' }] },
      thursday: { is_working: true, slots: [{ start: '10:00', end: '19:00' }] },
      friday: { is_working: true, slots: [{ start: '10:00', end: '19:00' }] },
      saturday: { is_working: false, slots: [{ start: '10:00', end: '19:00' }] },
      sunday: { is_working: false, slots: [{ start: '10:00', end: '19:00' }] }
    }
  }
  
  templateForm.value.weekly_schedule = JSON.parse(JSON.stringify(templateSchedules[type]))
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

    // Programları yükle
    const response = await store.fetchAll()
    schedules.value = response?.data || []
    
    // İstatistikleri hesapla
    stats.value.totalSchedules = schedules.value.length
    stats.value.activeEmployees = new Set(schedules.value.filter(s => s.is_active).map(s => s.employee_id)).size
    stats.value.weeklyHours = schedules.value
      .filter(s => s.is_active)
      .reduce((sum, s) => sum + calculateTotalHours(s.weekly_schedule), 0)
    stats.value.templates = templates.value.length

  } catch (error) {
    console.error('Veri yükleme hatası:', error)
  } finally {
    loading.value = false
  }
}

const saveSchedule = async () => {
  saving.value = true
  try {
    if (editingSchedule.value) {
      await store.update(editingSchedule.value.id, formData.value)
    } else {
      await store.create(formData.value)
    }
    closeModal()
    await loadData()
  } catch (error) {
    console.error('Kaydetme hatası:', error)
    alert('Program kaydedilemedi')
  } finally {
    saving.value = false
  }
}

const deleteSchedule = async (schedule: Schedule) => {
  if (!confirm('Bu programı silmek istediğinizden emin misiniz?')) return
  
  try {
    await store.delete(schedule.id)
    await loadData()
  } catch (error) {
    console.error('Silme hatası:', error)
    alert('Program silinemedi')
  }
}

const saveTemplate = async () => {
  try {
    templates.value.push({
      id: Date.now().toString(),
      name: templateForm.value.name,
      description: templateForm.value.description,
      weekly_schedule: JSON.parse(JSON.stringify(templateForm.value.weekly_schedule))
    })
    showTemplateModal.value = false
    stats.value.templates = templates.value.length
  } catch (error) {
    console.error('Şablon kaydetme hatası:', error)
    alert('Şablon kaydedilemedi')
  }
}

onMounted(() => {
  loadData()
})
</script>