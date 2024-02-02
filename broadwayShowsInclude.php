<?php
// Header
function WriteHeaders($Heading="Welcome", $TitleBar="MySite")
{
    echo "
        <!doctype html>
        <html lang = \"en\">
        <head>
            <meta charset = \"UTF-8\">
            <link rel =\"stylesheet\" type = \"text/css\" 
                href=\"broadwayShows.css\" />
            <title>$TitleBar</title>\n
        </head>
        <body>\n
            <h1>$Heading</h1>\n
    ";
}



// create a label element
function DisplayLabel($prompt)
{
    echo "<label>$prompt</label>";
}



// create an input element
function DisplayTextbox($htmlInputType, $name, $size, $value = 0, 
    $hasFocus = false)
{
    $focus = "";

    if($hasFocus)
    {
        echo "<input type=\"$htmlInputType\" name=\"$name\" size=\"$size\" 
            value=\"$value\" autofocus>";
    }
    else
    {
        echo "<input type=\"$htmlInputType\" name=\"$name\" size=\"$size\" 
            value=\"$value\">";
    }
}



// Create an image element
function DisplayImage($fileName, $alt, $height, $width)
{
    echo "
    <img src=\"$fileName\" alt=\"$alt\" height=\"$height\" width=\"$width\">
    ";
}



// Create a button element
function DisplayButton($buttonName, $buttonText, $fileName="", $alt="")
{
            if ($fileName == "")
            {
                echo "<button type=\"submit\" name=\"$buttonName\">
                    $buttonText</button>";
            }
            else
            {
                echo "<button type=\"submit\" name=\"$buttonName\" 
                    class=\"btnPic\">";
                    DisplayImage($fileName, $alt, "25", "105");
                echo "</button>";
            }
}



// Displays contact information
function DisplayContactInfo()
{
    echo "
        <footer>
            Questions? Comments? 
                <a href= mailto:\"example@email.com\">
                    example@email.com</a>
        </footer>
    ";
}



// Footer
function WriteFooters()
{
    DisplayContactInfo();
    echo "</body>\n";
    echo "</html>\n";
}



// database connection
function CreateConnectionObject()
{
    $fh = fopen('auth.txt','r');
    $Host =  trim(fgets($fh));
    $UserName = trim(fgets($fh));
    $Password = trim(fgets($fh));
    $Database = trim(fgets($fh));
    $Port = trim(fgets($fh)); 
    fclose($fh);
    $mysqlObj = new mysqli($Host, $UserName, $Password,$Database,$Port);

    if ($mysqlObj->connect_errno != 0) 
    {
     echo "<p>Connection failed. Unable to open database $Database. Error: "
              . $mysqlObj->connect_error . "</p>";
     exit;
    }

    return ($mysqlObj);
}



// Closes the object passed to it
function CloseConnection(&$closeObject)
{
    $closeObject->close();
}
?>