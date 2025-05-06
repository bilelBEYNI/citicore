import React from 'react';

const EventModal = ({ event, onClose }) => {
    if (!event) return null;

    return (
        <div className="modal">
            <div className="modal-content">
                <span className="close" onClick={onClose}>&times;</span>
                <h2>{event.title}</h2>
                <p>{event.description}</p>
                <p><strong>Date:</strong> {new Date(event.date).toLocaleString()}</p>
                <p><strong>Location:</strong> {event.location}</p>
                <button onClick={() => alert('Edit event functionality not implemented yet.')}>Edit</button>
                <button onClick={() => alert('Delete event functionality not implemented yet.')}>Delete</button>
            </div>
        </div>
    );
};

export default EventModal;