<?php
include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30, '/');
   header('location:index.php');
}

if(isset($_POST['check'])){

   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);
   $adults = isset($_POST['adults']) ? (int)$_POST['adults'] : 0;
   $childs = isset($_POST['childs']) ? (int)$_POST['childs'] : 0;
   $rooms = isset($_POST['rooms']) ? (int)$_POST['rooms'] : 1;

   $today = date('Y-m-d');

   if($check_in < $today){
      $warning_msg[] = 'Check-in date cannot be in the past.';
   } else {
      // Room capacity validation
      $max_adults = $rooms * 2;
      $max_childs = $rooms * 1;

      if($adults > $max_adults || $childs > $max_childs){
         $warning_msg[] = 'Rooms are not available. Please book more rooms.';
      } else {
         $total_rooms = 0;

         $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
         $check_bookings->execute([$check_in]);

         while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
            $total_rooms += $fetch_bookings['rooms'];
         }

         // if the hotel has total 30 rooms 
         if($total_rooms + $rooms > 30){
            $warning_msg[] = 'rooms are not available';
         } else {
            $success_msg[] = 'rooms are available';
         }
      }
   }
}

if(isset($_POST['book'])){

   $booking_id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $rooms = $_POST['rooms'];
   $rooms = (int)filter_var($rooms, FILTER_SANITIZE_NUMBER_INT);
   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);
   $check_out = $_POST['check_out'];
   $check_out = filter_var($check_out, FILTER_SANITIZE_STRING);
   $adults = isset($_POST['adults']) ? (int)$_POST['adults'] : 0;
   $childs = isset($_POST['childs']) ? (int)$_POST['childs'] : 0;
   $total_price = $_POST['total_price'];
   $total_price = (float)filter_var($total_price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

   // Debug: Log the submitted price
   error_log("Submitted total_price: {$_POST['total_price']}, Sanitized: $total_price");

   $today = date('Y-m-d');

   if($check_in < $today){
      $warning_msg[] = 'Check-in date cannot be in the past.';
   } elseif($check_out < $check_in){
      $warning_msg[] = 'Check-out date cannot be before check-in date.';
   } elseif($total_price <= 0){
      $warning_msg[] = 'Invalid price. Please select valid dates and rooms.';
   } else {
      // Room capacity validation
      $max_adults = $rooms * 2;
      $max_childs = $rooms * 1;

      if($adults > $max_adults || $childs > $max_childs){
         $warning_msg[] = 'Too many guests for selected rooms. Max 2 adults and 1 child per room allowed.';
      } else {

         $total_rooms = 0;

         $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
         $check_bookings->execute([$check_in]);

         while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
            $total_rooms += $fetch_bookings['rooms'];
         }

         if($total_rooms + $rooms > 30){
            $warning_msg[] = 'rooms are not available';
         } else {

            $verify_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND name = ? AND email = ? AND number = ? AND rooms = ? AND check_in = ? AND check_out = ? AND adults = ? AND childs = ? AND total_price = ?");
            $verify_bookings->execute([$user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs, $total_price]);

            if($verify_bookings->rowCount() > 0){
               $warning_msg[] = 'room booked already!';
            } else {
               $book_room = $conn->prepare("INSERT INTO `bookings`(booking_id, user_id, name, email, number, rooms, check_in, check_out, adults, childs, total_price) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
               $book_room->execute([$booking_id, $user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs, $total_price]);
               $success_msg[] = 'room booked successfully!';
            }

         }
      }
   }
}

