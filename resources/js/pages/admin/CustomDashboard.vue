<template>
    <div class="h-full flex flex-col transition-colors duration-300 bg-gray-50 dark:bg-[#0f1115]">

        <div
            class="sticky top-0 z-40 bg-white/80 dark:bg-[#1a1d22]/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 px-6 py-4 flex flex-col md:flex-row justify-between items-center gap-4 flex-none">
            <div>
                <h1
                    class="text-xl font-black uppercase tracking-tighter text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="text-2xl">🎛️</span> PRO DASHBOARD
                </h1>
                <p class="text-xs text-gray-500">Boshqaruv paneli konstruktori</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="relative group min-w-[200px]">
                    <span
                        class="absolute -top-2.5 left-3 bg-gray-50 dark:bg-[#0f1115] px-1 text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-cyan-500 transition-colors z-10">
                        Foydalanuvchi
                    </span>
                    <div
                        class="relative flex items-center bg-white dark:bg-[#1a1d22] border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-cyan-500 dark:hover:border-cyan-500 transition-all shadow-sm">
                        <span class="pl-3 text-lg grayscale group-hover:grayscale-0 transition-all">👤</span>
                        <select v-model="selectedUserId" @change="changeUser"
                            class="w-full bg-transparent py-2.5 pl-2 pr-10 text-xs font-black text-gray-900 dark:text-white outline-none appearance-none cursor-pointer z-20">
                            <option :value="currentUser.id"
                                class="text-gray-900 bg-white dark:bg-[#1a1d22] dark:text-white font-bold py-2">O'zim
                                uchun</option>
                            <option v-for="user in usersList" :key="user.id" :value="user.id"
                                class="text-gray-900 bg-white dark:bg-[#1a1d22] dark:text-white font-bold">{{ user.name
                                }}</option>
                        </select>
                        <div
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 group-hover:text-cyan-500 transition-colors pointer-events-none z-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="h-6 w-px bg-gray-300 dark:bg-gray-700 mx-1 hidden md:block"></div>

                <button @click="clearLayout"
                    class="bg-red-100 text-red-600 hover:bg-red-200 px-4 py-2.5 rounded-xl font-bold text-xs uppercase transition-all shadow-sm">🗑
                    Tozalash</button>

                <button @click="toggleEditMode"
                    :class="isEditMode ? 'bg-yellow-500 text-black shadow-yellow-500/50' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                    class="px-5 py-2.5 rounded-xl font-bold text-xs uppercase transition-all active:scale-95 shadow-md flex items-center gap-2">
                    <span v-if="isEditMode">💾 Saqlash</span>
                    <span v-else>✏️ Tahrirlash</span>
                </button>

                <button v-if="isEditMode" @click="showWidgetModal = true"
                    class="bg-cyan-600 hover:bg-cyan-500 text-white font-bold px-5 py-2.5 rounded-xl transition-all text-xs uppercase shadow-lg shadow-cyan-500/30 flex items-center gap-2 active:scale-95 animate-pulse">
                    <span>+</span> Vidjet
                </button>
            </div>
        </div>

        <div class="p-6 flex-1 relative flex flex-col overflow-y-auto custom-scrollbar">

            <div v-if="loadingLayout"
                class="absolute inset-0 z-50 flex items-center justify-center bg-gray-50/80 dark:bg-[#0f1115]/80 backdrop-blur-sm transition-all">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-8 h-8 border-4 border-cyan-500 border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-cyan-500 font-bold text-sm animate-pulse">Dashboard yuklanmoqda...</span>
                </div>
            </div>

            <GridLayout v-show="layout && layout.length > 0" v-model:layout="layout" :col-num="12" :row-height="30"
                :is-draggable="isEditMode" :is-resizable="isEditMode" :vertical-compact="true"
                :use-css-transforms="true" :responsive="true"
                :breakpoints="{ lg: 1200, md: 996, sm: 768, xs: 480, xxs: 0 }"
                :cols="{ lg: 12, md: 10, sm: 6, xs: 4, xxs: 2 }" class="w-full min-h-[500px]">
                <GridItem v-for="item in layout" :key="item.i" :x="item.x" :y="item.y" :w="item.w" :h="item.h"
                    :i="item.i"
                    class="rounded-2xl shadow-lg border transition-all duration-200 bg-white dark:bg-[#1a1d22] border-gray-200 dark:border-gray-800 flex flex-col group"
                    :class="{ 'border-dashed border-2 border-cyan-500 bg-cyan-50/50 dark:bg-cyan-900/20 cursor-move z-50': isEditMode }">
                    <div
                        class="absolute top-2 right-2 flex gap-2 z-[60] opacity-0 group-hover:opacity-100 transition-opacity">
                        <button
                            v-if="!isEditMode && ['chart', 'area_chart', 'gauge'].includes(getSafeCategory(item.type))"
                            @click="toggleFullscreen(item)"
                            class="w-7 h-7 flex items-center justify-center bg-gray-100 dark:bg-gray-700 text-gray-500 rounded-full hover:text-cyan-500 shadow-sm transition-all hover:scale-110"
                            title="Kengaytirish"><span class="text-sm font-bold">⤢</span></button>
                        <button v-if="isEditMode" @click="openSettings(item)"
                            class="w-7 h-7 flex items-center justify-center bg-blue-500 text-white rounded-full shadow-md hover:bg-blue-600 transition-all hover:scale-110"
                            title="Sozlamalar"><span class="text-sm">⚙️</span></button>
                        <button v-if="isEditMode" @click="removeItem(item.i)"
                            class="w-7 h-7 flex items-center justify-center bg-red-500 text-white rounded-full shadow-md hover:bg-red-600 transition-all hover:scale-110"
                            title="O'chirish"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg></button>
                    </div>

                    <div class="flex-1 w-full h-full overflow-hidden relative">

                        <div v-if="isWidgetLoading(item.i)"
                            class="absolute inset-0 bg-white/50 dark:bg-black/50 z-10 flex items-center justify-center backdrop-blur-[1px]">
                            <div
                                class="w-5 h-5 border-2 border-cyan-500 border-t-transparent rounded-full animate-spin">
                            </div>
                        </div>

                        <div v-if="getSafeCategory(item.type) === 'gauge'"
                            class="h-full flex flex-col justify-between p-4 items-center">
                            <h4 class="text-xs font-bold uppercase text-gray-500 w-full text-left">{{ item.data?.title
                                || 'Vidjet' }}</h4>
                            <div class="relative w-full flex-1 flex items-center justify-center">
                                <svg viewBox="0 0 100 60" class="w-full h-full max-h-[150px]">
                                    <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none" stroke="#374151" stroke-width="10"
                                        stroke-linecap="round" class="opacity-20 dark:opacity-100" />
                                    <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none"
                                        :stroke="item.data?.color || '#22c55e'" stroke-width="10" stroke-linecap="round"
                                        stroke-dasharray="125.6"
                                        :stroke-dashoffset="125.6 - (125.6 * ((getWidgetValue(item) || 0) / (getWidgetDef(item.type).max || 100)))"
                                        class="transition-all duration-1000 ease-out" />
                                </svg>
                                <div class="absolute bottom-0 flex flex-col items-center mb-2">
                                    <span class="text-3xl font-black text-gray-900 dark:text-white leading-none">{{
                                        getWidgetValue(item) || 0 }}</span>
                                    <span class="text-[10px] text-gray-500 font-bold uppercase">{{
                                        getWidgetDef(item.type).unit || '' }}</span>
                                </div>
                            </div>
                        </div>

                        <div v-else-if="getSafeCategory(item.type) === 'area_chart'"
                            class="h-full w-full flex flex-col p-4 relative overflow-hidden">
                            <div class="flex justify-between items-center z-10 mb-2">
                                <h4 class="text-xs font-bold uppercase text-gray-500">{{ item.data?.title || 'Grafik' }}
                                </h4>
                                <span class="text-xs font-bold text-white px-2 py-0.5 rounded-full"
                                    :style="{ backgroundColor: item.data?.color || '#22c55e' }">Live</span>
                            </div>
                            <div class="flex-1 w-full relative">
                                <svg viewBox="0 0 300 100" class="w-full h-full absolute bottom-0"
                                    preserveAspectRatio="none">
                                    <defs>
                                        <linearGradient :id="'grad-' + item.i" x1="0%" y1="0%" x2="0%" y2="100%">
                                            <stop offset="0%" :stop-color="item.data?.color || '#22c55e'"
                                                stop-opacity="0.5" />
                                            <stop offset="100%" :stop-color="item.data?.color || '#22c55e'"
                                                stop-opacity="0" />
                                        </linearGradient>
                                    </defs>
                                    <path :d="generateSplinePath(getWidgetValue(item))" :fill="`url(#grad-${item.i})`"
                                        :stroke="item.data?.color || '#22c55e'" stroke-width="2"
                                        class="drop-shadow-lg transition-all duration-500" />
                                </svg>
                            </div>
                        </div>

                        <div v-else-if="getSafeCategory(item.type) === 'stat'"
                            class="h-full flex flex-col justify-center items-center p-4 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-20 h-20 opacity-10 rounded-bl-full"
                                :style="{ backgroundColor: item.data?.color || '#06b6d4' }"></div>
                            <span class="text-3xl mb-2 filter drop-shadow-md">{{ getWidgetDef(item.type).icon || '📊'
                                }}</span>
                            <h3 class="text-3xl font-black text-gray-900 dark:text-white">{{ getWidgetValue(item) || 0
                                }}</h3>
                            <p class="text-[10px] uppercase font-bold text-gray-500 text-center tracking-wider">{{
                                item.data?.title || 'Statistika' }}</p>
                            <p class="text-[10px] font-bold mt-1 opacity-80"
                                :style="{ color: item.data?.color || '#06b6d4' }">{{ getWidgetDef(item.type).subtext ||
                                    '' }}</p>
                        </div>

                        <div v-else-if="getSafeCategory(item.type) === 'chart'" class="h-full w-full flex flex-col p-4">
                            <h4
                                class="text-xs font-bold uppercase text-gray-500 mb-2 flex justify-between items-center">
                                {{ item.data?.title || 'Grafik' }}
                                <span :style="{ color: item.data?.color || '#06b6d4' }" class="text-[10px] font-bold">↑
                                    Live</span>
                            </h4>
                            <div class="flex-1 flex items-end justify-between gap-1.5 pb-1 relative mt-2">
                                <template
                                    v-for="(val, idx) in (Array.isArray(getWidgetValue(item)) && getWidgetValue(item).length > 0 ? getWidgetValue(item) : [50, 30, 80, 20, 40, 90, 30])"
                                    :key="idx">
                                    <div
                                        class="w-full h-full flex flex-col justify-end items-center group relative cursor-pointer">
                                        <span
                                            class="absolute -top-7 bg-gray-800 dark:bg-gray-700 text-white text-[10px] font-bold px-2 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity z-10 pointer-events-none shadow-md whitespace-nowrap">
                                            {{ val }}
                                        </span>

                                        <div class="w-full rounded-t opacity-70 group-hover:opacity-100 transition-all duration-300"
                                            :style="{ backgroundColor: item.data?.color || '#06b6d4', height: ((val / getMaxValue(getWidgetValue(item))) * 100) + '%' }">
                                        </div>

                                        <span
                                            class="text-[9px] font-bold text-gray-500 dark:text-gray-400 mt-1 leading-none group-hover:text-gray-900 dark:group-hover:text-white transition-colors">
                                            {{ val }}
                                        </span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div v-else-if="getSafeCategory(item.type) === 'grid'" class="h-full w-full flex flex-col">
                            <div
                                class="p-3 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-[#1e2228]">
                                <h4
                                    class="text-xs font-black uppercase text-gray-500 tracking-wider flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full"
                                        :style="{ backgroundColor: item.data?.color || '#22c55e' }"></span>{{
                                            item.data?.title || 'Ro\'yxat' }}
                                </h4>
                                <span class="text-[10px] font-mono text-gray-400">{{ (getWidgetValue(item)?.length) || 0
                                    }} ta</span>
                            </div>
                            <div class="flex-1 overflow-y-auto p-2 custom-scrollbar">
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                                    <div v-for="(machine, idx) in (Array.isArray(getWidgetValue(item)) ? getWidgetValue(item) : [])"
                                        :key="idx"
                                        class="rounded-lg p-1.5 flex flex-col items-center justify-center transition-all hover:scale-105 cursor-pointer shadow-sm min-h-[50px] border"
                                        :style="{ backgroundColor: item.data?.color || '#22c55e', borderColor: item.data?.color || '#22c55e', color: isLightColor(item.data?.color) ? '#000' : '#fff' }">
                                        <span
                                            class="text-[9px] font-black opacity-70 uppercase tracking-tighter text-center leading-none">{{
                                                machine.label }}</span>
                                        <span class="text-sm font-black leading-tight mt-1">{{ machine.value }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else-if="getSafeCategory(item.type) === 'system'"
                            class="h-full flex flex-col justify-center p-5">
                            <div class="flex justify-between mb-2"><span
                                    class="text-xs font-bold text-gray-500 uppercase">{{ item.data?.title
                                        || 'Status' }}</span><span class="text-xs font-bold"
                                    :style="{ color: item.data?.color || '#3b82f6' }">{{
                                        getWidgetValue(item) || 0 }}%</span></div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 h-3 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-1000 shadow-[0_0_10px]"
                                    :style="{ backgroundColor: item.data?.color || '#3b82f6', width: (getWidgetValue(item) || 0) + '%' }">
                                </div>
                            </div>
                        </div>

                        <div v-else-if="getSafeCategory(item.type) === 'list'" class="h-full w-full flex flex-col">
                            <div
                                class="p-3 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-[#1e2228]">
                                <h4 class="text-xs font-black uppercase text-gray-500">{{ item.data?.title || 'Ro\'yxat'
                                    }}</h4>
                            </div>
                            <div class="flex-1 overflow-y-auto p-2 space-y-1 custom-scrollbar">
                                <div v-for="(row, idx) in (Array.isArray(getWidgetValue(item)) ? getWidgetValue(item) : [])"
                                    :key="idx"
                                    class="flex justify-between items-center p-2 hover:bg-gray-100 dark:hover:bg-white/5 rounded-lg transition-colors text-xs">
                                    <span class="font-bold dark:text-gray-300 truncate pr-2">{{ row.name }}</span>
                                    <span class="font-mono whitespace-nowrap"
                                        :class="(row.status === 'OK' || String(row.status).includes('%')) ? 'text-green-500' : 'text-red-500'">{{
                                            row.status }}</span>
                                </div>
                            </div>
                        </div>

                        <div v-else-if="item.type === 'WeatherWidget'"
                            class="h-full w-full flex flex-col justify-between p-5 bg-gradient-to-br from-blue-500 to-cyan-400 text-white relative overflow-hidden">
                            <div class="absolute -right-4 -top-4 text-9xl opacity-20">☀️</div>
                            <div class="flex justify-between items-start z-10">
                                <div>
                                    <h4 class="font-bold text-lg leading-none">Toshkent</h4>
                                    <p class="text-[10px] opacity-80 mt-1 uppercase tracking-wide">Bugun</p>
                                </div><span class="text-4xl drop-shadow-md">⛅</span>
                            </div>
                            <div class="z-10">
                                <h2 class="text-5xl font-black tracking-tighter">24°</h2>
                                <p class="text-xs font-bold opacity-90">Qisman bulutli</p>
                            </div>
                        </div>
                        <div v-else-if="item.type === 'ClockWidget'"
                            class="h-full w-full flex flex-col items-center justify-center bg-gray-900 text-white relative">
                            <div
                                class="text-4xl font-mono font-bold tracking-widest text-cyan-400 drop-shadow-[0_0_10px_rgba(34,211,238,0.8)]">
                                {{ currentTime }}</div>
                            <div class="text-xs text-gray-500 font-bold uppercase mt-1 tracking-[4px]">Toshkent vaqti
                            </div>
                        </div>
                        <div v-else-if="item.type === 'NoteWidget'"
                            class="h-full w-full flex flex-col p-4 bg-yellow-50 dark:bg-yellow-900/10">
                            <h4 class="text-xs font-bold uppercase text-yellow-600 dark:text-yellow-500 mb-2">📌 Eslatma
                            </h4><textarea v-model="item.data.text"
                                class="w-full flex-1 bg-transparent resize-none outline-none text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400/50 leading-relaxed custom-scrollbar"
                                placeholder="Yozib qo'ying..." :readonly="!isEditMode"></textarea>
                        </div>

                        <div v-else class="h-full w-full flex items-center justify-center text-gray-400 font-bold">
                            Noma'lum vidjet turi: {{ item.type }}
                        </div>

                    </div>
                </GridItem>
            </GridLayout>

            <div v-if="!loadingLayout && (!layout || layout.length === 0)"
                class="flex flex-col items-center justify-center flex-1 h-full text-gray-400 opacity-50 border-2 border-dashed border-gray-700 rounded-3xl m-6 min-h-[50vh]">
                <span class="text-6xl mb-4">🧩</span>
                <p class="uppercase font-bold tracking-widest text-center mb-2"><span
                        v-if="selectedUserId === currentUser.id">Sizning dashboardingiz bo'sh</span><span v-else>Ushbu
                        foydalanuvchida dashboard yo'q</span></p>
                <button @click="isEditMode = true; showWidgetModal = true"
                    class="text-cyan-500 underline hover:text-cyan-400">Vidjet qo'shish</button>
            </div>
        </div>

        <Transition name="fade">
            <div v-if="fullscreenItem"
                class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/80 backdrop-blur-md"
                @click.self="fullscreenItem = null">
                <div
                    class="bg-white dark:bg-[#1a1d22] w-[90vw] h-[85vh] rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-700 flex flex-col overflow-hidden relative scale-in-center">
                    <button @click="fullscreenItem = null"
                        class="absolute top-4 right-4 z-50 bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-red-500 p-2 rounded-full transition-colors"><svg
                            xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>

                    <div class="flex-1 p-8 flex flex-col items-center justify-center">
                        <div v-if="getSafeCategory(fullscreenItem.type) === 'chart'"
                            class="w-full h-full flex flex-col">
                            <h2 class="text-2xl font-bold mb-8 dark:text-white text-center">{{
                                fullscreenItem.data?.title }}
                            </h2>
                            <div class="flex-1 flex items-end justify-between gap-4 pb-4">
                                <template
                                    v-for="(val, idx) in (Array.isArray(getWidgetValue(fullscreenItem)) && getWidgetValue(fullscreenItem).length > 0 ? getWidgetValue(fullscreenItem) : [50, 30, 80, 20, 40, 90, 30])"
                                    :key="idx">
                                    <div
                                        class="w-full h-full flex flex-col justify-end items-center group relative cursor-pointer">
                                        <span
                                            class="text-xl font-black text-gray-400 mb-2 group-hover:text-white transition-colors">{{
                                                val }}</span>
                                        <div class="w-full rounded-t opacity-80 group-hover:opacity-100 transition-all duration-300"
                                            :style="{ backgroundColor: fullscreenItem.data?.color || '#06b6d4', height: ((val / getMaxValue(getWidgetValue(fullscreenItem))) * 100) + '%' }">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div v-else class="text-2xl dark:text-white text-center">To'liq ekran rejimi bu vidjet uchun
                            hozircha
                            ishlamaydi.</div>
                    </div>
                </div>
            </div>
        </Transition>

        <div v-if="showWidgetModal"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity">
            <div
                class="bg-white dark:bg-[#1a1d22] w-full max-w-5xl rounded-3xl border border-gray-200 dark:border-gray-700 shadow-2xl overflow-hidden flex flex-col max-h-[85vh] scale-in-center">
                <div
                    class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-[#1e2228]">
                    <h3
                        class="font-black text-xl text-gray-900 dark:text-white uppercase tracking-tight flex items-center gap-2">
                        <span class="text-2xl">📦</span> Vidjetlar Kutubxonasi
                    </h3>
                    <button @click="showWidgetModal = false"
                        class="text-gray-400 hover:text-red-500 text-2xl transition-colors">✕</button>
                </div>
                <div class="flex-1 overflow-y-auto p-8 bg-gray-50 dark:bg-[#0f1115] custom-scrollbar">
                    <div v-for="(group, name) in widgetGroups" :key="name" class="mb-10 last:mb-0">
                        <h4
                            class="text-xs font-black uppercase text-gray-500 mb-5 tracking-[4px] pl-1 border-b border-gray-200 dark:border-gray-800 pb-2">
                            {{ name }}</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                            <div v-for="widget in group" :key="widget.type"
                                @click="addItem(widget.type, widget.defaultW, widget.defaultH)"
                                class="bg-white dark:bg-[#1a1d22] p-5 rounded-2xl border border-gray-200 dark:border-gray-800 cursor-pointer hover:border-cyan-500 dark:hover:border-cyan-500 hover:shadow-cyan-500/20 hover:shadow-xl transition-all group relative overflow-hidden hover:-translate-y-1">
                                <div
                                    class="absolute top-0 right-0 p-1.5 bg-gray-100 dark:bg-gray-800 rounded-bl-xl text-[9px] font-mono text-gray-500 font-bold">
                                    {{ widget.defaultW }}x{{ widget.defaultH }}</div>
                                <div class="text-4xl mb-3 group-hover:scale-110 transition-transform duration-300">{{
                                    widget.icon }}</div>
                                <div class="font-bold text-sm text-gray-800 dark:text-gray-200 leading-tight mb-1.5">{{
                                    widget.title }}</div>
                                <div class="text-[10px] text-gray-500 line-clamp-2 leading-relaxed">{{ widget.desc }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showSettingsModal"
            class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div
                class="bg-white dark:bg-[#1a1d22] w-full max-w-md rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl p-6 scale-in-center">
                <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase mb-4 flex items-center gap-2">
                    <span class="text-blue-500">⚙️</span> Sozlamalar
                </h3>

                <div class="space-y-5 max-h-[60vh] overflow-y-auto custom-scrollbar pr-2">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sarlavha</label>
                        <input v-if="editingWidget.data" v-model="editingWidget.data.title" type="text"
                            placeholder="Vidjet nomi..."
                            class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-gray-700 rounded-lg p-2.5 text-sm text-gray-800 dark:text-white focus:border-cyan-500 outline-none transition-colors">
                    </div>

                    <div
                        v-if="['stat', 'gauge', 'chart', 'grid', 'list', 'system', 'area_chart'].includes(getSafeCategory(editingWidget.type))">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Qaysi ma'lumotni
                            chiqarmoqchisiz?</label>
                        <select v-if="editingWidget.data" v-model="editingWidget.data.dataKey"
                            class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-gray-700 rounded-lg p-2.5 text-sm text-gray-800 dark:text-gray-400 focus:border-cyan-500 outline-none transition-colors cursor-pointer font-mono">
                            <option value="">-- Avtomatik (Vidjet turiga qarab) --</option>
                            <option v-for="key in availableDataKeys" :key="key" :value="key">
                                {{ translateKey(key) }} ({{ Array.isArray(realData.GLOBAL[key]) ?
                                    realData.GLOBAL[key].length +
                                ' ta mashina' : realData.GLOBAL[key] }})
                            </option>
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1 leading-tight">Ushbu vidjetga Global API dan kelayotgan qaysi qiymatni ulashni tanlang.</p>
                    </div>

                    <div v-if="['grid', 'list', 'stat', 'gauge', 'chart', 'area_chart'].includes(getSafeCategory(editingWidget.type))">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Avtoguruhlarni tanlang</label>
                        <div class="bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-gray-700 rounded-lg p-2 max-h-32 overflow-y-auto custom-scrollbar flex flex-col gap-1">
                            <label v-for="tg in dbTransportGroups" :key="tg.id" 
                                   class="flex items-center gap-3 p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-800 cursor-pointer transition-colors">
                                <input type="checkbox" :value="tg.id" v-model="editingWidget.data.transport_groups"
                                       class="w-4 h-4 text-cyan-500 rounded border-gray-300 focus:ring-cyan-500 dark:bg-gray-700 dark:border-gray-600">
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ tg.name }}</span>
                            </label>
                            <div v-if="dbTransportGroups.length === 0" class="text-xs text-gray-400 italic p-2">Ro'yxat bo'sh... API yuklanmoqda</div>
                        </div>
                    </div>

                    <div v-if="['grid', 'list', 'stat', 'gauge', 'chart', 'area_chart'].includes(getSafeCategory(editingWidget.type))">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2 mt-4">Geozonalarni tanlang</label>
                        <div class="bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-gray-700 rounded-lg p-2 max-h-32 overflow-y-auto custom-scrollbar flex flex-col gap-1">
                            <label v-for="geo in dbGeozones" :key="geo.id" 
                                   class="flex items-center gap-3 p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-800 cursor-pointer transition-colors">
                                <input type="checkbox" :value="geo.id" v-model="editingWidget.data.geozones"
                                       class="w-4 h-4 text-purple-500 rounded border-gray-300 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600">
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ geo.name }}</span>
                            </label>
                            <div v-if="dbGeozones.length === 0" class="text-xs text-gray-400 italic p-2">Ro'yxat bo'sh... API yuklanmoqda</div>
                        </div>
                    </div>

                    <div v-if="['stat', 'gauge', 'chart', 'grid', 'list', 'system', 'area_chart'].includes(getSafeCategory(editingWidget.type))">
                        <label
                            class="block text-xs font-bold text-gray-500 uppercase mb-1 flex justify-between items-center">
                            Yoki maxsus API (URL)
                            <span
                                class="text-[9px] bg-blue-100 text-blue-600 px-2 py-0.5 rounded uppercase">Ixtiyoriy</span>
                        </label>
                        <input v-if="editingWidget.data" v-model="editingWidget.data.apiUrl" type="text"
                            placeholder="/api/stats/speed"
                            class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-gray-700 rounded-lg p-2.5 text-sm text-gray-800 dark:text-white focus:border-cyan-500 outline-none transition-colors font-mono placeholder:font-sans">
                        <p class="text-[10px] text-gray-400 mt-1 leading-tight">Agar to'ldirilsa, Tepadagi tanlov inobatga olinmaydi, o'zining shaxsiy urlsidan oladi.</p>
                    </div>

                    <div
                        v-if="['grid', 'gauge', 'area_chart', 'chart', 'stat', 'system'].includes(getSafeCategory(editingWidget.type))">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Asosiy Rang</label>
                        <div class="grid grid-cols-4 gap-2">
                            <button v-for="color in availableColors" :key="color.value"
                                @click="editingWidget.data.color = color.value"
                                class="h-8 w-full rounded-lg border-2 transition-all flex items-center justify-center hover:scale-105 shadow-sm"
                                :style="{ backgroundColor: color.value, borderColor: editingWidget.data.color === color.value ? (isLightColor(color.value) ? '#000' : '#fff') : 'transparent' }">
                                <span v-if="editingWidget.data.color === color.value" class="text-xs"
                                    :class="isLightColor(color.value) ? 'text-black' : 'text-white'">✓</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <button @click="showSettingsModal = false"
                        class="px-4 py-2 rounded-lg text-xs font-bold bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-white transition-colors">Bekor</button>
                    <button @click="saveSettings"
                        class="px-4 py-2 rounded-lg text-xs font-bold bg-blue-500 hover:bg-blue-400 text-white shadow-lg transition-colors">Saqlash</button>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { GridLayout, GridItem } from 'grid-layout-plus';
