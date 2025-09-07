<?php

  $inData = getRequestInfo();

  $userID = $inData["userID"];
  $searchTerm = $inData["searchTerm"];

  $conn = new mysqli("localhost", "UnNamed", "Small", "COP4331", 3306);

  if ($conn->connect_error)
  {
    returnWithError($conn->connect_error);
  }
  else
  {
    $stmt = $conn->prepare("SELECT name, email, phone FROM Contacts WHERE name LIKE ? AND user_id=?");
    
    $searchPattern = '%' . $searchTerm . '%';
    
    $stmt->bind_param("si", $searchPattern, $userID);
    
    $stmt->execute();

    $result = $stmt->get_result();

    $contacts = [];
    
    while($row = $result->fetch_assoc())
    {
      array_push($contacts, $row);
    }
    
    returnWithInfo($contacts);

    $stmt->close();
    $conn->close();
  }

  function getRequestInfo()
  {
    return json_decode(file_get_contents('php://input'), true);
  }

  function returnWithInfo($contacts)
  {
    if (count($contacts) == 0)
    {
      sendResultInfoAsJSON(null);
    }
    else
    {
      sendResultInfoAsJSON($contacts);
    }
  }

  function sendResultInfoAsJSON($obj)
  {
    header('Content-type: application/json');
    if ($obj == null)
    {
      echo "No contacts match the given information";
    }
    else
    {
      echo json_encode($obj);
    }
  }
  
  function returnWithError($err)
  {
      $retValue = '{"error":"' . $err . '"}';
      sendResultInfoAsJSON($retValue);
  }

?>