if(isset($_POST['send'])){

   $id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $message = $_POST['message'];
   $message = filter_var($message, FILTER_SANITIZE_STRING);

   $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $verify_message->execute([$name, $email, $number, $message]);

   if($verify_message->rowCount() > 0){
      $warning_msg[] = 'message sent already!';
   }else{
      $insert_message = $conn->prepare("INSERT INTO `messages`(id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$id, $name, $email, $number, $message]);
      $success_msg[] = 'message sent successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- home section starts  -->

<section class="home" id="home">

   <div class="swiper home-slider">

      <div class="swiper-wrapper">

         <div class="box swiper-slide">
            <img src="images/home-img-1.jpg" alt="">
            <div class="flex">
               <h3>luxurious rooms</h3>
               <a href="#availability" class="btn">check availability</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/home-img-2.jpg" alt="">
            <div class="flex">
               <h3>foods and drinks</h3>
               <a href="#reservation" class="btn">make a reservation</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/home-img-3.jpg" alt="">
            <div class="flex">
               <h3>luxurious halls</h3>
               <a href="#contact" class="btn">contact us</a>
            </div>
         </div>

      </div>

      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>

   </div>

</section>

<!-- home section ends -->

<!-- availability section starts  -->

<section class="availability" id="availability">
   <form action="" method="post">
      <div class="flex">
         <div class="box">
            <p>check in <span>*</span></p>
            <input type="date" name="check_in" class="input" required min="<?= date('Y-m-d'); ?>" id="check_in">
         </div>
         <div class="box">
            <p>check out <span>*</span></p>
            <input type="date" name="check_out" class="input" required id="check_out">
         </div>
         <div class="box">
            <p>adults <span>*</span></p>
            <select name="adults" class="input" id="adults" required onchange="suggestRooms()">
               <option value="1">1 adult</option>
               <option value="2">2 adults</option>
               <option value="3">3 adults</option>
               <option value="4">4 adults</option>
               <option value="5">5 adults</option>
               <option value="6">6 adults</option>
            </select>
         </div>
         <div class="box">
            <p>childs <span>*</span></p>
            <select name="childs" class="input" id="childs" required onchange="suggestRooms()">
               <option value="0">0 child</option>
               <option value="1">1 child</option>
               <option value="2">2 childs</option>
               <option value="3">3 childs</option>
               <option value="4">4 childs</option>
               <option value="5">5 childs</option>
               <option value="6">6 childs</option>
            </select>
         </div>
         <div class="box">
            <p>rooms <span>*</span></p>
            <select name="rooms" class="input" id="rooms" required onchange="updateAvailabilityPrice()">
               <option value="1">1 room</option>
               <option value="2">2 rooms</option>
               <option value="3">3 rooms</option>
               <option value="4">4 rooms</option>
               <option value="5">5 rooms</option>
               <option value="6">6 rooms</option>
            </select>
         </div>
         <div class="box">
            <p>total price <span>*</span></p>
            <input type="text" name="total_price" class="input" id="availability_price" readonly value="$0.00">
         </div>
      </div>
      <input type="submit" value="check availability" name="check" class="btn">
   </form>
</section>

<!-- about section starts  -->

<section class="about" id="about">

   <div class="row">
      <div class="image">
         <img src="images/about-img-1.png" alt="">
      </div>
      <div class="content">
         <h3>best staff</h3>
         <p>Our team is composed of experienced professionals dedicated to delivering exceptional service and support to every client.</p>
         <a href="#reservation" class="btn">make a reservation</a>
      </div>
   </div>

   <div class="row revers">
      <div class="image">
         <img src="images/about-img-2.jpg" alt="">
      </div>
      <div class="content">
         <h3>best foods</h3>
         <p>Experience the vibrant flavors of Mombasa with our selection of Swahili-inspired dishes, featuring coastal delicacies like biryani, pilau, samaki wa kupaka, and fresh coconut-infused curries.</p>
         <a href="#contact" class="btn">contact us</a>
      </div>
   </div>

   <div class="row">
      <div class="image">
         <img src="images/about-img-3.jpg" alt="">
      </div>
      <div class="content">
         <h3>swimming pool</h3>
         <p>Enjoy a refreshing swim in our crystal-clear pool, just steps away from the salty breeze and stunning views of the Indian Ocean along Mombasa’s beautiful coastline.</p>
         <a href="#availability" class="btn">check availability</a>
      </div>
   </div>

</section>

<!-- about section ends -->

<!-- services section starts  -->

<section class="services">

   <div class="box-container">

      <div class="box">
         <img src="images/icon-1.png" alt="">
         <h3>food & drinks</h3>
         <p>Delight in a wide variety of coastal cuisine and refreshing drinks, from spicy Swahili dishes to tropical juices and expertly crafted cocktails inspired by the flavors of Mombasa.</p>
      </div>

      <div class="box">
         <img src="images/icon-2.png" alt="">
         <h3>outdoor dining</h3>
         <p>Savor delicious meals in a serene outdoor setting, surrounded by swaying palm trees and the gentle ocean breeze—perfect for enjoying Mombasa’s warm evenings and vibrant atmosphere.</p>
      </div>

      <div class="box">
         <img src="images/icon-3.png" alt="">
         <h3>beach view</h3>
         <p>Take in breathtaking views of the Indian Ocean right from your doorstep, with golden sands, gentle waves, and unforgettable Mombasa sunsets creating the perfect coastal escape.</p>
      </div>

      <div class="box">
         <img src="images/icon-4.png" alt="">
         <h3>decorations</h3>
         <p>Our spaces are beautifully adorned with Swahili-inspired décor, blending traditional coastal art, rich textures, and vibrant colors to create an authentic and inviting Mombasa atmosphere.</p>
      </div>

      <div class="box">
         <img src="images/icon-5.png" alt="">
         <h3>swimming pool</h3>
         <p>Dive into our refreshing swimming pool surrounded by tropical ambiance, with views of the Indian Ocean and the soothing sounds of the coast creating a truly relaxing experience.</p>
      </div>

      <div class="box">
         <img src="images/icon-6.png" alt="">
         <h3>resort beach</h3>
         <p>Step onto our private resort beach, where soft white sands meet the clear, salty waters of the Indian Ocean—perfect for sunbathing, beach walks, and unforgettable coastal adventures in Mombasa.</p>
      </div>

   </div>

</section>

<!-- services section ends -->

<!-- reservation section starts  -->

<section class="reservation" id="reservation">
   <form action="" method="post" onsubmit="updateReservationPrice()">
      <h3>make a reservation</h3>
      <div class="flex">
         <div class="box">
            <p>your name <span>*</span></p>
            <input type="text" name="name" maxlength="50" required placeholder="enter your name" class="input">
         </div>
         <div class="box">
            <p>your email <span>*</span></p>
            <input type="email" name="email" maxlength="50" required placeholder="enter your email" class="input">
         </div>
         <div class="box">
            <p>your number <span>*</span></p>
            <input type="number" name="number" maxlength="10" min="0" max="9999999999" required placeholder="enter your number" class="input">
         </div>
         <div class="box">
            <p>rooms <span>*</span></p>
            <select name="rooms" class="input" id="res_rooms" required onchange="updateReservationPrice()">
               <option value="1" selected>1 room</option>
               <option value="2">2 rooms</option>
               <option value="3">3 rooms</option>
               <option value="4">4 rooms</option>
               <option value="5">5 rooms</option>
               <option value="6">6 rooms</option>
            </select>
         </div>
         <div class="box">
            <p>check in <span>*</span></p>
            <input type="date" name="check_in" class="input" min="<?= date('Y-m-d'); ?>" required id="res_check_in">
         </div>
         <div class="box">
            <p>check out <span>*</span></p>
            <input type="date" name="check_out" class="input" required id="res_check_out">
         </div>
         <div class="box">
            <p>adults <span>*</span></p>
            <select name="adults" class="input" id="res_adults" required onchange="suggestReservationRooms()">
               <option value="1" selected>1 adult</option>
               <option value="2">2 adults</option>
               <option value="3">3 adults</option>
               <option value="4">4 adults</option>
               <option value="5">5 adults</option>
               <option value="6">6 adults</option>
            </select>
         </div>
         <div class="box">
            <p>childs <span>*</span></p>
            <select name="childs" class="input" id="res_childs" required onchange="suggestReservationRooms()">
               <option value="0" selected>0 child</option>
               <option value="1">1 child</option>
               <option value="2">2 childs</option>
               <option value="3">3 childs</option>
               <option value="4">4 childs</option>
               <option value="5">5 childs</option>
               <option value="6">6 childs</option>
            </select>
         </div>
         <div class="box">
            <p>total price <span>*</span></p>
            <input type="text" name="total_price" class="input" id="reservation_price" readonly value="$0.00">
         </div>
      </div>
      <input type="submit" value="book now" name="book" class="btn">
   </form>
</section>

<!-- reservation section ends -->

<!-- gallery section starts  -->

<section class="gallery" id="gallery">

   <div class="swiper gallery-slider">
      <div class="swiper-wrapper">
         <img src="images/gallery-img-1.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-img-2.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-3.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-4.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-img-5.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-6.webp" class="swiper-slide" alt="">
      </div>
      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- gallery section ends -->

<!-- contact section starts  -->

<section class="contact" id="contact">

   <div class="row">

      <form action="" method="post">
         <h3>send us message</h3>
         <input type="text" name="name" required maxlength="50" placeholder="enter your name" class="box">
         <input type="email" name="email" required maxlength="50" placeholder="enter your email" class="box">
         <input type="number" name="number" required maxlength="10" min="0" max="9999999999" placeholder="enter your number" class="box">
         <textarea name="message" class="box" required maxlength="1000" placeholder="enter your message" cols="30" rows="10"></textarea>
         <input type="submit" value="send message" name="send" class="btn">
      </form>

      <div class="faq">
         <h3 class="title">Frequently Asked Questions</h3>
         
         <div class="box active">
            <h3>How to cancel?</h3>
            <p>You can cancel your booking by heading over to my bookings and clicking on the cancel button.</p>
         </div>
         
         <div class="box">
            <h3>Is there any vacancy?</h3>
            <p>Vacancy is subject to availability. We recommend checking on availability by simply clicking on check availability.</p>
         </div>
         
         <div class="box">
            <h3>What are payment methods?</h3>
            <p>We accept major credit and debit cards, mobile money (such as M-Pesa), and secure online payments. For special arrangements, please contact our front desk.</p>
         </div>
         
         <div class="box">
            <h3>How to claim coupon codes?</h3>
            <p>To claim a coupon code, enter it during the checkout process in the 'Promo Code' field. Valid discounts will be automatically applied to your total.</p>
         </div>
         
         <div class="box">
            <h3>What are the age requirements?</h3>
            <p>Guests of all ages are welcome. However, guests under 18 must be accompanied by an adult. Age-specific policies may apply for certain activities or accommodations.</p>
         </div>
      </div>

   </div>

</section>

<!-- contact section ends -->

<!-- reviews section starts  -->

<section class="reviews" id="reviews">

   <div class="swiper reviews-slider">

      <div class="swiper-wrapper">
         <div class="swiper-slide box">
            <img src="images/pic-1.jpg" alt="">
            <h3>Jane Wambui</h3>
            <p>Absolutely loved the beachfront view and the delicious Swahili cuisine! The staff were incredibly friendly and helpful throughout our stay.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-2.jpg" alt="">
            <h3>Michael Otieno</h3>
            <p>The pool area was clean and relaxing, with a great ocean breeze. Perfect for a quiet afternoon with a cold drink in hand!</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-3.jpg" alt="">
            <h3>Fatma Yusuf</h3>
            <p>The outdoor dining experience was magical. We had dinner under the stars with the sound of waves in the background—simply unforgettable.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-4.jpg" alt="">
            <h3>Kevin Muli</h3>
            <p>Very impressed with the resort’s cleanliness and décor. It truly captured the beauty and culture of the Kenyan coast.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-5.jpg" alt="">
            <h3>Susan Achieng</h3>
            <p>My kids had a blast at the pool while I relaxed with a book. The resort is great for families looking for comfort and fun.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-6.jpg" alt="">
            <h3>Daniel Mwangi</h3>
            <p>Everything from the booking process to checkout was seamless. Highly recommend for anyone visiting Mombasa for a coastal getaway.</p>
         </div>
      </div>

      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- reviews section ends  -->

<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<script>
function calculateNights(checkIn, checkOut) {
   if (!checkIn || !checkOut || isNaN(checkIn.getTime()) || isNaN(checkOut.getTime())) {
      console.log('Invalid or missing dates:', { checkIn, checkOut });
      return 0;
   }
   if (checkOut <= checkIn) {
      console.log('Check-out date is not after check-in date:', { checkIn, checkOut });
      return 0;
   }
   const timeDiff = checkOut - checkIn;
   const nights = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
   console.log('Calculated nights:', nights);
   return nights;
}

function suggestRooms() {
   const adultsInput = document.getElementById('adults');
   const childsInput = document.getElementById('childs');
   const roomsSelect = document.getElementById('rooms');

   if (!adultsInput || !childsInput || !roomsSelect) {
      console.error('Missing input elements for suggestRooms:', { adultsInput, childsInput, roomsSelect });
      return;
   }

   const adults = parseInt(adultsInput.value) || 0;
   const childs = parseInt(childsInput.value) || 0;

   const roomsForAdults = Math.ceil(adults / 2);
   const roomsForChildren = Math.ceil(childs / 1);
   const suggestedRooms = Math.max(roomsForAdults, roomsForChildren);

   console.log('Suggesting rooms:', { adults, childs, suggestedRooms });

   const options = roomsSelect.options;
   for (let i = 0; i < options.length; i++) {
      if (parseInt(options[i].value) === suggestedRooms) {
         roomsSelect.selectedIndex = i;
         break;
      }
   }
   updateAvailabilityPrice();
}

function updateAvailabilityPrice() {
   const checkInInput = document.getElementById('check_in');
   const checkOutInput = document.getElementById('check_out');
   const roomsInput = document.getElementById('rooms');
   const priceInput = document.getElementById('availability_price');

   if (!checkInInput || !checkOutInput || !roomsInput || !priceInput) {
      console.error('Missing input elements for updateAvailabilityPrice:', { checkInInput, checkOutInput, roomsInput, priceInput });
      return;
   }

   const checkIn = new Date(checkInInput.value);
   const checkOut = new Date(checkOutInput.value);
   const rooms = parseInt(roomsInput.value) || 1;
   const pricePerRoomPerNight = 50; // $50 per room per night

   const nights = calculateNights(checkIn, checkOut);
   const totalPrice = rooms * nights * pricePerRoomPerNight;

   console.log('Updating availability price:', { rooms, nights, totalPrice });

   priceInput.value = `$${totalPrice.toFixed(2)}`;
}

function suggestReservationRooms() {
   const adultsInput = document.getElementById('res_adults');
   const childsInput = document.getElementById('res_childs');
   const roomsSelect = document.getElementById('res_rooms');

   if (!adultsInput || !childsInput || !roomsSelect) {
      console.error('Missing input elements for suggestReservationRooms:', { adultsInput, childsInput, roomsSelect });
      return;
   }

   const adults = parseInt(adultsInput.value) || 0;
   const childs = parseInt(childsInput.value) || 0;

   const roomsForAdults = Math.ceil(adults / 2);
   const roomsForChildren = Math.ceil(childs / 1);
   const suggestedRooms = Math.max(roomsForAdults, roomsForChildren);

   console.log('Suggesting reservation rooms:', { adults, childs, suggestedRooms });

   const options = roomsSelect.options;
   for (let i = 0; i < options.length; i++) {
      if (parseInt(options[i].value) === suggestedRooms) {
         roomsSelect.selectedIndex = i;
         break;
      }
   }
   updateReservationPrice();
}

function updateReservationPrice() {
   const checkInInput = document.getElementById('res_check_in');
   const checkOutInput = document.getElementById('res_check_out');
   const roomsInput = document.getElementById('res_rooms');
   const priceInput = document.getElementById('reservation_price');

   if (!checkInInput || !checkOutInput || !roomsInput || !priceInput) {
      console.error('Missing input elements for updateReservationPrice:', { checkInInput, checkOutInput, roomsInput, priceInput });
      return;
   }

   const checkIn = new Date(checkInInput.value);
   const checkOut = new Date(checkOutInput.value);
   const rooms = parseInt(roomsInput.value) || 1;
   const pricePerRoomPerNight = 50; // $50 per room per night

   const nights = calculateNights(checkIn, checkOut);
   const totalPrice = rooms * nights * pricePerRoomPerNight;

   console.log('Updating reservation price:', { rooms, nights, totalPrice });

   priceInput.value = `$${totalPrice.toFixed(2)}`;
}

// Initialize when DOM is fully loaded
document.addEventListener('DOMContentLoaded', () => {
   const requiredIds = ['check_in', 'check_out', 'rooms', 'adults', 'childs', 'availability_price', 'res_check_in', 'res_check_out', 'res_rooms', 'res_adults', 'res_childs', 'reservation_price'];
   for (const id of requiredIds) {
      if (!document.getElementById(id)) {
         console.error(`Element with ID "${id}" not found in DOM`);
      }
   }

   const checkInInput = document.getElementById('check_in');
   const checkOutInput = document.getElementById('check_out');
   const roomsInput = document.getElementById('rooms');
   const adultsInput = document.getElementById('adults');
   const childsInput = document.getElementById('childs');
   const resCheckInInput = document.getElementById('res_check_in');
   const resCheckOutInput = document.getElementById('res_check_out');
   const resRoomsInput = document.getElementById('res_rooms');
   const resAdultsInput = document.getElementById('res_adults');
   const resChildsInput = document.getElementById('res_childs');

   if (checkInInput) checkInInput.addEventListener('change', updateAvailabilityPrice);
   if (checkOutInput) checkOutInput.addEventListener('change', updateAvailabilityPrice);
   if (roomsInput) roomsInput.addEventListener('change', updateAvailabilityPrice);
   if (adultsInput) adultsInput.addEventListener('change', suggestRooms);
   if (childsInput) childsInput.addEventListener('change', suggestRooms);
   if (resCheckInInput) resCheckInInput.addEventListener('change', updateReservationPrice);
   if (resCheckOutInput) resCheckOutInput.addEventListener('change', updateReservationPrice);
   if (resRoomsInput) resRoomsInput.addEventListener('change', updateReservationPrice);
   if (resAdultsInput) resAdultsInput.addEventListener('change', suggestReservationRooms);
   if (resChildsInput) resChildsInput.addEventListener('change', suggestReservationRooms);

   console.log('Initializing price updates');
   updateAvailabilityPrice();
   updateReservationPrice();
});
</script>

<?php include 'components/message.php'; ?>

</body>
</html>