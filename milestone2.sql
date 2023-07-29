﻿CREATE TABLE ManagementEmployee_Manages (
	employeeID 		INT,
	firstName		CHAR(20),
	lastName		CHAR(20),
	hotelName		CHAR(20),
	hotelAddress	CHAR(30),
	PRIMARY KEY (employeeID),
	FOREIGN KEY (hotelName,hotelAddress) REFERENCES HotelLocation(hotelName, hotelAddress));

CREATE TABLE Views (
	employeeID     	INT,
	reservationID	INT,
	PRIMARY KEY (reservationID, employeeID), 
	FOREIGN KEY (reservationID) REFERENCES Reservation,
	FOREIGN KEY (employeeID) REFERENCES ManagementEmployee_Manages(employeeID));

CREATE TABLE Reservation (
	reservationID	INT,
	startDate 		DATE,
	endDate			DATE,
	customerID		INT NOT NULL,
	hotelName		CHAR(20) NOT NULL,
	hotelAddress	CHAR(30) NOT NULL,
	typeName		CHAR(20) NOT NULL,
	PRIMARY KEY (reservationID),
	FOREIGN KEY (customerID) REFERENCES Customer,
	FOREIGN KEY (hotelName, hotelAddress) REFERENCES HotelLocation(hotelName, hotelAddress),
	FOREIGN KEY (typeName) REFERENCES RoomType(typeName));

CREATE TABLE Billing_Has (
	reservationID	INT,
	creditCard		CHAR(16),
	billingName		CHAR(20),
	billingAddress	CHAR(30),
	PRIMARY KEY (creditCard, reservationID),
	FOREIGN KEY (reservationID) REFERENCES Reservation(reservationID) 
	ON DELETE CASCADE
	ON UPDATE CASCADE);


CREATE TABLE HotelLocation (
	hotelName		CHAR(20),
	hotelAddress   	CHAR(30),
	city			CHAR(20),
	PRIMARY KEY (hotelName, hotelAddress)
	FOREIGN KEY (city) REFERENCES CitySeason(city));

CREATE TABLE CitySeason (
	city		CHAR(20),
	peakSeason	CHAR(20),
	PRIMARY KEY (city));

CREATE TABLE Customer (
	customerID	INT,
	firstName	CHAR(20),
	lastName	CHAR(20),
	email		CHAR(30),
	PRIMARY KEY (customerID)
	FOREIGN KEY (email) REFERENCES EmailPassword(email));

CREATE TABLE EmailPassword (
	email		CHAR(30),
	password	CHAR(30),
	PRIMARY KEY (email));


CREATE TABLE HotelCustomer (
	customerID		INT,
	hotelName		CHAR(20),
	hotelAddress   	CHAR(30),
	PRIMARY KEY (customerID, name, hotelAddress),
	FOREIGN KEY (customerID) REFERENCES Customer,
	FOREIGN KEY (hotelName, hotelAddress) REFERENCES HotelLocation(hotelName, hotelAddress));	

CREATE TABLE CleaningStaff_assignedBy (
	staffID 	INT,
	firstName 	CHAR(20),
	lastName	CHAR(20),
	employeeID 	INT,
	PRIMARY KEY (staffID),
	FOREIGN KEY (employeeID) REFERENCES ManagementEmployee_Manages);


CREATE TABLE Room (
	roomNum	INT,
	floor		INT,
	status		CHAR(20),
	hotelName	CHAR(20),
	hotelAddress	CHAR(30), 
	staffID		INT,              
	PRIMARY KEY (roomNum, floor),
	FOREIGN KEY (hotelName, hotelAddress) REFERENCES HotelLocation(hotelName, hotelAddress),
	FOREIGN KEY (staffID) REFERENCES Assignment(staffID)); 

CREATE TABLE Assignment (
	staffID		INT,              
	typeName	CHAR(20)  NOT NULL,
	PRIMARY KEY (staffID)
	FOREIGN KEY (staffID) REFERENCES  CleaningStaff_assignedBy(staffID),
	FOREIGN KEY (typeName) REFERENCES RoomType(typeName));

CREATE TABLE RoomType (
	typeName	CHAR(20),
	rate		INT,
	PRIMARY KEY (typeName));

CREATE TABLE Standard (
	typeName		CHAR(20),
	viewDirection 	CHAR(20),
	PRIMARY KEY (typeName),
	FOREIGN KEY (typeName) REFERENCES RoomType);	

