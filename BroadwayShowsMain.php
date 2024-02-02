<?php
//MAIN
require_once("BroadwayShowsInclude.php");

date_default_timezone_set ('America/Toronto');

// Connect to database
$mysqlObj = CreateConnectionObject();
$TableName = "BroadwayShows";

WriteHeaders("Broadway Shows!", "Broadway Shows");

echo "<form action=? method=post>";

if(isset($_POST['f_displayData'])) // Display Data
{   
    ShowDataForm($mysqlObj, $TableName);
}
else if(isset($_POST['f_saveRecord'])) // Save Record
{
    AddRecordToTable($mysqlObj, $TableName);

}
else if(isset($_POST['f_addRecord'])) // Add Record
{
    AddRecordForm();
}
else if (isset($_POST['f_createTable'])) // Create Table
{
    CreateTableForm($mysqlObj, $TableName);
}
else
{
    echo "<p>You can create a table and then add new records to keep track of your show tickets!<p>";
    echo "<p>Don't forget to display your data after to view all your tickets!<p>";
    DisplayMainForm();
}

echo "</form>";

CloseConnection($mysqlObj);

WriteFooters();
//END OF MAIN



// Main Page
function DisplayMainForm()
{
    // Create Table Button
    DisplayButton("f_createTable", "Create Table", 
        "btnCreateTable.png", "Create Table");

    // Add Record Button
    DisplayButton("f_addRecord", "Add Record", 
        "btnAddRecord.png", "Add Record");

    // Display Data Button
    DisplayButton("f_displayData", "Display Data", 
        "btnDisplayData.png", "Display Data");
}



// Create Table Page
function CreateTableForm(&$mysqlObj, $TableName)
{
    $showName = "showName VARCHAR(50) PRIMARY KEY";
    $performanceDateAndTime = "performanceDateAndTime DATETIME";
    $nbrTickets = "nbrTickets INT";
    $ticketPrice = "ticketPrice DECIMAL(5,2)";
    $totalCost = "totalCost DECIMAL(8,2)";

    //Drop Table
    $SQLStatement = "DROP TABLE IF EXISTS $TableName;";

    $stmtObj = new mysqli_stmt($mysqlObj);
    $stmtObj = $mysqlObj->prepare($SQLStatement);
    $CreateResult = $stmtObj->execute();

    // Create Table
    $SQLStatement = "
    CREATE TABLE $TableName ($showName, $performanceDateAndTime, 
        $nbrTickets, $ticketPrice, $totalCost);";


    // Prepare
    $stmtObj = $mysqlObj->prepare($SQLStatement);

    // Execute
    $CreateResult = $stmtObj->execute();

    if($CreateResult) // Table created
    {
        echo "<p>Table $TableName created.</p>";
    }
    else // Table not created
    {
        echo "Unable to create $TableName.";
    }

    $stmtObj->close();     

    DisplayButton("home", "Home", "btnHome.png", "Home");
}



// Add Record Page
function AddRecordForm()
{
    // Get current time and date
    $currentDate = date("Y-n-d");
    $currentTime = date("H:i");

    // Show name
    echo "<div class=\"datapair\">";
        DisplayLabel("Show Name: ");
        DisplayTextbox("text", "f_showName", 20, null, true);
    echo "</div>
    <div class=\"datapair\">";

    // Performance date
        DisplayLabel("Performance Date: ");
        DisplayTextbox("date", "f_performanceDate", 20, $currentDate);
    echo "</div>
    <div class=\"datapair\">";

    // Performance time
        DisplayLabel("Performance Time: ");
        DisplayTextbox("time", "f_performanceTime", 20, $currentTime);
    echo "</div>
    <div class=\"datapair\">";

    // Number of tickets
        DisplayLabel("Number of Tickets: ");
        DisplayTextbox("number", "f_nbrTickets", 20, 2);
    echo "</div>
    <div class=\"datapair\">";

    // Ticket price
        DisplayLabel("Ticket Price: ");
        echo "  <p>
                    <input type='radio' id='price100' 
                        name='f_ticketPrice' value='100.0' checked='checked'>
                    <label for='price100'>$100</label>
                </p>
                <p>
                    <input type='radio' id='price150' \
                        name='f_ticketPrice' value='150.0'>
                    <label for='price150'>$150</label>
                </p>
                <p>
                    <input type='radio' id='price200' 
                        name='f_ticketPrice' value='200.0'>
                    <label for='price200'>$200</label>
                </p>";

    echo "</div>";


    // Save Button
    DisplayButton("f_saveRecord", "Save Record", "btnSaveRecord.png",
         "Save Record");

    // Home Button
    DisplayButton("home", "Home", "btnHome.png", "Home");
}