import axios from 'axios';

// --- STATE O'ZGARUVCHILAR ---
const isEditMode = ref(false);
const showWidgetModal = ref(false);
const showSettingsModal = ref(false);
const fullscreenItem = ref(null);
const editingWidget = ref(null);
const layout = ref([]);
const currentTime = ref(new Date().toLocaleTimeString());

const realData = ref({ GLOBAL: {} });
const widgetLoadingStates = ref({});
const loadingLayout = ref(false);

const usersList = ref([]);
const currentUser = ref({ id: 1, name: 'Admin' });
const selectedUserId = ref(currentUser.value.id);

// YANGI O'ZGARUVCHILAR: Bazadan kelgan guruhlar va geozonalar
const dbTransportGroups = ref([]);
const dbGeozones = ref([]);

let timer;
let dataInterval;

// API dan ro'yxatlarni yuklab olish funksiyasi
const fetchDropdownData = async () => {
    try {
        const resTg = await axios.get('/api/crud/transport_groups');
        const resGeo = await axios.get('/api/crud/geozone_groups');
        
        dbTransportGroups.value = resTg.data.data ? resTg.data.data : resTg.data;
        dbGeozones.value = resGeo.data.data ? resGeo.data.data : resGeo.data;
    } catch (e) {
        console.error("Guruhlarni yuklashda xatolik:", e);
    }
};