CREATE TABLE Deluxe (
	typeName		CHAR(20),
	roomService 	CHAR(20),
	PRIMARY KEY (typeName),
	FOREIGN KEY (typeName) REFERENCES RoomType);

CREATE TABLE Suite (
	typeName			CHAR(20),
	privatePoolAccess	CHAR(20), 
	PRIMARY KEY (typeName),
	FOREIGN KEY (typeName) REFERENCES RoomType);

CREATE TABLE Executive (
	typeName			CHAR(20),
	executiveAmenities	CHAR(20),
	PRIMARY KEY (typeName),
	FOREIGN KEY (typeName) REFERENCES RoomType);

CREATE TABLE Family (
	typeName			CHAR(20) PRIMARY KEY,
	numBedrooms 		INT,
	numParkingSpaces 	INT,
	FOREIGN KEY (typeName) REFERENCES RoomType);



INSERT INTO ManagementEmployee_Manages (employeeID, firstName, lastName, hotelName, hotelAddress) VALUES ('1', 'John', 'Smith', 'Hotel A', '111 Main Street');
INSERT INTO ManagementEmployee_Manages (employeeID, firstName, lastName, hotelName, hotelAddress) VALUES ('2', 'Alice', 'Johnson', 'Hotel B', '222 Maple Avenue');
INSERT INTO ManagementEmployee_Manages (employeeID, firstName, lastName, hotelName, hotelAddress) VALUES ('3', 'Michael', 'Williams', 'Hotel C', '333 Oak Lane');
INSERT INTO ManagementEmployee_Manages (employeeID, firstName, lastName, hotelName, hotelAddress) VALUES ('4', 'Sarah', 'Davis', 'Hotel D', '444 Pine Ridge');
INSERT INTO ManagementEmployee_Manages (employeeID, firstName, lastName, hotelName, hotelAddress) VALUES ('5', 'Robert', 'Brown', 'Hotel E', '555 Granville Avenue');

INSERT INTO Views (employeeID, reservationID) VALUES ('1', '11');
INSERT INTO Views (employeeID, reservationID) VALUES ('2', '22');
INSERT INTO Views (employeeID, reservationID) VALUES ('3', '33');
INSERT INTO Views (employeeID, reservationID) VALUES ('4', '44');
INSERT INTO Views (employeeID, reservationID) VALUES ('5', '55');

INSERT INTO Reservation (reservationID, startDate, endDate, customerID, hotelName, hotelAddress, typeName) VALUES ('11', 'June 4, 2023', 'June 15, 2023', '111', 'Hotel A', '111 Main Street', 'Standard');
INSERT INTO Reservation (reservationID, startDate, endDate, customerID, hotelName, hotelAddress, typeName) VALUES ('22', 'June 29, 2023', 'July 3, 2023', '222', 'Hotel E', '555 Granville Avenue', 'Deluxe');
INSERT INTO Reservation (reservationID, startDate, endDate, customerID, hotelName, hotelAddress, typeName) VALUES ('33', 'July 2, 2023', 'July 12, 2023', '333', 'Hotel D', '444 Pine Ridge', 'Suite');
INSERT INTO Reservation (reservationID, startDate, endDate, customerID, hotelName, hotelAddress, typeName) VALUES ('44', 'August 16, 2023', 'August 23, 2023', '444', 'Hotel B', '222 Maple Avenue', 'Executive ');
INSERT INTO Reservation (reservationID, startDate, endDate, customerID, hotelName, hotelAddress, typeName) VALUES ('55', 'September 19, 2023', 'September 21, 2023', '555', 'Hotel C', '333 Oak Lane', 'Family');

INSERT INTO Billing_Has (reservationID, creditCard, billingName, billingAddress) VALUES ('11', '1049284938601918', 'Alice Johnson ', '432 Elm Street');
INSERT INTO Billing_Has (reservationID, creditCard, billingName, billingAddress) VALUES ('22', '3920573819474825', 'Michael Williams', '92 Birch Road');
INSERT INTO Billing_Has (reservationID, creditCard, billingName, billingAddress) VALUES ('33', '2038562965028461', 'Henry Ford', '15015 Aspen Circle');
INSERT INTO Billing_Has (reservationID, creditCard, billingName, billingAddress) VALUES ('44', '4083719339576904', 'Thomas Wyatt', '204 River Rock Road');
INSERT INTO Billing_Has (reservationID, creditCard, billingName, billingAddress) VALUES ('55', '6948103749014623', 'Taylor Rierson', '61 Fir Lane');

