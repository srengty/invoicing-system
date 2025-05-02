import axios from 'axios';

export async function recordPayment(scheduleId, amount, receiptNo, receiptDate) {
    try {
        const response = await axios.post('/record-payment', {
            schedule_id: scheduleId,
            amount: amount,
            receipt_no: receiptNo,
            receipt_date: receiptDate
        });

        // Update UI with the response data
        console.log('Payment recorded:', response.data);
        return response.data;
    } catch (error) {
        console.error('Error recording payment:', error);
        throw error;
    }
}

// You can add other payment-related functions here
