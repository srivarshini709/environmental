document.addEventListener('DOMContentLoaded', function() {
    const reminders = document.querySelectorAll('.reminder');
    
    reminders.forEach(reminder => {
        const reminderTime = new Date(reminder.getAttribute('data-time'));
        const now = new Date();
        
        if (reminderTime <= now) {
            alert(`Reminder: ${reminder.getAttribute('data-medication')}`);
            reminder.classList.add('reminder-past-due');
        }
    });
});
