-- start create.sql

CREATE TABLE CitySeason (
	city		CHAR(20),
	peakSeason	CHAR(20),
	PRIMARY KEY (city)
);

CREATE TABLE HotelLocation (
 	hotelName		CHAR(20),
 	hotelAddress   	CHAR(30),
 	city			CHAR(20),
 	PRIMARY KEY (hotelName, hotelAddress),
 	FOREIGN KEY (city) REFERENCES CitySeason(city)
);

CREATE TABLE EmailPassword (
	email		CHAR(30),
	password	CHAR(30),
	PRIMARY KEY (email)
);

CREATE TABLE RoomType (
	typeName	CHAR(20),
	rate		INT,
	PRIMARY KEY (typeName)
);

CREATE TABLE Customer (
	customerID	INT,
	firstName	CHAR(20),
	lastName	CHAR(20),
	email		CHAR(30),
	PRIMARY KEY (customerID),
	FOREIGN KEY (email) REFERENCES EmailPassword(email)
);

CREATE TABLE ManagementEmployee_Manages (
	employeeID 		INT,
	firstName		CHAR(20),
	lastName		CHAR(20),
	hotelName		CHAR(20),
	hotelAddress	CHAR(30),
	PRIMARY KEY (employeeID),
	FOREIGN KEY (hotelName,hotelAddress) REFERENCES HotelLocation(hotelName, hotelAddress)
);

CREATE TABLE Reservation (
	reservationID	INT,
	startDate 		DATE,
	endDate			DATE,
	customerID		INT NOT NULL,
	hotelName		CHAR(20) NOT NULL,
	hotelAddress	CHAR(30) NOT NULL,
	typeName		CHAR(20) NOT NULL,
	PRIMARY KEY (reservationID),
	FOREIGN KEY (customerID) REFERENCES Customer(customerID),
	FOREIGN KEY (hotelName, hotelAddress) REFERENCES HotelLocation(hotelName, hotelAddress),
	FOREIGN KEY (typeName) REFERENCES RoomType(typeName)
);

CREATE TABLE HotelCustomer (
	customerID		INT,
	hotelName		CHAR(20),
	hotelAddress   	CHAR(30),
	PRIMARY KEY (customerID, hotelName, hotelAddress),
	FOREIGN KEY (customerID) REFERENCES Customer(customerID),
	FOREIGN KEY (hotelName, hotelAddress) REFERENCES HotelLocation(hotelName, hotelAddress)
);

CREATE TABLE CleaningStaff_assignedBy (
	staffID 	INT,
	firstName 	CHAR(20),
	lastName	CHAR(20),
	employeeID 	INT,
	PRIMARY KEY (staffID),
	FOREIGN KEY (employeeID) REFERENCES ManagementEmployee_Manages(employeeID)
);

CREATE TABLE Billing_Has (
	reservationID	INT,
	creditCard		CHAR(16),
	billingName		CHAR(20),
	billingAddress	CHAR(30),
	PRIMARY KEY (creditCard, reservationID),
	FOREIGN KEY (reservationID) REFERENCES Reservation(reservationID) ON DELETE CASCADE
);

CREATE TABLE Views (
	employeeID     	INT,
	reservationID	INT,
	PRIMARY KEY (reservationID, employeeID),
	FOREIGN KEY (reservationID) REFERENCES Reservation(reservationID),
	FOREIGN KEY (employeeID) REFERENCES ManagementEmployee_Manages(employeeID)
);

CREATE TABLE Assignment (
	staffID		INT,
	typeName	CHAR(20) NOT NULL,
	PRIMARY KEY (staffID),
	FOREIGN KEY (staffID) REFERENCES CleaningStaff_assignedBy(staffID),
	FOREIGN KEY (typeName) REFERENCES RoomType(typeName)
);

CREATE TABLE Room (
	roomNum	    CHAR(10),
	floor		INT,
	status		CHAR(20),
	hotelName	CHAR(20),
	hotelAddress	CHAR(30),
	staffID		INT,
	PRIMARY KEY (roomNum, floor),
	FOREIGN KEY (hotelName, hotelAddress) REFERENCES HotelLocation(hotelName, hotelAddress),
	FOREIGN KEY (staffID) REFERENCES Assignment(staffID)
);

CREATE TABLE Standard (
	typeName		CHAR(20),
	viewDirection 	CHAR(20),
	PRIMARY KEY (typeName),
	FOREIGN KEY (typeName) REFERENCES RoomType(typeName)
);

CREATE TABLE Deluxe (
	typeName		CHAR(20),
	roomService 	CHAR(20),
	PRIMARY KEY (typeName),
	FOREIGN KEY (typeName) REFERENCES RoomType(typeName)
);

CREATE TABLE Suite (
	typeName			CHAR(20),
	privatePoolAccess	CHAR(20),
	PRIMARY KEY (typeName),
	FOREIGN KEY (typeName) REFERENCES RoomType(typeName)
);

CREATE TABLE Executive (
	typeName			CHAR(20),
	executiveAmenities	CHAR(20),
	PRIMARY KEY (typeName),
	FOREIGN KEY (typeName) REFERENCES RoomType(typeName)
);

CREATE TABLE Family (
	typeName			CHAR(20) PRIMARY KEY,
	numBedrooms 		INT,
	numParkingSpaces 	INT,
	FOREIGN KEY (typeName) REFERENCES RoomType(typeName)
);
