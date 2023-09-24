/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./node_modules/flowbite/**/*.js",
  ],
  theme: {
    colors: {    
      'primary': '#7480EB',
      'primary-600': '#545FCA',
      'bg-blue': '#E3E6F0',
      'link-blue': '#4E5E80',
      'hitam': '#403930',
      'hitam-400': '#594F43',
      'neutral': '#EEEEEE',
      
    },
    extend: {
      fontFamily: {
        poppins: ["Poppins", "sans-serif"],
      },
    },
  },
  plugins: [
    require('flowbite/plugin')
  ],
}

