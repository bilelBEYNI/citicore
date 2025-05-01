import axios from 'axios';

const API_URL = 'https://api.example.com/events';

class EventService {
    async fetchEvents(date) {
        try {
            const response = await axios.get(`${API_URL}?date=${date}`);
            return response.data;
        } catch (error) {
            console.error('Error fetching events:', error);
            throw error;
        }
    }

    async createEvent(eventData) {
        try {
            const response = await axios.post(API_URL, eventData);
            return response.data;
        } catch (error) {
            console.error('Error creating event:', error);
            throw error;
        }
    }

    async updateEvent(eventId, eventData) {
        try {
            const response = await axios.put(`${API_URL}/${eventId}`, eventData);
            return response.data;
        } catch (error) {
            console.error('Error updating event:', error);
            throw error;
        }
    }

    async deleteEvent(eventId) {
        try {
            await axios.delete(`${API_URL}/${eventId}`);
        } catch (error) {
            console.error('Error deleting event:', error);
            throw error;
        }
    }
}

export default new EventService();