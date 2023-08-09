
<html>
    <head>
        <title>Hotel Customer</title>
    </head>

    <body>
        <h1>Customer</h1>

        <h2>Projection Query</h2>
        <p>Finding the hotel with rooms available</p>
        <form method="GET" action="customer.php"> <!--refresh page when submitted-->
              <input type="hidden" id="countRoomRequest" name="countRoomRequest">
              <input type="submit" name="countRooms"></p>
        </form>

        <h2>Nested Aggregation Query</h2>
        <p>Finding number of rooms on each floor at hotels above the average floor level (across all hotels)</p>
        <form method="GET" action="customer.php"> <!--refresh page when submitted-->
              <input type="hidden" id="highRoomsRequest" name="highRoomsRequest">
              <input type="submit" name="highRooms"></p>
        </form>

        <h2>Insert Query</h2>
        <p>Creating a new reservation with a start and end date</p>
        <form method="POST" action="customer.php"> <!--refresh page when submitted-->
	        <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
	        ReservationID: <input type="text" name="resID"> <br /><br />
	        Start Date: <input type="text" name="startDate"> <br /><br />
	        End Date: <input type="text" name="endDate"> <br /><br />
	        <input type="submit" value="Insert" name="insertSubmit"></p>
        </form>

        <h2>Update Query</h2>
        <p>Updating an existing reservation with a new start and end date</p>
        <form method="POST" action="customer.php"> <!--refresh page when submitted-->
            <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
	        ReservationID: <input type="text" name="resID"> <br /><br />
            Old Start Date: <input type="text" name="oldStart"> <br /><br />
            New Start Date: <input type="text" name="newStart"> <br /><br />
            Old End Date: <input type="text" name="oldEnd"> <br /><br />
            New End Date: <input type="text" name="newEnd"> <br /><br />
            <input type="submit" value="Update" name="updateSubmit"></p>
        </form>

        <h2>Count the Tuples in ReservationTable</h2>
        <form method="GET" action="customer.php"> <!--refresh page when submitted-->
            <input type="hidden" id="countTupleRequest" name="countTupleRequest">
            <input type="submit" name="countTuples"></p>
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
    
                // Your username is ora_(CWL_ID) and the password is a(student number). For example,
                // ora_platypus is the username and a12345678 is the password.
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


        // PROJECTION Query - find hotels with rooms available
        function handleCountRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT roomNum, floor, status, hotelName, hotelAddress FROM Room WHERE status = 'Available'");

            // if (($row = oci_fetch_row($result)) != false) {
            //     echo "<br> The number of available rooms are: " . $row[0] . "<br>";
            // }

            echo "<br>Retrieved data from Room:<br>";
            echo "<table>";
            echo "<tr><th>Room Num</th><th>Floor</th><th>Status</th><th>Hotel Name</th><th>Hotel Address</th></tr>";
            while (($row = OCI_Fetch_Array($result, OCI_BOTH)) != false) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><td>" . $row[4] . "</td></tr>"; //or just use "echo $row[0]"
            }
            echo "</table>";
        }

        // Nested Aggregation Query - find number of rooms on higher floors in a hotel compared to overall average floor
        function handleHighRoomsRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT R.floor, R.hotelName, R.hotelAddress, COUNT(*) as numHigh
            FROM Room R
            GROUP BY R.floor, R.hotelName, R.hotelAddress
            HAVING R.floor > (SELECT AVG(R2.floor)
            FROM Room R2)");

            // if (($row = oci_fetch_row($result)) != false) {
            //     echo "<br> The number of available rooms are: " . $row[0] . "<br>";
            // }

            echo "<br>Retrieved data from Room:<br>";
            echo "<table>";
            echo "<tr><th>Floor</th><th>Hotel Name</th><th>Hotel Address</th><th>numHigh</th></tr>";
            while (($row = OCI_Fetch_Array($result, OCI_BOTH)) != false) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td></tr>"; //or just use "echo $row[0]"
            }
            echo "</table>";
        }

        function handleInsertRequest() {
            global $db_conn;

            //Getting the values from user and insert data into the table (array)
            $tuple = array (
                ":bind1" => $_POST['resID'],    // retrieves parameter passed by user
                ":bind2" => $_POST["startDate"],
                ":bind3" => $_POST["endDate"],
                ":bind4" => '333',
                ":bind5" => 'Hotel C',
                ":bind6" => '333 Oak Lane',
                ":bind7" => 'Standard-East',
            );

            $alltuples = array (
                $tuple
            );

            echo "<br> here " . "<br>";

            executeBoundSQL("insert into Reservation values (:bind1,  TO_DATE(:bind2,'YYYY-MM-DD'), TO_DATE(:bind3,'YYYY-MM-DD'), :bind4, :bind5, :bind6, :bind7)", $alltuples); // helper function - queries script into database
            echo "<br> Inserted: " . "<br>";
            OCICommit($db_conn); // must commit when adding smth to oracle database
            
        }

        function handleUpdateRequest() {
            global $db_conn;

            $res_id = $_POST['resID'];
            $old_start = $_POST['oldStart'];
            $new_start = $_POST['newStart'];
            $old_end = $_POST['oldEnd'];
            $new_end = $_POST['newEnd'];

            // you need the wrap the old name and new name values with single quotations
            executePlainSQL("UPDATE Reservation SET startDate='" . $new_start . "' WHERE startDate='" . $old_start . "' AND reservationID='" . $res_id . "'");
            executePlainSQL("UPDATE Reservation SET endDate='" . $new_end . "' WHERE endDate='" . $old_end . "' AND reservationID='" . $res_id . "'");
            OCICommit($db_conn);
        }

        function handleCountTupleRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM Reservation");

            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in Reservation: " . $row[0] . "<br>";
            }
        }


        // HANDLE ALL POST ROUTES
     	    // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('resetTablesRequest', $_POST)) {
                    handleResetRequest();
                } else if (array_key_exists('updateQueryRequest', $_POST)) {
                    handleUpdateRequest();
                } else if (array_key_exists('insertQueryRequest', $_POST)) {
                    handleInsertRequest();
                } 

                disconnectFromDB();
            }
        }

        // HANDLE ALL GET ROUTES
        	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('countRooms', $_GET)) {
                    handleCountRequest();
                }
                if (array_key_exists('highRooms', $_GET)) {
                    handleHighRoomsRequest();
                }
                if (array_key_exists('countTuples', $_GET)) {
                    handleCountTupleRequest();
                }

                disconnectFromDB();
           }
        }

        
        if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit']) || isset($_POST['updateQueryRequest']) || isset($_POST['insertQueryRequest'])) {
            handlePOSTRequest();
        } else if (isset($_GET['countRoomRequest']) || isset($_GET['highRoomsRequest']) || isset($_GET['countTupleRequest'])) {
             handleGETRequest();
        }

      	?>

	</body>
</html>
