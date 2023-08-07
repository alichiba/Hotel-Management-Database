# Hotel Booking and Management System

https://www.students.cs.ubc.ca/~alisonh2/index.php

## Description:

Our main perspective focuses on the administrative view of a hotel, although a customer view is also integrated with this. Customers will have specific information associated with their account such as a unique customer ID, name, and email, and should be standard between customers. Each room will be uniquely identified by their room number and floor, and will also have a status of available, booked, or pending cleaning. Room types will be modeled using an ISA hierarchy, where rooms will be one of the following: standard, deluxe, suite, executive, or family, each of which have different attributes. Reservations are uniquely identified with a reservation ID and will be associated with only one customer. Reservations also include information about the dates and hotel booked. Billing is a weak entity that is dependent on Reservation. Each billing contains credit card information and additional information about the name and address associated with the billing.

The system will have 2 users of the system: customers and management employees. Customers should be able to access their own account information, as well as view their bookings. They should be able to update their account information and bookings, including canceling their booking prior to the cancellation date. Management employees manage the hotel and are able to view all data in the system except for personal information of customers and sensitive billing information. Management employees are responsible for assigning cleaning staff to hotel rooms if the room status is pending cleaning.

Hotel booking and management systems are beneficial to hotels for a variety of reasons. These systems can be utilized for a variety of different bookings, including family vacations, business trips, and solo travel. The database can allow customers to view their booking information, room and room type, and charges, all in one spot. This database also allows management to capture snapshots of what rooms are available and which staff are assigned at one point in time, which can result in better scheduling and higher utilization of cleaning staff. Furthermore, this database can be adapted to other room rental services such as AirBnB, Vrbo, etc.

### Platform and Application technology stack:

This project will be implemented using the UBC CPSC department’s oracle database system and PHP.

### ER Diagrams:

(Initial ER Diagram from Milestone 1):

<img width="671" alt="Screen Shot 2023-07-28 at 11 52 49 PM" src="https://media.github.students.cs.ubc.ca/user/14937/files/20d20435-cf9e-4c1a-87cf-b2a50688ac0e">

---

(Updated ER Diagram from Milestone 2):

<img width="880" alt="Screen Shot 2023-07-28 at 11 53 04 PM" src="https://media.github.students.cs.ubc.ca/user/14937/files/6ae348e2-441d-4b81-849e-1bb3bc94ee76">

