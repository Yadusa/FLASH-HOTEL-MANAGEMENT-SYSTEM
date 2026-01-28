# TODO List for Adding New Features to Hotel Booking Management System

## Database Changes
- [x] Create add_features.sql with ALTER TABLE for customer address and CREATE TABLE for room_blocked_dates

## Customer Management
- [x] Update customer_register_process.php to include address in registration
- [x] Update edit_customer.php to include address in editing
- [x] Modify delete_customer.php to delete associated bookings before deleting customer

## Room Management
- [x] Add form in manage_rooms.php to add new rooms with capacity
- [x] Add admin interface in manage_rooms.php to block dates for rooms

## Booking Management
- [x] Add form in bookings.php to manually create bookings
- [x] Update booking.php to check for blocked dates before allowing booking

## Testing
- [x] Test all new features
- [x] Ensure no existing functionality is broken