// --- HAYOT SIKLI (LIFECYCLE) ---
onMounted(async () => {
    timer = setInterval(() => currentTime.value = new Date().toLocaleTimeString(), 1000);
    window.addEventListener('keydown', (e) => { if (e.key === 'Escape') fullscreenItem.value = null; });

    await fetchUsers();
    await fetchDropdownData(); // YANGI: Guruhlarni yuklash
    await loadLayout(selectedUserId.value);

    fetchAllWidgetsData();
    dataInterval = setInterval(fetchAllWidgetsData, 10000);
});

onUnmounted(() => {
    clearInterval(timer);
    clearInterval(dataInterval);
});

// --- XAVFSIZ FUNKSIYALAR (FAIL-SAFE) ---
const getWidgetDef = (type) => {
    return widgetDefinitions[type] || { category: 'other', title: 'Noma\'lum Vidjet', color: '#888', icon: '❓', max: 100, unit: '' };
};

const getSafeCategory = (type) => {
    return getWidgetDef(type).category;
};

const isWidgetLoading = (id) => widgetLoadingStates.value[id] || false;


// --- API ORQALI MA'LUMOT OLISH ---
const fetchAllWidgetsData = async () => {
    // 1. UMUMIY (GLOBAL) API-dan tortish
    try {
        const res = await axios.get('/api/dashboard/data'); // Backend URL
        if (!realData.value['GLOBAL']) realData.value['GLOBAL'] = {};

        Object.keys(res.data).forEach(key => {
            realData.value['GLOBAL'][key] = res.data[key];
        });
    } catch (e) { console.error("Umumiy API da xato:", e); }

    // 2. SHAXSIY (CUSTOM) API-larni tortish
    if (layout.value && layout.value.length > 0) {
        layout.value.forEach(async (item) => {
            if (item.data && item.data.apiUrl && item.data.apiUrl.trim() !== '') {
                widgetLoadingStates.value[item.i] = true;
                try {
                    const response = await axios.get(item.data.apiUrl);
                    const val = (response.data !== null && typeof response.data === 'object' && response.data.value !== undefined)
                        ? response.data.value
                        : response.data;
                    realData.value[item.i] = val;
                } catch (error) {
                    console.error(`Vidjet API xatosi (${item.data.apiUrl}):`, error);
                    realData.value[item.i] = null;
                } finally {
                    widgetLoadingStates.value[item.i] = false;
                }
            }
        });
    }
};

