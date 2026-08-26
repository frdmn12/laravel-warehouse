import axios from './client';

export function list(params) {
    return axios.get('/api/products', { params }).then((res) => res.data);
}

export function get(id) {
    return axios.get(`/api/products/${id}`).then((res) => res.data.data);
}

export function create(payload) {
    return axios.post('/api/products', payload).then((res) => res.data.data);
}

export function update(id, payload) {
    return axios.put(`/api/products/${id}`, payload).then((res) => res.data.data);
}

export function destroy(id) {
    return axios.delete(`/api/products/${id}`).then((res) => res.data);
}
