export function formatDate(date) {
    return date.toISOString().split('T')[0];
}

export function isSameDay(date1, date2) {
    return date1.getFullYear() === date2.getFullYear() &&
           date1.getMonth() === date2.getMonth() &&
           date1.getDate() === date2.getDate();
}

export function getStartOfWeek(date) {
    const start = new Date(date);
    const day = start.getDay();
    const diff = start.getDate() - day + (day === 0 ? -6 : 1); // adjust when day is sunday
    start.setDate(diff);
    return start;
}

export function getEndOfWeek(date) {
    const end = new Date(date);
    const day = end.getDay();
    const diff = end.getDate() + (6 - day); // adjust when day is sunday
    end.setDate(diff);
    return end;
}

export function isCurrentMonth(date) {
    const now = new Date();
    return date.getFullYear() === now.getFullYear() && date.getMonth() === now.getMonth();
}