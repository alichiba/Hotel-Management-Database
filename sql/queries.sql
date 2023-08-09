-- INSERT QUERY
-- insert new reservation (default customer, hotel, hotel address)
insert into Reservation values (:bind1, TO_DATE(:bind2,'YYYY-MM-DD'), TO_DATE(:bind3,'YYYY-MM-DD'), :bind4, :bind5, :bind6, :bind7
                                
-- UPDATE QUERY
-- update reservation dates
UPDATE Reservation SET startDate=TO_DATE('" . $new_start . "', 'YYYY-MM-DD') WHERE startDate=TO_DATE('" . $old_start . "', 'YYYY-MM-DD') AND reservationID='" . $res_id . "'
UPDATE Reservation SET endDate=TO_DATE('" . $new_end . "', 'YYYY-MM-DD') WHERE endDate=TO_DATE('" . $old_end . "', 'YYYY-MM-DD') AND reservationID='" . $res_id . "'

-- DELETE QUERY
-- delete reservation
DELETE FROM Reservation WHERE reservationID = '" . $res_id . "'                                
                              
-- SELECTION QUERY 
-- select reservations at hotels after specified date
SELECT * FROM Reservation WHERE hotelName= '" . $status . "' AND startDate > TO_DATE('" . $start_date . "', 'YYYY-MM-DD')

-----------------------------------------------------------------------------------------------------------------------------------------------------------------                               

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
