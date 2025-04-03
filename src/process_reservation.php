<?php

$db_host = 'localhost';
$db_name = 'hotel_reservation_db'; 
$db_user = 'root';
$db_pass = '';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'An unknown error occurred.'];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $customerName = trim(filter_input(INPUT_POST, 'customerName', FILTER_SANITIZE_STRING));
    $contactNumber = trim(filter_input(INPUT_POST, 'contactNumber', FILTER_SANITIZE_STRING));
    $fromDate = trim(filter_input(INPUT_POST, 'fromDate', FILTER_SANITIZE_STRING));
    $toDate = trim(filter_input(INPUT_POST, 'toDate', FILTER_SANITIZE_STRING));
    $roomType = trim(filter_input(INPUT_POST, 'roomType', FILTER_SANITIZE_STRING));
    $roomCapacity = trim(filter_input(INPUT_POST, 'roomCapacity', FILTER_SANITIZE_STRING));
    $paymentType = trim(filter_input(INPUT_POST, 'paymentType', FILTER_SANITIZE_STRING));

    
    $numberOfDays = 0; $totalBill = 0; $ratePerDay = 0; $additionalCharge = 0;
    $discountPercent = 0; $subTotalBeforeCharges = 0; $discountAmount = 0;
    $discountedTotal = 0; $additionalChargeAmount = 0; $calculation_error_message = null;

    
     if (!empty($fromDate) && !empty($toDate) && !empty($roomType) && !empty($roomCapacity) && !empty($paymentType)) {
        try {
             $date1 = new DateTime($fromDate); $date2 = new DateTime($toDate);
             if ($date2 >= $date1) { $diff = $date2->diff($date1); $numberOfDays = $diff->days == 0 ? 1 : $diff->days; }
             else { throw new Exception("Check-out date cannot be before check-in date."); }
             $lRoomCapacity = strtolower($roomCapacity); $lRoomType = strtolower($roomType); $lPaymentType = strtolower($paymentType);
             
             if ($lRoomCapacity == "single" && $lRoomType == "regular") $ratePerDay = 100.0; else if ($lRoomCapacity == "single" && $lRoomType == "deluxe") $ratePerDay = 300.0; else if ($lRoomCapacity == "single" && $lRoomType == "suite") $ratePerDay = 500.0; else if ($lRoomCapacity == "double" && $lRoomType == "regular") $ratePerDay = 200.0; else if ($lRoomCapacity == "double" && $lRoomType == "deluxe") $ratePerDay = 500.0; else if ($lRoomCapacity == "double" && $lRoomType == "suite") $ratePerDay = 800.0; else if ($lRoomCapacity == "family" && $lRoomType == "regular") $ratePerDay = 500.0; else if ($lRoomCapacity == "family" && $lRoomType == "deluxe") $ratePerDay = 750.0; else if ($lRoomCapacity == "family" && $lRoomType == "suite") $ratePerDay = 1000.0; else { $ratePerDay = 0; }
             
             if($lPaymentType == "cheque") $additionalCharge = 0.05; else if($lPaymentType == "credit-card") $additionalCharge = 0.10;
             if($lPaymentType == "cash" && $numberOfDays >= 6) $discountPercent = 0.15; else if($lPaymentType == "cash" && $numberOfDays >= 3) $discountPercent = 0.10;
             $subTotalBeforeCharges = $ratePerDay * $numberOfDays; $discountAmount = $subTotalBeforeCharges * $discountPercent; $discountedTotal = $subTotalBeforeCharges - $discountAmount; $additionalChargeAmount = $discountedTotal * $additionalCharge; $totalBill = $discountedTotal + $additionalChargeAmount;
        } catch (Exception $e) { $calculation_error_message = "Calculation Error: " . $e->getMessage(); $totalBill = null; }
    } else { $calculation_error_message = "Missing required fields for calculation."; $totalBill = null; }
    
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false];

    try {
        
        $pdo = new PDO($dsn, $db_user, $db_pass, $options);

        
        $createTableSql = "
        CREATE TABLE IF NOT EXISTS reservations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_name VARCHAR(255) NOT NULL,
            contact_number VARCHAR(50),
            from_date DATE NOT NULL,
            to_date DATE NOT NULL,
            room_type VARCHAR(50),
            room_capacity VARCHAR(50),
            payment_type VARCHAR(50),
            number_of_days INT,
            total_bill DECIMAL(10, 2),
            reservation_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        $pdo->exec($createTableSql); 

        $sql = "INSERT INTO reservations (customer_name, contact_number, from_date, to_date, room_type, room_capacity, payment_type, number_of_days, total_bill)
                VALUES (:customer_name, :contact_number, :from_date, :to_date, :room_type, :room_capacity, :payment_type, :number_of_days, :total_bill)";
        $stmt = $pdo->prepare($sql);

        
        $stmt->bindParam(':customer_name', $customerName);
        $stmt->bindParam(':contact_number', $contactNumber);
        $stmt->bindParam(':from_date', $fromDate);
        $stmt->bindParam(':to_date', $toDate);
        $stmt->bindParam(':room_type', $roomType);
        $stmt->bindParam(':room_capacity', $roomCapacity);
        $stmt->bindParam(':payment_type', $paymentType);
        $dbNumberOfDays = ($numberOfDays > 0) ? $numberOfDays : 0;
        $stmt->bindParam(':number_of_days', $dbNumberOfDays, PDO::PARAM_INT);
        $dbTotalBill = ($totalBill !== null) ? $totalBill : null;
        $stmt->bindParam(':total_bill', $dbTotalBill);

        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Reservation successfully saved!';
            
            $response['billing_details'] = [ 
                'customer_name' => $customerName, 'contact_number' => $contactNumber, 'from_date' => $fromDate,
                'to_date' => $toDate, 'number_of_days' => $numberOfDays, 'room_type' => $roomType,
                'room_capacity' => $roomCapacity, 'payment_type' => $paymentType, 'rate_per_day' => $ratePerDay,
                'subtotal' => $subTotalBeforeCharges, 'discount_percent' => $discountPercent, 'discount_amount' => $discountAmount,
                'additional_charge_percent' => $additionalCharge, 'additional_charge_amount' => $additionalChargeAmount,
                'total_bill' => $totalBill, 'calculation_error' => $calculation_error_message
            ];
        } else {
            $response['message'] = 'Database execute failed: Failed to save reservation.';
        }

    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        $response['message'] = "Database error occurred: " . $e->getMessage(); 
    } catch (Exception $e) {
        error_log("General Error: " . $e->getMessage());
        $response['message'] = "An error occurred during processing.";
    }

} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
exit;
?>