import axios from './client';

export function list() {
    return axios.get('/api/locations').then((res) => res.data.data);
}
