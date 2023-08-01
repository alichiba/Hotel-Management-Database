-- PROJECTION QUERY
-- "Select the room number, floor, status, hotel name and hotel address for the available rooms"
SELECT roomNum, floor, status, hotelName, hotelAddress
    FROM Room
    WHERE status = 'AVAILABLE';

-- JOIN QUERY
-- "Select the room number, floor, status, staff ID, full name of the staff associated to the room based on the input
--  provided for the status"
SELECT  R.roomNum, R.floor, R.status, CS.staffID, CS.firstName, CS.lastName
    FROM  Room R, CleaningStaff_assignedBy CS
    WHERE R.staffID = CS.staffID
        AND R.status = :status;

-- Aggregation with GROUP BY
-- "Find the number of customers for each hotel"
SELECT R.hotelName, COUNT(*) AS numOfCustomers
    FROM Reservation R
    GROUP BY R.hotelName
    ORDER BY numOfCustomers ASC;