# Interactive Attendance Calendar

## Overview
The Interactive Attendance Calendar is a comprehensive feature that displays employee attendance, shift changes, and absence requests in an interactive calendar format. It provides real-time data visualization with filtering capabilities and detailed event information.

## Features

### 🗓️ Calendar Views
- **Month View**: Overview of the entire month
- **Week View**: Detailed weekly schedule
- **List View**: Chronological list of events

### 📊 Statistics Dashboard
- Total Employees count
- Present Today count
- Absent Today count
- Shift Changes count
- Pending Requests count

### 🔍 Filtering Options
- **Employee Filter**: Filter by specific employee
- **Branch Filter**: Filter by company branch
- **Position Filter**: Filter by job position
- **Combined Filtering**: Apply multiple filters simultaneously

### 🎯 Event Types & Colors
- **🟢 Present (Green)**: Employee attended work
- **🔵 Shift Change (Blue)**: Approved shift change request
- **🔴 Absent (Red)**: Approved absence request
- **🟠 Pending (Orange)**: Requests awaiting approval

### ⌨️ Keyboard Shortcuts
- `Ctrl + ←` / `Ctrl + →`: Navigate between periods
- `Ctrl + Home`: Go to today
- `Ctrl + 1`: Switch to Month view
- `Ctrl + 2`: Switch to Week view
- `Ctrl + 3`: Switch to List view

### 🚀 Interactive Features
- **Event Click**: Click on any event to view detailed information
- **Hover Tooltips**: Hover over events to see quick information
- **Loading States**: Visual feedback during data loading
- **Error Handling**: Graceful error handling with user notifications
- **Responsive Design**: Mobile-friendly interface

### 📤 Export & Print
- **Export Calendar**: Download calendar data as JSON
- **Print View**: Print-friendly calendar layout

## Usage

### Accessing the Calendar
1. Navigate to Admin Dashboard
2. Click on "Calendar" in the navigation menu
3. The calendar will load with current month's data

### Filtering Data
1. Select desired filters from the dropdown menus
2. Click "Filter" to apply filters
3. Click "Clear" to reset all filters
4. Click "🔄" to refresh the calendar

### Viewing Event Details
1. Click on any calendar event
2. A modal will open showing detailed information
3. Information includes employee name, date, shift, and reason (if applicable)

### Navigating the Calendar
1. Use the navigation arrows to move between months/weeks
2. Click "Today" to return to current date
3. Use view buttons to switch between calendar formats

## Technical Implementation

### Backend
- **Controller**: `AdminController@calendar` and `AdminController@calendarEvents`
- **Models**: `Attendance`, `RequestAbsent`, `RequestShift`, `Employee`
- **Routes**: `/admin/calendar` and `/admin/calendar/events`

### Frontend
- **FullCalendar.js**: Main calendar library
- **DaisyUI**: UI components and styling
- **Vanilla JavaScript**: Event handling and interactions

### Data Flow
1. Calendar requests events for visible date range
2. Backend queries database with applied filters
3. Events are formatted and returned as JSON
4. Frontend renders events on calendar
5. Statistics are updated based on current view

## Configuration

### Calendar Options
- **Business Hours**: Monday-Friday, 8:00 AM - 5:00 PM
- **Time Range**: 6:00 AM - 10:00 PM
- **First Day**: Monday (ISO standard)
- **Week Numbers**: Enabled
- **Event Display**: Block format

### Styling
- Custom CSS for event styling
- Responsive design for mobile devices
- Hover effects and transitions
- Color-coded event types

## Troubleshooting

### Common Issues
1. **Calendar not loading**: Check browser console for JavaScript errors
2. **Events not showing**: Verify database has attendance data
3. **Filters not working**: Check if employee/branch/position data exists
4. **Slow performance**: Consider limiting date range or adding database indexes

### Browser Compatibility
- Modern browsers (Chrome, Firefox, Safari, Edge)
- JavaScript enabled
- CSS Grid support recommended

## Future Enhancements
- Drag and drop event editing
- Bulk event operations
- Calendar sharing and collaboration
- Advanced reporting and analytics
- Integration with external calendar systems
- Real-time updates via WebSockets

## Support
For technical support or feature requests, please contact the development team or create an issue in the project repository.