// --- SILLIQ GRAFIK (SPLINE) UCHUN YONLAMACHA CHIZIQ YASASH ---
const generateSplinePath = (dataArray) => {
    let arr = (Array.isArray(dataArray) && dataArray.length > 0) ? dataArray : [10, 30, 15, 45, 20, 60, 40];
    const max = Math.max(...arr.map(v => Number(v) || 0), 100);
    const w = 300;
    const h = 100;
    const step = w / (arr.length - 1 || 1);

    let path = `M 0,${h} L 0,${h - (arr[0] / max) * h} `;

    for (let i = 0; i < arr.length - 1; i++) {
        const x1 = i * step;
        const y1 = h - (arr[i] / max) * h;
        const x2 = (i + 1) * step;
        const y2 = h - (arr[i + 1] / max) * h;

        const cx1 = x1 + step / 2;
        const cy1 = y1;
        const cx2 = x1 + step / 2;
        const cy2 = y2;

        path += `C ${cx1},${cy1} ${cx2},${cy2} ${x2},${y2} `;
    }
    path += `L ${w},${h} Z`;
    return path;
};

// --- API dan kelgan barcha kalitlar (Dropdown uchun) ---
const availableDataKeys = computed(() => {
    return Object.keys(realData.value['GLOBAL'] || {});
});