// Save Record Page
function AddRecordToTable(&$mysqlObj, $TableName)
{
    // get data from POST array
    $addShowName = $_POST['f_showName'];
    $addPerformanceDateAndTime = 
        $_POST['f_performanceDate'] . " " . $_POST['f_performanceTime'];
    $addNbrTickets = $_POST['f_nbrTickets'];
    $addTicketPrice = $_POST['f_ticketPrice'];

    // Calculate Subtotal
    $subTotal = $addNbrTickets * $addTicketPrice;

    // Calculate Tax
    $tax = $subTotal * 0.13;

    // Calculate Total
    $totalCost = $subTotal + $tax;

    // Prepare insert
    $query = "INSERT INTO $TableName (showName, performanceDateAndTime, 
        nbrTickets, ticketPrice, totalCost ) Values (?, ?, ?, ?, ?)";
    $stmtObj = new mysqli_stmt($mysqlObj);
    $stmtObj = $mysqlObj->prepare($query);

    if ($stmtObj == false)
    {
        echo "Prepare failed on $query " . $mysqlObj->error;
        exit;
    }

    // Bind
    $BindSuccess = $stmtObj->bind_param("ssidd", $addShowName, 
        $addPerformanceDateAndTime, $addNbrTickets, 
        $addTicketPrice, $totalCost);


    // Execute statement
    if ($BindSuccess)
    {
        $success = $stmtObj->execute();
        if($success)
        {
            echo "<p>Record successfully added to $TableName</p>";
        }
        else
        {
            echo "<p>Unable to add record to $TableName</p>";
        }
    }

    // Home button
    DisplayButton("home", "Home", "btnHome.png", "Home");
}



// Display Data page
function ShowDataForm(&$mysqlObj, $TableName)   
{
    $query = "SELECT showName, performanceDateAndTime, 
        nbrTickets, ticketPrice, totalCost
    FROM $TableName
    ORDER BY ticketPrice, nbrTickets DESC";

    // Prepare
    $stmtObj = new mysqli_stmt($mysqlObj);
    $stmtObj = $mysqlObj->prepare($query);

    // Execute
    $success = $stmtObj->execute();

    // Bind results
    $stmtObj->bind_result($replyShowName, $replyPerformanceDateAndTime, 
        $replyNbrTickets, $replyTicketPrice, $replyTotalCost);

    echo "<table>
            <tr>
                <th class=\"tableShowName\">Show Name</th>
                <th class=\"tablePerformanceDateAndTime\">
                    Performance Date and Time</th>
                <th class=\"tableNumberOfTickets\">Number of Tickets</th>
                <th class=\"tableTicketPrice\">Ticket Price</th>
                <th class=\"tableTotalCost\">Total Cost</th>
            </tr>";

    $numOfRowsReturned = 0;


    // Display results
    While($stmtObj->fetch())
    {
        echo "<tr>
                <td class=\"tableShowName\">$replyShowName</td>
                <td class=\"tablePerformanceDateAndTime\">
                    $replyPerformanceDateAndTime</td>
                <td class=\"tableNumberOfTickets\">$replyNbrTickets</td>
                <td class=\"tableTicketPrice\">\$$replyTicketPrice</td>
                <td class=\"tableTotalCost\">\$$replyTotalCost</td>
            </tr>";

        // Increase number of rows
        $numOfRowsReturned++;
    }   

    echo "</table>";

    echo "<p>$numOfRowsReturned bookings to date.</p>";

    $stmtObj->close();

    DisplayButton("home", "Home", "btnHome.png", "Home");
}

?>