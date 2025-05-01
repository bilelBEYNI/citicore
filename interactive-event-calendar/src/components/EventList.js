import React from 'react';

const EventList = ({ events }) => {
    return (
        <div className="event-list">
            <h2>Scheduled Events</h2>
            {events.length === 0 ? (
                <p>No events scheduled for this date.</p>
            ) : (
                <ul>
                    {events.map((event) => (
                        <li key={event.id}>
                            <h3>{event.title}</h3>
                            <p>{event.description}</p>
                            <p>{new Date(event.date).toLocaleString()}</p>
                        </li>
                    ))}
                </ul>
            )}
        </div>
    );
};

export default EventList;