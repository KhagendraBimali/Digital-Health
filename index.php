<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
<link rel="stylesheet" href="style.css">
<script src="script.js"></script>
</head>
<body>

    
    <?php include "header.html";?>
    <div class="wrap">
        
        <div id="slider">
            <div class="slide slide1">
                <div class="slide-content">
                </div>
            </div>
            <div class="slide slide2">
                <div class="slide-content">
                </div>
            </div>
            <div class="slide slide3">
                <div class="slide-content">
                </div>
            </div>
        </div>
    </div>
    <!-- Image Slider Section -->
    <div class="carousel-container">
      <div class="carousel-track" id="carouselTrack">
        <div class="carousel-slide"><img src="pics/ward.webp" alt="Slide 1"></div>
        <div class="carousel-slide"><img src="pics/images1.jpg" alt="Slide 2"></div>
        <div class="carousel-slide"><img src="pics/images2.jpg" alt="Slide 3"></div>
        <div class="carousel-slide"><img src="pics/images3.jpg" alt="Slide 4"></div>
        <div class="carousel-slide"><img src="pics/ward.webp" alt="Slide 1 Clone"></div>
      </div>
    </div>
    <script>
    // Cyclic slider animation
    const track = document.getElementById('carouselTrack');
    const slides = document.querySelectorAll('.carousel-slide');
    let currentIndex = 0;
    const slideCount = slides.length;
    function goToSlide(index) {
      track.style.transition = 'transform 0.7s cubic-bezier(0.77,0,0.175,1)';
      track.style.transform = `translateX(-${index * 100}vw)`;
    }
    function nextSlide() {
      currentIndex++;
      goToSlide(currentIndex);
      if (currentIndex === slideCount - 1) {
        setTimeout(() => {
          track.style.transition = 'none';
          currentIndex = 0;
          track.style.transform = `translateX(0vw)`;
        }, 700);
      }
    }
    setInterval(nextSlide, 3000);
    </script>
    <h1 id="logins">Logins</h1>
    <div class="login-container">
        <div class="image-box">
            <img src="pic/logo.png" alt="Hospital Image">
        </div>
        <div class="login-box">
            <h1>Welcome to the Digital Health</h1>
            <p>Please select your role to log in:</p>
            <button class="login-button" onclick="window.location.href = 'patient_login.php'">Patient Login</button>
            <button class="login-button" onclick="window.location.href = '/project/doctor/doctor_login.php'">Doctor Login</button>
            <button class="login-button" onclick="window.location.href = '/project/hospital/admin_login.php'">Hospital Login</button>
        </div>
    </div>
  <h1 id="about">About Us</h1> 
   <div class="about">
    <div class="aboutpic">
        <img src="pic/logo.png" alt="">
        </div>
        <div class="abouttext">
            <p>Digital health is the pioneer online healthcare
                service provider in eastern Nepal where patients
                can consult certified medical personnel and get 
                additional health related services along with 
                other information related to health online. We're 
                transforming the way patients connect 
                with doctors and hospitals, making healthcare 
                more accessible and efficient. From easy 
                appointment scheduling to secure medical 
                record management, we're here to simplify your 
                healthcare journey. Join us in shaping a healthier 
                tomorrow, today.</p>
            </div>
    </div>
    <style>
        .error {
            color: red;
        }
    </style>
    <script>
        function validateForm() {
            let name = document.getElementById('name').value;
            let email = document.getElementById('email').value;
            let mobile = document.getElementById('mobile').value;
            let message = document.getElementById('messages').value;

            let nameError = document.getElementById('nameError');
            let emailError = document.getElementById('emailError');
            let mobileError = document.getElementById('mobileError');
            let messageError = document.getElementById('messageError');

            nameError.textContent = '';
            emailError.textContent = '';
            mobileError.textContent = '';
            messageError.textContent = '';

            let isValid = true;

            if (name === "") {
                nameError.textContent = 'Name cannot be blank';
                isValid = false;
            }

            if (!email.endsWith("@gmail.com")) {
                emailError.textContent = 'Email must end with @gmail.com';
                isValid = false;
            }

            if (mobile.length !== 10 || isNaN(mobile)) {
                mobileError.textContent = 'Mobile number must be 10 digits';
                isValid = false;
            }

            if (message === "") {
                messageError.textContent = 'Message cannot be blank';
                isValid = false;
            }

            return isValid;
        }
    </script>
</head>
<body>
    <h1 id="contact">Contact Form</h1>
    <form action="process.php" method="post" onsubmit="return validateForm()">
        <label for="name">Enter Name:</label>
        <input type="text" id="name" name="name" >
        <span class="error" id="nameError"></span>
        <br><br>
        
        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" >
        <span class="error" id="emailError"></span>
        <br><br>
        
        <label for="mobile">Mobile Number:</label>
        <input type="tel" id="mobile" name="mobile" >
        <span class="error" id="mobileError"></span>
        <br><br>
        
        <label for="messages">Enter Message:</label>
        <textarea id="messages" name="messages" rows="4" ></textarea>
        <span class="error" id="messageError"></span>
        <br><br>
        
        <input type="submit" value="Send Message">
    </form>
    <?php include "footer.html";?>
    
</body>
</html>

