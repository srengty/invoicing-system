import axios from 'axios';
window.axios = axios;
window.axios.defaults.baseURL = import.meta.env.APP_URL || 'https://finance.itc.edu.kh';
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