// --- GRAFIKLAR UCHUN ENG KATTA QIYMATNI TOPISH (Scale uchun) ---
const getMaxValue = (arr) => {
    const dataArray = (Array.isArray(arr) && arr.length > 0) ? arr : [50, 30, 80, 20, 40, 90, 30];
    const max = Math.max(...dataArray.map(v => Number(v) || 0));
    return max > 0 ? max : 100;
};

// --- QIYMATNI TOPISH LOGIKASI ---
const getWidgetValue = (item) => {
    const id = item.i;
    const type = item.type;
    const customDataKey = item.data?.dataKey;

    // 1. Maxsus API dan (Agar apiUrl yozilgan bo'lsa)
    if (realData.value[id] !== undefined && realData.value[id] !== null) {
        return realData.value[id];
    }

    // 2. Umumiy API dan
    if (realData.value['GLOBAL']) {
        if (customDataKey && realData.value['GLOBAL'][customDataKey] !== undefined) {
            return realData.value['GLOBAL'][customDataKey];
        }
        if (realData.value['GLOBAL'][type] !== undefined) {
            return realData.value['GLOBAL'][type];
        }
    }

    // 3. Fallback (Massiv yoki Son/Matn)
    const isArrayType = ['GreenGrid', 'OrangeGrid', 'BlueGrid', 'LastEvents', 'TopDrivers', 'MaintenanceList', 'GeozoneList', 'WeeklyChart', 'FuelChart', 'DistanceChart', 'SplineChart'].includes(type);
    return isArrayType ? [] : 0;
};


