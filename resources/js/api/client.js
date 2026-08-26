import axios from 'axios';

let csrfCookiePromise = null;

export function ensureCsrfCookie() {
    if (!csrfCookiePromise) {
        csrfCookiePromise = axios.get('/sanctum/csrf-cookie');
    }

    return csrfCookiePromise;
}

export default axios;
