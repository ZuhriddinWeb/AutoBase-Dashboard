import { createApp } from 'vue'
import App from './App.vue'
import router from './router/routes'
import '../css/app.css'
import VueApexCharts from "vue3-apexcharts";

// 1. Dastur obyektini yaratib, o'zgaruvchiga olamiz
const app = createApp(App)

// 2. Plaginlarni ulaymiz
app.use(router)
app.use(VueApexCharts)

// 3. Global funksiyani qo'shamiz (MOUNT QILISHDAN OLDIN!)
app.config.globalProperties.$can = (permissionName) => {
  try {
    const roles = localStorage.getItem('roles');
    const permissions = localStorage.getItem('permissions');

    const userRoles = roles ? JSON.parse(roles) : [];
    const userPermissions = permissions ? JSON.parse(permissions) : [];

    // Agar "Super Admin" bo'lsa - hamma narsaga ruxsat
    if (userRoles.includes('Super Admin')) {
        return true;
    }

    // Aks holda ruxsatni tekshiramiz
    return userPermissions.includes(permissionName);
  } catch (error) {
    console.error("Ruxsatni tekshirishda xato:", error);
    return false;
  }
};

// 4. Eng oxirida dasturni ishga tushiramiz (SHU QATOR ENG PASTDA TURISHI SHART)
app.mount('#app')