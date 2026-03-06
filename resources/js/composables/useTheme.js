import { ref, onMounted } from 'vue';

const isDark = ref(true); // Boshlanishiga Dark bo'lsin

export function useTheme() {
  
  const toggleTheme = () => {
    isDark.value = !isDark.value;
    updateDOM();
  };

  const updateDOM = () => {
    const html = document.documentElement;
    if (isDark.value) {
      html.classList.add('dark');
      localStorage.setItem('theme', 'dark');
    } else {
      html.classList.remove('dark');
      localStorage.setItem('theme', 'light');
    }
  };

  onMounted(() => {
    // Brauzer xotirasini tekshirish
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
      isDark.value = savedTheme === 'dark';
    } else {
      // Yoki tizim sozlamasiga qarash
      isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
    updateDOM();
  });

  return { isDark, toggleTheme };
}