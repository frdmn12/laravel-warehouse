import axios from './client';

export function list(params) {
    return axios.get('/api/stock-products', { params }).then((res) => res.data);
}

export function create(payload) {
    return axios.post('/api/stock-products', payload).then((res) => res.data);
}
