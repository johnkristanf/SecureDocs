import { onMounted } from "vue";

export const ThemeSelector = () => {
    return onMounted(() => {
        if(localStorage.getItem('theme') === 'dark'){
          document.documentElement.classList.add('dark')
        }
    });
}