INSERT INTO Hotel (hotelName, hotelAddress, city) VALUES ('Hotel A', '111 Main Street', 'Vancouver');
INSERT INTO Hotel (hotelName, hotelAddress, city) VALUES ('Hotel B', '222 Maple Avenue', 'Vancouver');
INSERT INTO Hotel (hotelName, hotelAddress, city) VALUES ('Hotel C', '333 Oak Lane', 'Richmond');
INSERT INTO Hotel (hotelName, hotelAddress, city) VALUES ('Hotel D', '444 Pine Ridge', 'Burnaby');
INSERT INTO Hotel (hotelName, hotelAddress, city) VALUES ('Hotel E', '555 Granville Avenue', 'Vancouver');

INSERT INTO CitySeason (city, peakSeason) VALUES ('Vancouver', 'Summer ');
INSERT INTO CitySeason (city, peakSeason) VALUES ('Cancun', 'Winter');
INSERT INTO CitySeason (city, peakSeason) VALUES ('Richmond', 'Spring');
INSERT INTO CitySeason (city, peakSeason) VALUES ('Burnaby', 'Fall');
INSERT INTO CitySeason (city, peakSeason) VALUES ('Toronto', 'Spring');

INSERT INTO Customer (customerID, firstName, lastName, email) VALUES ('111', 'Ethan', 'Anderson', 'ethan.anderson@gmail.com');
INSERT INTO Customer (customerID, firstName, lastName, email) VALUES ('222', 'Ava', 'Mitchell', 'ava.mitchell@gmail.com');
INSERT INTO Customer (customerID, firstName, lastName, email) VALUES ('333', 'Benjamin', 'Turner', 'benjamin.turner@gmail.com');
INSERT INTO Customer (customerID, firstName, lastName, email) VALUES ('444', 'Isabella', 'Martinez', 'isabella.martinez@gmail.com');
INSERT INTO Customer (customerID, firstName, lastName, email) VALUES ('555', 'Oliver', 'Scott', 'oliver.scott@gmail.com');

INSERT INTO EmailPassword (email, password) VALUES ('ethan.anderson@gmail.com', 'password123');
INSERT INTO EmailPassword (email, password) VALUES ('ava.mitchell@gmail.com', 'secret123');
INSERT INTO EmailPassword (email, password) VALUES ('benjamin.turner@gmail.com', 'pass1234');
INSERT INTO EmailPassword (email, password) VALUES ('isabella.martinez@gmail.com', 'mypass4321');
INSERT INTO EmailPassword (email, password) VALUES ('oliver.scott@gmail.com', 'securepass123');

INSERT INTO HotelCustomer (customerID, hotelName, hotelAddress) VALUES ('111', 'Hotel A', '111 Main Street');
INSERT INTO HotelCustomer (customerID, hotelName, hotelAddress) VALUES ('222', 'Hotel B', '222 Maple Avenue');
INSERT INTO HotelCustomer (customerID, hotelName, hotelAddress) VALUES ('333', 'Hotel C', '333 Oak Lane');
INSERT INTO HotelCustomer (customerID, hotelName, hotelAddress) VALUES ('444', 'Hotel D', '444 Pine Ridge');
INSERT INTO HotelCustomer (customerID, hotelName, hotelAddress) VALUES ('555', 'Hotel E', '555 Granville Avenue');

INSERT INTO CleaningStaff_assignedBy (staffID, firstName, lastName, employeeID) VALUES ('1111', 'Lucas', 'Clark', '1');
INSERT INTO CleaningStaff_assignedBy (staffID, firstName, lastName, employeeID) VALUES ('2222', 'Chloe', 'Lopez', '2');
INSERT INTO CleaningStaff_assignedBy (staffID, firstName, lastName, employeeID) VALUES ('3333', 'Grace', 'Lee', '3');
INSERT INTO CleaningStaff_assignedBy (staffID, firstName, lastName, employeeID) VALUES ('4444', 'Eric', 'Wright', '4');
INSERT INTO CleaningStaff_assignedBy (staffID, firstName, lastName, employeeID) VALUES ('5555', 'Noah', 'Cook', '5');

INSERT INTO Room (roomNum, floor, status, hotelName, hotelAddress, staffID) VALUES ('12', '5', 'Available', 'Hotel A', '111 Main Street', '1111');
INSERT INTO Room (roomNum, floor, status, hotelName, hotelAddress, staffID) VALUES ('15', '8', 'Pending Cleaning', 'Hotel B', '222 Maple Avenue', '2222');
INSERT INTO Room (roomNum, floor, status, hotelName, hotelAddress, staffID) VALUES ('4', '15', 'Booked', 'Hotel C', '333 Oak Lane', '3333');
INSERT INTO Room (roomNum, floor, status, hotelName, hotelAddress, staffID) VALUES ('10', '22', 'Available', 'Hotel D', '444 Pine Ridge', '4444');
INSERT INTO Room (roomNum, floor, status, hotelName, hotelAddress, staffID) VALUES ('3', '4', 'Booked', 'Hotel E', '555 Granville Avenue', '5555');

