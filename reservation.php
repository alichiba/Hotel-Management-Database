<html>
    <head>
        <title>Hotel Reservation</title>
    </head>

    <body>
    <h1>Reservation</h1>

    <h2>Reset</h2>
        <p>Resetting the hotel reservation form </p>

        <form method="POST" action="r20.php">
            <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
            <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
            <p><input type="submit" value="Reset" name="reset"></p>
        </form>

    <h2>Insert Query</h2>
        <p>Creating a new reservation with a reservationID, start date, and end date</p>
        <form method="POST" action="r20.php"> <!--refresh page when submitted-->
	        <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
	        ReservationID: <input type="text" name="resID"> <br /><br />
	        Start Date: <input type="text" name="startDate"> <br /><br />
	        End Date: <input type="text" name="endDate"> <br /><br />
            Room Type Name: 
                <select name = "typeName">
                    <option value = "Standard-Mountain">Standard-Mountain</option>
                    <option value = "Deluxe-Cleaning">Deluxe-Cleaning</option>
                    <option value = "Suite-Infinity">Suite-Infinity</option>
                    <option value = "Executive-Jacuzzi">Executive-Jacuzzi</option>
                    <option value = "Family-Couple">Family-Couple</option>
                </select> <br /><br />
	        <input type="submit" value="Insert" name="insertSubmit"></p>
        </form>

        <hr />

         <h2>Update Query</h2>
        <p>Updating an existing reservation with a new start and end date</p>
        <form method="POST" action="r20.php"> <!--refresh page when submitted-->
            <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
	        ReservationID: <input type="text" name="resID"> <br /><br />
            Old Start Date: <input type="text" name="oldStart"> <br /><br />
            New Start Date: <input type="text" name="newStart"> <br /><br />
            Old End Date: <input type="text" name="oldEnd"> <br /><br />
            New End Date: <input type="text" name="newEnd"> <br /><br />
            <input type="submit" value="Update" name="updateSubmit"></p>
        </form>

        <h2>Delete Query</h2>
        <p>Deleting a reservation and the associated billing information based on room typeName</p>
        <form method="POST" action="r20.php"> <!--refresh page when submitted-->
            <input type="hidden" id="deleteQueryRequest" name="deleteQueryRequest">
            <select name = "formStatus">
                <option value = "Standard-Mountain">Standard-Mountain</option>
                <option value = "Deluxe-Cleaning">Deluxe-Cleaning</option>
                <option value = "Suite-Infinity">Suite-Infinity</option>
                <option value = "Executive-Jacuzzi">Executive-Jacuzzi</option>
                <option value = "Family-Couple">Family-Couple</option>
            </select>

            <input type="submit" value="Delete" name="deleteSubmit"></p>
        </form>

        <h2>Select Query</h2>
        <p>Viewing reservations at a specific hotel with a start date of after input</p>
        <form method="POST" action="r20.php"> <!--refresh page when submitted-->
            <input type="hidden" id="selectQueryRequest" name="selectQueryRequest">
            Hotel Name:
                <select name = "formStatus">
                    <option value = "Hotel A">Hotel A</option>
                    <option value = "Hotel B">Hotel B</option>
                    <option value = "Hotel C">Hotel C</option>
                    <option value = "Hotel D">Hotel D</option>
                    <option value = "Hotel E">Hotel E</option>
                </select> <br /><br />
            Start Date: <input type="text" name="startDate"> <br /><br />    
            <input type="submit" value="Select" name="selectSubmit"></p>
        </form>



        <h2>Count the Tuples in Reservation</h2>
        <form method="GET" action="r20.php"> <!--refresh page when submitted-->
            <input type="hidden" id="countTupleRequest" name="countTupleRequest">
            <input type="submit" name="countTuples"></p>
        </form>

        <a href="index.php"><button type="button">Back</button></a>


        <?php
		//this tells the system that it's no longer just parsing html; it's now parsing PHP

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
            $db_conn = OCILogon("ora_elenaguo", "a52870235", "dbhost.students.cs.ubc.ca:1522/stu"); // <- db url

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


        // INSERT Query 
        function handleInsertRequest() {
            global $db_conn;

            //Getting the values from user and insert data into the table (array)
            $tuple = array (
                ":bind1" => $_POST['resID'],    // retrieves parameter passed by user
                ":bind2" => $_POST['startDate'],
                ":bind3" => $_POST['endDate'],
                ":bind4" => '333',
                ":bind5" => 'Hotel C',
                ":bind6" => '333 Oak Lane',
                ":bind7" => $_POST['typeName']
            );

            $alltuples = array (
                $tuple
            );

            executeBoundSQL("insert into Reservation values (:bind1, TO_DATE(:bind2, 'YYYY-MM-DD'), TO_DATE(:bind3, 'YYYY-MM-DD'), :bind4, :bind5,
            :bind6, :bind7)", $alltuples); 
            OCICommit($db_conn); 

            echo "<br>Retrieved data from Reservation:<br>";
            echo "<table>";
            echo "<tr><th>ReservationID</th><th>startDate</th><th>endDate</th><th>customerID</th><th>hotelName</th><th>hotelAddress</th><th>typeName</th></tr>";
            while (($row = OCI_Fetch_Array($result, OCI_BOTH)) != false) {
                echo $row[0];
            }
            echo "<table>";
        }

        // UPDATE Query 
        function handleUpdateRequest() {
            global $db_conn;

            $res_id = $_POST['resID'];
            $old_start = $_POST['oldStart'];
            $new_start = $_POST['newStart'];
            $old_end = $_POST['oldEnd'];
            $new_end = $_POST['newEnd'];

    
            executePlainSQL("UPDATE Reservation SET startDate=TO_DATE('" . $new_start . "', 'YYYY-MM-DD') WHERE startDate=TO_DATE('" . $old_start . "', 'YYYY-MM-DD') AND reservationID='" . $res_id . "'");
            executePlainSQL("UPDATE Reservation SET endDate=TO_DATE('" . $new_end . "', 'YYYY-MM-DD') WHERE endDate=TO_DATE('" . $old_end . "', 'YYYY-MM-DD') AND reservationID='" . $res_id . "'");
            OCICommit($db_conn);


            echo "<br>Retrieved data from Reservation:<br>";
            echo "<table>";
            echo "<tr><th>ReservationID</th><th>startDate</th><th>endDate</th><th>customerID</th><th>hotelName</th><th>hotelAddress</th><th>typeName</th></tr>";
            while (($row = OCI_Fetch_Array($result, OCI_BOTH)) != false) {
                echo $row[0];
            }
            echo "<table>";
        }


        // DELETE Query - deleting reservation based of room typeName
        // !!! Update CREATE TABLE statements to have ON DELETE CASCADE for all? or just billing_has? or RoomType on delete cascade?
        function handleDeleteRequest() {
            global $db_conn;

            $status = $_POST['formStatus'];

            $result = executePlainSQL("DELETE FROM Reservation WHERE typeName= '" . $status . "'");


            echo "<br>Retrieved data from Reservation:<br>";
            echo "<table>";
            echo "<tr><th>ReservationID</th><th>startDate</th><th>endDate</th><th>customerID</th><th>hotelName</th><th>hotelAddress</th><th>typeName</th></tr>";
            while (($row = OCI_Fetch_Array($result, OCI_BOTH)) != false) {
                echo $row[0];
            }
            echo "<table>";
        }

        // SELECT Query - viewing reservations based off of hotelName and start date
        // !!! Double check > operator works
        function handleSelectRequest() {
            global $db_conn;

            $status = $_POST['formStatus'];
            $start_date = $_POST['startDate'];

            $result = executePlainSQL("SELECT FROM Reservation WHERE hotelName= '" . $status . "' AND startDate>TO_DATE('" . $start_date . "', 'YYYY-MM-DD')");


            echo "<br>Retrieved data from Reservation:<br>";
            echo "<table>";
            echo "<tr><th>ReservationID</th><th>startDate</th><th>endDate</th><th>customerID</th><th>hotelName</th><th>hotelAddress</th><th>typeName</th></tr>";
            while (($row = OCI_Fetch_Array($result, OCI_BOTH)) != false) {
                echo $row[0];
            }
            echo "<table>";

        }

        function handleCountRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM Reservation");

            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in Reservation: " . $row[0] . "<br>";
            }
        }
   
        // RESET Form - Dropping and creating tables
        function handleResetRequest() {
            global $db_conn;
            // Drop old table
            executePlainSQL("DROP TABLE Reservation");

            // Create new table
            echo "<br> creating new table <br>";
            executePlainSQL("CREATE TABLE Reservation (reservationID int PRIMARY KEY, startDate char(30), endDate char(30), typeName char(30))");
            OCICommit($db_conn);
        }

        // HANDLE ALL POST ROUTES
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('resetTablesRequest', $_POST)) {
                    handleResetRequest();
                } else if (array_key_exists('updateQueryRequest', $_POST)) {
                    handleUpdateRequest();
                } else if (array_key_exists('insertQueryRequest', $_POST)) {
                    handleInsertRequest();
                } else if (array_key_exists('deleteQueryRequest', $_POST)) {
                    handleDeleteRequest();
                } else if (array_key_exists('selectQueryRequest', $_POST)) {
                    handleSelectRequest();
                } 

                disconnectFromDB(); // program is completed, application has finished running (1-3s). when submitting new request, restarting application from start-end
            }
        }

        // HANDLE ALL GET ROUTES
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('countTuples', $_GET)) {
                    handleCountRequest();
                }

                disconnectFromDB();
            }
        }

		if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit']) || isset($_POST['deleteSubmit']) || isset($_POST['selectSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['countTupleRequest'])) {
            handleGETRequest();
        }
		?>
	</body>
</html>
