import axios, { ensureCsrfCookie } from './client';

export async function fetchUser() {
    const { data } = await axios.get('/api/user');
    return data.data;
}

export async function login(credentials) {
    await ensureCsrfCookie();
    const { data } = await axios.post('/api/login', credentials);
    return data.data;
}

export async function register(payload) {
    await ensureCsrfCookie();
    const { data } = await axios.post('/api/register', payload);
    return data.data;
}

export async function logout() {
    await axios.post('/api/logout');
}
