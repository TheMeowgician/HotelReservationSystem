<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Information - Darrel&Ayien Hotels</title>
    <link href="./output.css" rel="stylesheet"> <link href="./global.css" rel="stylesheet"> </head>

<body class="bg-[#FFFFFF] flex flex-col min-h-screen ">
    <header class="lg:px-16 px-4 bg-[#FFFFFF] flex flex-wrap items-center py-4 shadow-md sticky top-0 z-50">
        <div class="flex-1 flex justify-between items-center">
            <a href="index.html#home" class="text-xl font-[PoppinsBold] text-[38px]">Darrel&Ayien</a> </div>
        <div class="hidden md:flex md:items-center md:w-auto w-full" id="menu">
            <nav>
                <ul class="md:flex items-center justify-between text-[18px] text-gray-700 pt-4 md:pt-0 md:gap-x-4">
                     <li><a class="block px-4 py-2 transition-colors duration-200" href="index.html#home">Home</a></li>
                    <li><a class="block px-4 py-2 transition-colors duration-200" href="index.html#about">About</a></li>
                    <li><a class="block px-4 py-2 transition-colors duration-200 bg-black text-white rounded-md" href="index.html#reservation">Reservation</a></li> <li><a class="block px-4 py-2 transition-colors duration-200 md:mb-0 mb-2" href="index.html#contact">Contact Us</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="flex-grow container mx-auto px-4 py-10 lg:py-16">
        <h1 class="text-3xl md:text-4xl font-[PoppinsBold] text-center mb-8 text-gray-800">Billing Information</h1>
        <div class="w-full lg:w-3/4 xl:w-2/3 mx-auto bg-white p-6 shadow-lg rounded-lg border border-gray-200 overflow-x-auto">
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                
                $customerName = isset($_POST['customerName']) ? htmlspecialchars($_POST['customerName']) : 'N/A';
                $contactNumber = isset($_POST['contactNumber']) ? htmlspecialchars($_POST['contactNumber']) : 'N/A';
                $fromDate = isset($_POST['fromDate']) ? htmlspecialchars($_POST['fromDate']) : 'N/A';
                $toDate = isset($_POST['toDate']) ? htmlspecialchars($_POST['toDate']) : 'N/A';
                $roomType = isset($_POST['roomType']) ? htmlspecialchars($_POST['roomType']) : 'N/A';
                $roomCapacity = isset($_POST['roomCapacity']) ? htmlspecialchars($_POST['roomCapacity']) : 'N/A';
                $paymentType = isset($_POST['paymentType']) ? htmlspecialchars($_POST['paymentType']) : 'N/A';

                $numberOfDays = 0;
                if ($fromDate !== 'N/A' && $toDate !== 'N/A') {
                    try {
                       $date1 = new DateTime($fromDate);
                       $date2 = new DateTime($toDate);
                       $diff = $date2->diff($date1);
                       $numberOfDays = ($diff->days > 0) ? $diff->days : 0; 
                    } catch (Exception $e) {
                        
                        $numberOfDays = 0; 
                        echo "<p class='text-red-500 text-center mb-4'>Error calculating dates. Please check your input.</p>";
                    }
                } else {
                    echo "<p class='text-red-500 text-center mb-4'>Missing date information.</p>";
                }


                $ratePerDay = 0;
                $additionalCharge = 0;
                $discountPercent = 0;

                
                $lRoomCapacity = strtolower($roomCapacity);
                $lRoomType = strtolower($roomType);
                $lPaymentType = strtolower($paymentType);

                if ($lRoomCapacity == "single" && $lRoomType == "regular") $ratePerDay = 100.0;
                else if ($lRoomCapacity == "single" && $lRoomType == "deluxe") $ratePerDay = 300.0;
                else if ($lRoomCapacity == "single" && $lRoomType == "suite") $ratePerDay = 500.0;
                else if ($lRoomCapacity == "double" && $lRoomType == "regular") $ratePerDay = 200.0;
                else if ($lRoomCapacity == "double" && $lRoomType == "deluxe") $ratePerDay = 500.0;
                else if ($lRoomCapacity == "double" && $lRoomType == "suite") $ratePerDay = 800.0;
                else if ($lRoomCapacity == "family" && $lRoomType == "regular") $ratePerDay = 500.0;
                else if ($lRoomCapacity == "family" && $lRoomType == "deluxe") $ratePerDay = 750.0;
                else if ($lRoomCapacity == "family" && $lRoomType == "suite") $ratePerDay = 1000.0;

                
                if($lPaymentType == "cheque") $additionalCharge = 0.05;
                else if($lPaymentType == "credit-card") $additionalCharge = 0.10;

                
                if($lPaymentType == "cash" && $numberOfDays >= 6) $discountPercent = 0.15;
                else if($lPaymentType == "cash" && $numberOfDays >= 3) $discountPercent = 0.10; 


                
                $subTotalBeforeCharges = $ratePerDay * $numberOfDays;
                $discountAmount = $subTotalBeforeCharges * $discountPercent;
                $discountedTotal = $subTotalBeforeCharges - $discountAmount;
                $additionalChargeAmount = $discountedTotal * $additionalCharge;
                $totalBill = $discountedTotal + $additionalChargeAmount;

                
                echo "<table class='w-full border-collapse text-sm'>";
                echo "<thead>";
                echo "<tr>";
                echo "<th class='border border-gray-300 px-4 py-2 bg-gray-100 text-left font-semibold text-gray-700'>Field</th>";
                echo "<th class='border border-gray-300 px-4 py-2 bg-gray-100 text-left font-semibold text-gray-700'>Value</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                
                function echo_table_row($label, $value) {
                    echo "<tr class='odd:bg-white even:bg-gray-50'>";
                    echo "<td class='border border-gray-300 px-4 py-2 text-gray-800 font-medium'>" . htmlspecialchars($label) . "</td>";
                    echo "<td class='border border-gray-300 px-4 py-2 text-gray-800'>" . htmlspecialchars($value) . "</td>";
                    echo "</tr>";
                }

                echo_table_row("Customer Name", $customerName);
                echo_table_row("Contact Number", $contactNumber);
                echo_table_row("From Date", $fromDate);
                echo_table_row("To Date", $toDate);
                echo_table_row("Number of Days", $numberOfDays);
                echo_table_row("Room Type", $roomType);
                echo_table_row("Room Capacity", $roomCapacity);
                echo_table_row("Payment Type", $paymentType);
                echo_table_row("Rate Per Day", "$" . number_format($ratePerDay, 2));
                echo_table_row("Subtotal (Before Charges)", "$" . number_format($subTotalBeforeCharges, 2));

                if ($discountPercent > 0) {
                    echo_table_row("Discount (" . ($discountPercent * 100) . "%)", "-$" . number_format($discountAmount, 2));
                }
                 if ($additionalCharge > 0) {
                     echo_table_row("Additional Charge (" . ($additionalCharge * 100) . "%)", "+$" . number_format($additionalChargeAmount, 2));
                 }

                
                 echo "<tr class='bg-gray-200 font-bold'>";
                 echo "<td class='border border-gray-300 px-4 py-2 text-gray-900'>Total Bill</td>";
                 echo "<td class='border border-gray-300 px-4 py-2 text-gray-900'>$" . number_format($totalBill, 2) . "</td>";
                 echo "</tr>";


                echo "</tbody>";
                echo "</table>";

            } else {
                
                echo "<p class='text-center text-red-500'>No reservation data submitted.</p>";
            }
            ?>
             <div class="text-center mt-8">
                <a href="index.html#reservation" class="inline-block px-6 py-2 text-base font-semibold rounded-md shadow-sm transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500">Back to Reservation</a>
            </div>
        </div>
    </div>

    <footer class="bg-gray-800 text-gray-400 py-8 mt-auto">
        <div class="container mx-auto px-4 text-center">
            <p class="text-sm">
                &copy; 2025 Darrel&Ayien Hotels. All Rights Reserved.
            </p>
            <p class="text-xs mt-1">
                Taguig, Metro Manila, Philippines
            </p>
        </div>
    </footer>
    </body>
</html>