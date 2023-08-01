-- PROJECTION QUERY
-- "Select the room number, floor, status, hotel name and hotel address for the available rooms"
SELECT roomNum, floor, status, hotelName, hotelAddress
    FROM Room
    WHERE status = 'AVAILABLE';

