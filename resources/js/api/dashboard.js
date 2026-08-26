import axios from './client';

export function summary() {
    return axios.get('/api/dashboard/summary').then((res) => res.data.data);
}
