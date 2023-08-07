-- PROJECTION QUERY
-- "Select the room number, floor, status, hotel name and hotel address for the available rooms"
SELECT roomNum, floor, status, hotelName, hotelAddress
    FROM Room
    WHERE status = 'Available';

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
    
    
-- ALISON QUERIES ------------------------------------------------------------------------------------------------------------------------------
 
-- Aggregation with Having
-- find employees who manage more than 3 cleaning staff
SELECT employeeID, COUNT(*) AS numStaff
FROM CleaningStaff_assignedBy
GROUP BY employeeID
HAVING COUNT(*) > 3;


-- Nested Aggregation with Group By
-- number of rooms on each floor at hotels above the average floor level (across all hotels)
SELECT R.floor, R.hotelName, R.hotelAddress, COUNT(*) as numHigh
FROM Room R
GROUP BY R.floor, R.hotelName, R.hotelAddress
HAVING R.floor > (SELECT AVG(R2.floor)
FROM Room R2);


-- Division
-- find customers who have visited all hotels
-- HotelCustomer / Hotel  -> join with customer to get customer ID, first and lase name
SELECT C.customerID, C.firstName, C.lastName
FROM Customer C
WHERE NOT EXISTS
((SELECT H.hotelName, H.hotelAddress
FROM HotelLocation H)
EXCEPT
(SELECT X.hotelName, X.hotelAddress
FROM HotelCustomer X
WHERE X.customerID = C.customerID));

-- ORACLE NEEDS MINUS INSTEAD OF EXCEPT
SELECT C.customerID, C.firstName, C.lastName
            FROM Customer C
            WHERE NOT EXISTS
            ((SELECT H.hotelName, H.hotelAddress
            FROM HotelLocation H)
            MINUS
            (SELECT X.hotelName, X.hotelAddress
            FROM HotelCustomer X
            WHERE X.customerID = C.customerID));