// --- FOYDALANUVCHILAR VA LAYOUT BOSHQARUVI ---
const fetchUsers = async () => {
    try {
        const res = await axios.get('/api/users-list');
        usersList.value = res.data;
    } catch (e) { console.error("Userlar ro'yxati yuklanmadi"); }
};

const changeUser = () => loadLayout(selectedUserId.value);

const loadLayout = async (userId) => {
    loadingLayout.value = true;
    layout.value = [];
    try {
        const res = await axios.get('/api/dashboard/config', { params: { user_id: userId } });
        if (res.data && Array.isArray(res.data) && res.data.length > 0) {
            layout.value = res.data;
        } else if (userId === currentUser.value.id) {
            addItem('TotalMachines', 3, 3);
            addItem('GreenGrid', 6, 6);
        }
    } catch (e) {
        console.error("Layout API xatosi, LocalStorage dan o'qilmoqda...");
        const saved = localStorage.getItem('dashboard_fallback');
        if (saved) layout.value = JSON.parse(saved);
    } finally {
        loadingLayout.value = false;
        fetchAllWidgetsData();
    }
};

const saveLayoutToDb = async () => {
    try {
        await axios.post('/api/dashboard/config', {
            layout: layout.value,
            user_id: selectedUserId.value
        });
        alert("Dashboard saqlandi!");
        localStorage.setItem('dashboard_fallback', JSON.stringify(layout.value));
        isEditMode.value = false;
        fetchAllWidgetsData();
    } catch (e) { alert("Saqlashda xatolik yuz berdi!"); }
};

// --- YORDAMCHI FUNKSIYALAR ---
const availableColors = [
    { value: '#ef4444', label: 'Qizil' }, { value: '#f97316', label: 'Apelsin' }, { value: '#eab308', label: 'Sariq' }, { value: '#22c55e', label: 'Yashil' },
    { value: '#06b6d4', label: 'Moviy' }, { value: '#3b82f6', label: 'Ko\'k' }, { value: '#a855f7', label: 'Binafsha' }, { value: '#ec4899', label: 'Pushti' }
];