INSERT INTO Assignment (staffID, typeName) VALUES ('1111', 'Standard-Mountain');
INSERT INTO Assignment (staffID, typeName) VALUES ('2222', 'Deluxe-Cleaning');
INSERT INTO Assignment (staffID, typeName) VALUES ('3333', 'Suite-Infinity');
INSERT INTO Assignment (staffID, typeName) VALUES ('4444', 'Executive-Jacuzzi');
INSERT INTO Assignment (staffID, typeName) VALUES ('5555', 'Family-Couple');

INSERT INTO RoomType (typeName, rate) VALUES ('Standard-Mountain', '60');
INSERT INTO RoomType (typeName, rate) VALUES ('Deluxe-Cleaning', '120');
INSERT INTO RoomType (typeName, rate) VALUES ('Suite-Infinity', '250');
INSERT INTO RoomType (typeName, rate) VALUES ('Executive-Jacuzzi', '400');
INSERT INTO RoomType (typeName, rate) VALUES ('Family-Couple', '310');

INSERT INTO Standard (typeName, viewDirection) VALUES ('Standard-Mountain', 'North');
INSERT INTO Standard (typeName, viewDirection) VALUES ('Standard-East', 'East');
INSERT INTO Standard (typeName, viewDirection) VALUES ('Standard-Ocean', 'South');
INSERT INTO Standard (typeName, viewDirection) VALUES ('Standard-Pool', 'South');
INSERT INTO Standard (typeName, viewDirection) VALUES ('Standard-West', 'West');

INSERT INTO Deluxe (typeName, roomService) VALUES ('Deluxe-Basic', 'Food and Beverage');
INSERT INTO Deluxe (typeName, roomService) VALUES ('Deluxe-Cleaning', 'Housekeeping');
INSERT INTO Deluxe (typeName, roomService) VALUES ('Deluxe-Gourmet', 'Food and Beverage');
INSERT INTO Deluxe (typeName, roomService) VALUES ('Deluxe-DryClean', 'Laundry/Dry Cleaning');
INSERT INTO Deluxe (typeName, roomService) VALUES ('Deluxe-Spa', 'In-Room Spa');

INSERT INTO Suite (typeName, privatePool) VALUES ('Suite-Terrace', 'Pool Terrace');
INSERT INTO Suite (typeName, privatePool) VALUES ('Suite-Cabana', 'Pool Cabana');
INSERT INTO Suite (typeName, privatePool) VALUES ('Suite-Plunge', 'Plunge Pool');
INSERT INTO Suite (typeName, privatePool) VALUES ('Suite-Courtyard', 'Pool Courtyard');
INSERT INTO Suite (typeName, privatePool) VALUES ('Suite-Infinity', 'Infinity Pool');

INSERT INTO Executive (typeName, executiveAmenities) VALUES ('Executive-Jacuzzi', 'Jacuzzi');
INSERT INTO Executive (typeName, executiveAmenities) VALUES ('Executive-Butler', 'Butler Service');
INSERT INTO Executive (typeName, executiveAmenities) VALUES ('Executive-Chauffeur', 'Private Chauffeur');
INSERT INTO Executive (typeName, executiveAmenities) VALUES ('Executive-Balcony', 'Private Balcony');
INSERT INTO Executive (typeName, executiveAmenities) VALUES ('Executive-Valet', 'Valet Parking');

INSERT INTO Family (typeName, rate, numBedrooms, numParkingSpaces) VALUES ('Family-Hut', '260', '2', '1');
INSERT INTO Family (typeName, rate, numBedrooms, numParkingSpaces) VALUES ('Family-Cabin', '310', '2', '2');
INSERT INTO Family (typeName, rate, numBedrooms, numParkingSpaces) VALUES ('Family-Caravan', '250', '2', '3');
INSERT INTO Family (typeName, rate, numBedrooms, numParkingSpaces) VALUES ('Family-Couple', '195', '1', '1');
INSERT INTO Family (typeName, rate, numBedrooms, numParkingSpaces) VALUES ('Family-Extended', '370', '3', '2');
