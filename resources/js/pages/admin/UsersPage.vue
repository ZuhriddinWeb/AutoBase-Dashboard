<template>
  <div>
    <BaseCrud :config="userConfig" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import BaseCrud from '../../components/BaseCrud.vue';

// Config endi REACTIVE (ref ichida), chunki biz uning ichini o'zgartiramiz
const userConfig = ref({
  title: 'Xodimlar Boshqaruvi',
  slug: 'users', // API: /api/crud/users
  fields: [
    { key: 'id', label: 'ID', type: 'text', readonly: true },
    { key: 'first_name', label: 'Ism', type: 'text', placeholder: 'Ismni kiriting' },
    { key: 'last_name', label: 'Familiya', type: 'text', placeholder: 'Familiyani kiriting' },
    { key: 'login', label: 'Login', type: 'text', placeholder: 'Login' },
    { key: 'password', label: 'Parol', type: 'password', placeholder: 'Yangi parol (bo\'sh qoldirish mumkin)' },
    
    // ROLE qismi (Options boshida bo'sh turadi, API dan kelgach to'ladi)
    { 
      key: 'role', // Backendga "role" nomi bilan boradi
      label: 'Lavozimi (Rol)', 
      type: 'select', 
      options: [] // <--- BU YER HOZIRCHA BO'SH
    },
    
    { key: 'status', label: 'Holati', type: 'select', options: [
      { value: 'active', label: 'Faol' },
      { value: 'inactive', label: 'Nofaol' }
    ]}
  ]
});

// Rollarni bazadan yuklab olish funksiyasi
const fetchRoles = async () => {
  try {
    const response = await axios.get('/api/roles');
    
    // Backenddan kelgan ma'lumotni to'g'irlash (data yoki data.data)
    const rolesData = Array.isArray(response.data) ? response.data : (response.data.data || []);

    // Rollarni Select uchun mos formatga o'tkazamiz { value: 'admin', label: 'Admin' }
    const roleOptions = rolesData.map(role => ({
      value: role.name, // Backendga rolning NOMI ketadi (masalan: "Super Admin")
      label: role.name  // Foydalanuvchiga ko'rinadigan nomi
    }));

    // Config ichidagi 'role' maydonini topib, options ni yangilaymiz
    const roleField = userConfig.value.fields.find(f => f.key === 'role');
    if (roleField) {
      roleField.options = roleOptions;
    }

  } catch (error) {
    console.error("Rollarni yuklashda xatolik:", error);
  }
};

// Sahifa yuklanganda ishga tushadi
onMounted(() => {
  fetchRoles();
});
</script>