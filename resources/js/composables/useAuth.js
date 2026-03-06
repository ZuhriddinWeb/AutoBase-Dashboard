// src/composables/useAuth.js
import { ref } from 'vue';

export function useAuth() {
  // 1. LocalStorage dan userni o'qib olish
  // Agar user bo'lmasa, bo'sh obyekt qaytaradi xatolik chiqmasligi uchun
  let user = {};
  
  try {
    const storedUser = localStorage.getItem('user');
    if (storedUser) {
      user = JSON.parse(storedUser);
    }
  } catch (error) {
    console.error("User ma'lumotlarini o'qishda xatolik yuz berdi:", error);
  }
  
  // 2. Userning ruxsatlari (permissions) va roli (role) ni olish
  // Backenddan keladigan format taxminan shunday:
  // permissions: ['dashboard.view_working', 'dashboard.view_fuel']
  const permissions = user.permissions || []; 
  const role = user.role || '';

  // 3. Ruxsatni tekshirish funksiyasi (Asosiy mantiq)
  const can = (permissionName) => {
    // Agar foydalanuvchi 'admin' bo'lsa, unga har doim ruxsat beramiz
    if (role === 'admin') {
      return true;
    }
    
    // Aks holda, permissions massivini tekshiramiz
    return permissions.includes(permissionName);
  };

  // Funksiya va user obyektini komponentlarga qaytaramiz
  return { 
    can, 
    user 
  };
}