const dataKeyTranslations = {
    'TotalMachines': 'Jami texnikalar',
    'ActiveDrivers': 'Faol texnikalar (Harakatda)',
    'FuelConsumption': 'Jami yoqilg\'i sarfi',
    'TotalDistance': 'Bosib o\'tilgan masofa',
    'AlertsCount': 'Ogohlantirishlar',
    'OnlineUsers': 'Tizimda onlaynlar',
    'WeeklyChart': 'Haftalik grafik',
    'FuelChart': 'Yoqilg\'i grafigi',
    'DistanceChart': 'Masofa grafigi',
    'SplineChart': 'To\'lqinli grafik (Signal)',
    'GreenGrid': 'Faollar ro\'yxati (Yashil)',
    'OrangeGrid': 'Yoqilg\'i ro\'yxati (Sariq)',
    'BlueGrid': 'To\'xtab turganlar (Ko\'k)',
    'SpeedGauge': 'Tezlik ko\'rsatkichi',
    'RpmGauge': 'Dvigatel aylanishi (RPM)',
    'ServerStatus': 'Server yuklanishi',
    'DbStatus': 'Baza xotirasi',
    'WialonStatus': 'API aloqa holati',
    'LastEvents': 'So\'nggi hodisalar',
    'TopDrivers': 'Haydovchilar reytingi',
    'MaintenanceList': 'Ta\'mirlash ro\'yxati',
    'GeozoneList': 'Barcha mashinalar (Qayerdaligi bilan)'
};

const translateKey = (key) => {
    if (dataKeyTranslations[key]) return dataKeyTranslations[key];
    if (key.startsWith('Zone_')) {
        return '📍 Hudud: ' + key.replace('Zone_', '').replace(/_/g, ' ');
    }
    return key;
};

const isLightColor = (hex) => {
    if (!hex) return false;
    const c = hex.substring(1);
    const rgb = parseInt(c, 16);
    return (((rgb >> 16) & 0xff) * 0.299 + ((rgb >> 8) & 0xff) * 0.587 + ((rgb >> 0) & 0xff) * 0.114) > 186;
};

const toggleEditMode = () => { if (isEditMode.value) saveLayoutToDb(); else isEditMode.value = true; };
const clearLayout = () => { if (confirm("Barcha vidjetlarni o'chirib tashlamoqchimisiz?")) layout.value = []; };
const removeItem = (id) => layout.value = layout.value.filter(item => item.i !== id);
const toggleFullscreen = (item) => fullscreenItem.value = item;

const openSettings = (item) => {
    editingWidget.value = JSON.parse(JSON.stringify(item));
    if (editingWidget.value.data.apiUrl === undefined) editingWidget.value.data.apiUrl = '';
    if (editingWidget.value.data.dataKey === undefined) editingWidget.value.data.dataKey = '';
    
    // YANGI: Agar arraylar bo'lmasa yaratib olamiz
    if (!editingWidget.value.data.transport_groups) editingWidget.value.data.transport_groups = [];
    if (!editingWidget.value.data.geozones) editingWidget.value.data.geozones = [];
    
    showSettingsModal.value = true;
};

const saveSettings = () => {
    const index = layout.value.findIndex(i => i.i === editingWidget.value.i);
    if (index !== -1) layout.value[index].data = { ...layout.value[index].data, ...editingWidget.value.data };
    showSettingsModal.value = false;
    fetchAllWidgetsData();
};

const addItem = (type, w, h) => {
    const def = getWidgetDef(type);
    let maxY = 0;
    layout.value.forEach(item => { if (item.y + item.h > maxY) maxY = item.y + item.h; });
    const newItem = {
        x: 0, y: maxY, w: w, h: h, i: Date.now().toString() + Math.random(), type: type,
        data: {
            title: def.title || 'Yangi Vidjet',
            color: def.color || '#3b82f6',
            apiUrl: '',
            dataKey: '',
            text: '',
            transport_groups: [], // YANGI
            geozones: [] // YANGI
        }
    };
    layout.value.push(newItem);
    showWidgetModal.value = false;
};

// --- VIDJETLAR KONFIGURATSIYASI ---
const widgetDefinitions = {
    'SpeedGauge': { category: 'gauge', title: 'Tezlik', unit: 'km/h', max: 200, color: '#22c55e', icon: '🚀' },
    'RpmGauge': { category: 'gauge', title: 'Motor (RPM)', unit: 'x1000', max: 8, color: '#f97316', icon: '⚙️' },
    'SplineChart': { category: 'area_chart', title: 'Signal Sifati', color: '#22c55e', icon: '📶' },
    'GreenGrid': { category: 'grid', title: 'Ish Jarayonida', color: '#22c55e' },
    'OrangeGrid': { category: 'grid', title: 'Yoqilg\'i Qabulida', color: '#f97316' },
    'BlueGrid': { category: 'grid', title: 'Smena / Parkovka', color: '#3b82f6' },
    'TotalMachines': { category: 'stat', title: 'Jami Texnikalar', subtext: 'Parkdagi', icon: '🚜', color: '#06b6d4' },
    'ActiveDrivers': { category: 'stat', title: 'Faol Texnikalar', subtext: 'Harakatda', icon: '👨‍✈️', color: '#22c55e' },
    'FuelConsumption': { category: 'stat', title: 'Yoqilg\'i Qoldig\'i', subtext: 'Jami Litr', icon: '⛽', color: '#f97316' },
    'TotalDistance': { category: 'stat', title: 'Bosib o\'tilgan yo\'l', subtext: 'Jami km', icon: '🛣️', color: '#a855f7' },
    'AlertsCount': { category: 'stat', title: 'Ogohlantirishlar', subtext: 'So\'nggi 24s', icon: '🔔', color: '#ef4444' },
    'OnlineUsers': { category: 'stat', title: 'Online Tizimda', subtext: 'Foydalanuvchilar', icon: '👥', color: '#3b82f6' },
    'WeeklyChart': { category: 'chart', title: 'Haftalik Tahlil', color: '#06b6d4' },
    'FuelChart': { category: 'chart', title: 'Yoqilg\'i Sarfi', color: '#f97316' },
    'DistanceChart': { category: 'chart', title: 'Masofa Tahlili', color: '#a855f7' },
    'ServerStatus': { category: 'system', title: 'Server CPU', color: '#3b82f6' },
    'DbStatus': { category: 'system', title: 'Baza Xotirasi', color: '#eab308' },
    'WialonStatus': { category: 'system', title: 'API Ulanish', color: '#22c55e' },
    'LastEvents': { category: 'list', title: 'Oxirgi Hodisalar' },
    'TopDrivers': { category: 'list', title: 'Reyting' },
    'MaintenanceList': { category: 'list', title: 'Remont va Xizmat' },
    'GeozoneWidget': { category: 'list', title: 'Geozonalar holati' },
    'WeatherWidget': { category: 'other' },
    'ClockWidget': { category: 'other' },
    'NoteWidget': { category: 'other' },
};

