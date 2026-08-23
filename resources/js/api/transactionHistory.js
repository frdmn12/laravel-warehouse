import axios from './client';

export function list(params) {
    return axios.get('/api/transaction-history', { params }).then((res) => res.data);
}

export function generateBatch(transactionType, date) {
    return axios
        .post('/api/transaction-history/generate-batch', { transaction_type: transactionType, date })
        .then((res) => res.data.batch);
}
