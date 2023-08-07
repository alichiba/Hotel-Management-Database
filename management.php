<html>
    <head>
        <title>Hotel Management</title>
    </head>

    <body>
        <h1>Management</h1>

        <h2>Join Query</h2>
        <p>Select the room number, floor, status, staff ID, full name of the staff associated to the room based on the input provided for the status</p>
        <form method="POST" action="management.php"> <!--refresh page when submitted-->
            <input type="hidden" id="joinQueryRequest" name="joinQueryRequest">
            <select name = "formStatus">
                <option value = "Available">Available</option>
                <option value = "Booked">Booked</option>
                <option value = "Pending Cleaning">Pending Cleaning</option>
            </select>

            <input type="submit" value="Join" name="joinSubmit"></p>
        </form>

        <h2>Aggregation with GROUP BY Query</h2>
        <p> Find the number of customers for each hotel</p>
        <form method="GET" action="management.php"> <!--refresh page when submitted-->
              <input type="hidden" id="countCustomerRequest" name="countCustomerRequest">
              <input type="submit" name="countCustomers"></p>
        </form>

        <h2>Aggregation with Having Query</h2>
        <p>Find employees who manage more than 3 cleaning staff</p>
        <form method="GET" action="management.php"> <!--refresh page when submitted-->
              <input type="hidden" id="countStaffRequest" name="countStaffRequest">
              <input type="submit" name="countStaff"></p>
        </form>

        <h2>Division</h2>
        <p>Find customers who have visited all hotels</p>
        <form method="GET" action="management.php"> <!--refresh page when submitted-->
              <input type="hidden" id="loyalCustomerRequest" name="loyalCustomerRequest">
              <input type="submit" name="loyalCustomer"></p>
        </form>

        <a href="index.php"><button type="button">Back</button></a>

        <?php
        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

        function debugAlertMessage($message) {
             global $show_debug_alert_messages;

             if ($show_debug_alert_messages) {
                 echo "<script type='text/javascript'>alert('" . $message . "');</script>";
             }
        }

        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            //echo "<br>running ".$cmdstr."<br>";
            global $db_conn, $success;

            $statement = OCIParse($db_conn, $cmdstr);
            //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

            if (!$statement) {
                 echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                 $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                 echo htmlentities($e['message']);
                 $success = False;
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                echo htmlentities($e['message']);
                $success = False;
            }

            return $statement;
        }

        function executeBoundSQL($cmdstr, $list) {
        /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
        In this case you don't need to create the statement several times. Bound variables cause a statement to only be
        parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
        See the sample code below for how this function is used */

            global $db_conn, $success;
            $statement = OCIParse($db_conn, $cmdstr);

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn);
                echo htmlentities($e['message']);
                $success = False;
            }

            foreach ($list as $tuple) {
                foreach ($tuple as $bind => $val) {
                    //echo $val;
                    //echo "<br>".$bind."<br>";
                    OCIBindByName($statement, $bind, $val);
                    unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
                    }

                $r = OCIExecute($statement, OCI_DEFAULT);
                if (!$r) {
                    echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                        $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                    echo htmlentities($e['message']);
                    echo "<br>";
                    $success = False;
                }
            }
        }

        function printResult($result) { //prints results from a select statement
            echo "<br>Retrieved data from table:<br>";
            echo "<table>";
            echo "<tr><th>Column 1</th><th>Column 2</th></tr>";

            while (($row = OCI_Fetch_Array($result, OCI_BOTH)) != false) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>"; //or just use "echo $row[0]"
            }

            echo "</table>";
        }

        function connectToDB() {
            global $db_conn;

            $db_conn = OCILogon("ora_alisonh2", "a89783757", "dbhost.students.cs.ubc.ca:1522/stu");

            if ($db_conn) {
                debugAlertMessage("Database is Connected");
                return true;
            } else {
                debugAlertMessage("Cannot connect to Database");
                $e = OCI_Error(); // For OCILogon errors pass no handle
                echo htmlentities($e['message']);
                return false;
                }
            }

        function disconnectFromDB() {
            global $db_conn;

            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }


        // JOIN Query - select staff based off status
        function handleJoinRequest() {
            global $db_conn;

            $status = $_POST['formStatus'];


            executePlainSQL("SELECT R.roomNum, R.floor, R.status, CS.staffID, CS.firstName, CS.lastName
                                FROM Room R, CleaningStaff_assignedBy CS
                                    WHERE R.staffID = CS.staffID AND R.status = " . $status . "");
            OCICommit($db_conn);
        }

        // PROJECTION Query - Aggregation with Group By (number of customers per hotel)
        function handleCountRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT R.hotelName, COUNT(*) AS numOfCustomers FROM Reservation R
                                       GROUP BY R.hotelName ORDER BY numOfCustomers ASC");

            // while (($row = oci_fetch_row($result)) != false) {
            //     echo "<br> The number of customers at " . $row[0] . "is" . $row[1] . "<br>";
            // }
            echo "<br>Retrieved data from Reservation:<br>";
            echo "<table>";
            echo "<tr><th>Hotel Name</th><th>numOfCustomers</th></tr>";
            while (($row = OCI_Fetch_Array($result, OCI_BOTH)) != false) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>"; //or just use "echo $row[0]"
            }
            echo "</table>";

        }

        // Aggregation with Having Query (count employees with over 3 staff)
        function handleCountStaffRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT employeeID, COUNT(*) AS numStaff
            FROM CleaningStaff_assignedBy
            GROUP BY employeeID
            HAVING COUNT(*) > 3");

            // while (($row = oci_fetch_row($result)) != false) {
            //     echo "<br>" . $row[0] . "has" . $row[1] . " staff <br>";
            // }

            echo "<br>Retrieved data from CleaningStaff_assignedBy:<br>";
            echo "<table>";
            echo "<tr><th>EmployeeID</th><th>numStaff</th></tr>";
            while (($row = OCI_Fetch_Array($result, OCI_BOTH)) != false) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>"; //or just use "echo $row[0]"
            }
            echo "</table>";

        }

        // DIVISION QUERY (find customers who have visited all hotels)
        function handleLoyalCustomerRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT C.customerID, C.firstName, C.lastName
            FROM Customer C
            WHERE NOT EXISTS
            ((SELECT H.hotelName, H.hotelAddress
            FROM HotelLocation H)
            MINUS
            (SELECT X.hotelName, X.hotelAddress
            FROM HotelCustomer X
            WHERE X.customerID = C.customerID))");

            echo "<br>Retrieved data from Customer:<br>";
            echo "<table>";
            echo "<tr><th>CustomerID</th><th>First Name</th><th>Last Name</th></tr>";
            while (($row = OCI_Fetch_Array($result, OCI_BOTH)) != false) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td></tr>"; //or just use "echo $row[0]"
            }
            echo "</table>";

        }


        // HANDLE ALL POST ROUTES
            // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('joinQueryRequest', $_POST)) {
                    handleJoinRequest();
                }

                disconnectFromDB();
            }
        }

        // HANDLE ALL GET ROUTES
            // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('countCustomers', $_GET)) {
                    handleCountRequest();
                }
                if (array_key_exists('countStaff', $_GET)) {
                    handleCountStaffRequest();
                }
                if (array_key_exists('loyalCustomer', $_GET)) {
                    handleLoyalCustomerRequest();
                }

                disconnectFromDB();
           }
        }

        if (isset($_POST['joinSubmit'])){
            handlePOSTRequest();
        } else if (isset($_GET['countCustomerRequest']) || isset($_GET['countStaffRequest']) || isset($_GET['loyalCustomerRequest'])) {
             handleGETRequest();
        }

//         function handleResetRequest() {
//             global $db_conn;
//             // Drop old table
//             executePlainSQL("DROP TABLE demoTable");
//
//             // Create new table
//             echo "<br> creating new table <br>";
//             executePlainSQL("CREATE TABLE demoTable (id int PRIMARY KEY, name char(30))");
//             OCICommit($db_conn);
//         }

//         function handleInsertRequest() {
//             global $db_conn;
//
//             //Getting the values from user and insert data into the table
//             $tuple = array (
//                 ":bind1" => $_POST['insNo'],
//                 ":bind2" => $_POST['insName']
//             );
//
//             $alltuples = array (
//             $tuple
//             );
//
//             executeBoundSQL("insert into demoTable values (:bind1, :bind2)", $alltuples);
//             OCICommit($db_conn);
//         }

        ?>

	</body>
</html>