const widgetGroups = {
    '📈 Spidometrlar': [{ type: 'SpeedGauge', title: 'Spidometr', icon: '🚀', desc: 'Tezlikni ko\'rsatish', defaultW: 4, defaultH: 5 }, { type: 'RpmGauge', title: 'Tahometr (RPM)', icon: '⚙️', desc: 'Motor aylanishi', defaultW: 4, defaultH: 5 }, { type: 'SplineChart', title: 'Silliq Grafik', icon: '📶', desc: 'Dinamik o\'zgarishlar', defaultW: 6, defaultH: 5 }],
    '🏗️ Gridlar (Kataklar)': [{ type: 'GreenGrid', title: 'Yashil Grid', icon: '🟢', desc: 'Mashinalar ro\'yxati', defaultW: 6, defaultH: 6 }, { type: 'OrangeGrid', title: 'Sariq Grid', icon: '🟠', desc: 'Mashinalar ro\'yxati', defaultW: 6, defaultH: 6 }, { type: 'BlueGrid', title: 'Ko\'k Grid', icon: '🔵', desc: 'Mashinalar ro\'yxati', defaultW: 6, defaultH: 6 }],
    '📊 Statistika Kartalari': [{ type: 'TotalMachines', title: 'Karta 1', icon: '🚜', desc: 'Katta raqamli blok', defaultW: 3, defaultH: 3 }, { type: 'ActiveDrivers', title: 'Karta 2', icon: '👨‍✈️', desc: 'Katta raqamli blok', defaultW: 3, defaultH: 3 }, { type: 'FuelConsumption', title: 'Karta 3', icon: '⛽', desc: 'Katta raqamli blok', defaultW: 3, defaultH: 3 }, { type: 'TotalDistance', title: 'Karta 4', icon: '🛣️', desc: 'Katta raqamli blok', defaultW: 3, defaultH: 3 }, { type: 'AlertsCount', title: 'Karta 5', icon: '🔔', desc: 'Katta raqamli blok', defaultW: 3, defaultH: 3 }, { type: 'OnlineUsers', title: 'Karta 6', icon: '👥', desc: 'Katta raqamli blok', defaultW: 3, defaultH: 3 }],
    '📈 Grafiklar': [{ type: 'WeeklyChart', title: 'Ustunli Grafik 1', icon: '📊', desc: 'Massiv qiymatlar', defaultW: 6, defaultH: 6 }, { type: 'FuelChart', title: 'Ustunli Grafik 2', icon: '📉', desc: 'Massiv qiymatlar', defaultW: 6, defaultH: 6 }, { type: 'DistanceChart', title: 'Ustunli Grafik 3', icon: '📏', desc: 'Massiv qiymatlar', defaultW: 6, defaultH: 6 }],
    '📋 Ro\'yxatlar': [{ type: 'LastEvents', title: 'Ro\'yxat 1', icon: '⚡', desc: 'Name va Status', defaultW: 4, defaultH: 6 }, { type: 'TopDrivers', title: 'Ro\'yxat 2', icon: '🏆', desc: 'Name va Status', defaultW: 4, defaultH: 6 }, { type: 'MaintenanceList', title: 'Ro\'yxat 3', icon: '🛠️', desc: 'Name va Status', defaultW: 4, defaultH: 6 }, { type: 'GeozoneWidget', title: 'Geozonalar paneli', icon: '📍', desc: 'Hududlardagi mashinalar', defaultW: 6, defaultH: 6 }],
    '⚙️ Tizim': [{ type: 'ServerStatus', title: 'Foiz chizig\'i 1', icon: '🖥️', desc: '0-100 gacha qiymat', defaultW: 3, defaultH: 3 }, { type: 'DbStatus', title: 'Foiz chizig\'i 2', icon: '🗄️', desc: '0-100 gacha qiymat', defaultW: 3, defaultH: 3 }, { type: 'WialonStatus', title: 'Foiz chizig\'i 3', icon: '📡', desc: '0-100 gacha qiymat', defaultW: 3, defaultH: 3 }],
    '🧩 Boshqalar': [{ type: 'WeatherWidget', title: 'Ob-havo', icon: '🌤️', desc: 'Statik', defaultW: 3, defaultH: 4 }, { type: 'ClockWidget', title: 'Soat', icon: '⏰', desc: 'Jonli vaqt', defaultW: 3, defaultH: 2 }, { type: 'NoteWidget', title: 'Eslatma', icon: '📝', desc: 'Matn kiritish uchun', defaultW: 4, defaultH: 4 }]
};
</script>

<style scoped>
.scale-in-center {
    animation: scale-in 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94) both;
}

@keyframes scale-in {
    0% {
        transform: scale(0.95);
        opacity: 0;
    }

    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #374151;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #4b5563;
}

.vue-grid-item.vue-grid-placeholder {
    background: rgba(6, 182, 212, 0.3) !important;
    border-radius: 16px !important;
    opacity: 0.5;
}
</style>