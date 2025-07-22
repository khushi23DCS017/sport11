<?php
require_once __DIR__ . '/config/database.php';
$ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$visit_time = date('Y-m-d H:i:s');

// Device type detection
function getDeviceType($user_agent) {
    $user_agent = strtolower($user_agent);
    if (preg_match('/tablet|ipad|playbook|silk|kindle|android(?!.*mobile)/i', $user_agent)) {
        return 'Tablet';
    } elseif (preg_match('/mobile|iphone|ipod|android.*mobile|blackberry|opera mini|windows phone|iemobile/i', $user_agent)) {
        return 'Mobile';
    } else {
        return 'Desktop';
    }
}
$device_type = getDeviceType($user_agent);

try {
    $stmt = $pdo->prepare("INSERT INTO visit_logs (visit_time, ip_address, user_agent, device_type) VALUES (?, ?, ?, ?)");
    $stmt->execute([$visit_time, $ip, $user_agent, $device_type]);
} catch (Exception $e) {
    // Optionally log error
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sport11 - India's Leading Fantasy Sports Platform">
    <meta name="author" content="Sport11">
    <link rel="icon" href="images/logo.png">
    <title>Sports11</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <!-- Owl Carousel CSS -->
    <link href="css/owl.carousel.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" rel="stylesheet" type="text/css">
    
    <!-- Font Awesome -->
    <link href="fontawesome/css/all.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    
    <!-- Dark Mode Stylesheet -->
    <link href="css/dark-mode.css" rel="stylesheet" type="text/css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        /* Basic styling for the dark mode toggle - adjust as needed */
        .dark-mode-toggle {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 1000;
            cursor: pointer;
            font-size: 24px;
            color: white; /* Default color in light mode header */
        }

        /* Adjust toggle color in dark mode */
        body.dark-mode .dark-mode-toggle {
             color: #ccc;
        }

         /* Styles to transition colors smoothly */
        body {
            transition: background-color 0.5s ease, color 0.5s ease;
        }
        body * {
             transition: color 0.5s ease, background-color 0.5s ease;
        }

    </style>

</head>

<body class="light-mode"> <!-- Add a default class -->

    <!-- Dark Mode Toggle Button -->
    <div class="dark-mode-toggle" id="darkModeToggle">
        <i class="fas fa-moon"></i> <!-- Moon icon for light mode -->
    </div>


    <header>
        <div class="header_main" style="background-image: url(images/banner1.jpg)">
            <div class="logo_div">
                <a href="index.html"><img class="img-fluid" src="images/logo.png" alt=""></a>
            </div>
<!--
            <div class="banner_txt">
                <h1>JAZBAA HAI JEET KA</h1>
                <p>Make team of experts on a platform to win</p>
            </div>
-->
            <div class="banner_app_d">
                <a class="btn white" href="https://play.google.com/store/apps/details?id=com.sport11.app" target="_blank">Download App</a>
            </div>
        </div>
    </header>

    <section class="app_guidence padding">
        <div class="container">
            <div class="section_title">
                <h2>It's easy to start playing on Sport11</h2>
            </div>
            <div class="right_steps_box">
                <div class="row">
                    <div class="col-md-4 col-sm-12 r_s_boxes">
                        <div class="r_s_box">
                            <span class="step_number">1</span>
                            <div class="title_img_box">
                                <div class="winning_step_img">
                                    <img class="img-fluid" src="images/install01.png" alt="">
                                </div>
                                <div>
                                    <h5>Select A Match</h5>
                                </div>
                            </div>
                            <div class="step_title">Choose an upcoming match that you want to play</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 r_s_boxes">
                        <div class="r_s_box">
                            <span class="step_number">2</span>
                            <div class="title_img_box">
                                <div class="winning_step_img">
                                    <img class="img-fluid" src="images/install02.png" alt="">
                                </div>
                                <div>
                                    <h5>Create Team</h5>
                                </div>
                            </div>
                            <div class="step_title">Use your skills to pick the right players</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 r_s_boxes">
                        <div class="r_s_box">
                            <span class="step_number">3</span>
                            <div class="title_img_box">
                                <div class="winning_step_img">
                                    <img class="img-fluid" src="images/install03.png" alt="">
                                </div>
                                <div>
                                    <h5>Join Contests</h5>
                                </div>
                            </div>
                            <div class="step_title">Choose between different contests and win money</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="add_download_sec bg_gray padding">
        <div class="container">
            <div class="section_title">
                <h2>DOWNLOAD SPORT11 APP TO ACHIEVE REMARKABLE REWARDS</h2>
            </div>
            <div class="row align-items-center">
                <div class="col-md-6 a_d_left mb-3">
                    <img class="img-fluid" src="images/mobileScren.png" alt="">
                </div>
                <div class="col-md-6 a_d_right text-center">
                    <p>Be a cricket champion using your skills by playing sport11 yourself with your fingertips and win fabulous cash prizes.</p>
                    <span>Allow Outside Google Playstore App Download.</span>
                    <div class="app_type">
                        <a href="https://play.google.com/store/apps/details?id=com.sport11.app" target="_blank">
                            <img src="images/android.svg" alt="">
                        </a>
                        <a href="https://apps.apple.com/in/app/sport11/id1456459632" target="_blank">
                            <img src="images/ios.svg" alt="">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="review_rating padding">
        <div class="container">
            <div class="section_title">
                <h2>Reviews & Rating</h2>
            </div>
            <div id="review" class="owl-carousel review">
                <div class="item">
                    <div class="review_box">
                        <div class="media">
                            <img class="align-self-start mr-3" src="images/user.png" alt="">
                            <div class="media-body">
                                <h4>Sonu Shrivastav</h4>
                                <p>I have been playing Sport11 since 2017, and one thing I can say for sure is practice matters if you want to win big. I take a lot of time to study teams and players, and this time my hard work helped me win ₹25 Lakh.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="review_box">
                        <div class="media">
                            <img class="align-self-start mr-3" src="images/user.png" alt="">
                            <div class="media-body">
                                <h4>Samir Pinjari</h4>
                                <p>Cricket is much more than a passion for me. Cricket is life. That's why I like playing on Sport11 so much. Thanks to its contests, I'm not just using my knowledge of cricket every day but also earning from it.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="review_box">
                        <div class="media">
                            <img class="align-self-start mr-3" src="images/user.png" alt="">
                            <div class="media-body">
                                <h4>Indrajeet Pramanik</h4>
                                <p>As a die-hard cricket fan, I practically kept track of all sorts of news on players, teams and tournaments. But when I got introduced to Sport11, I got an opportunity to put my knowledge to good use.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="contact_section padding">
        <div class="container">
            <div class="section_title">
                <h2>Contact Us</h2>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <form id="contactForm" class="contact-form">
                        <div class="mb-3">
                            <input type="text" class="form-control" name="name" placeholder="Your Name" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" name="message" rows="5" placeholder="Your Message" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="map-container">
                        <div id="map" style="height: 400px; width: 100%; border-radius: 8px;"></div>
                    </div>
                    <div class="newsletter-box mt-4">
                        <h4>Subscribe to Our Newsletter</h4>
                        <p>Stay updated with the latest news and offers!</p>
                        <form id="newsletterForm" class="newsletter-form">
                            <div id="emailStep">
                                <div class="input-group mb-3">
                                    <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                                    <button class="btn btn-primary" type="submit">Subscribe</button>
                                </div>
                            </div>
                            <div id="otpStep" style="display: none;">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="otp" placeholder="Enter OTP">
                                    <button class="btn btn-primary" type="button" id="verifyOtpBtn">Verify OTP</button>
                                </div>
                            </div>
                        </form>
                        <div id="newsletterMessage" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="app-info-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <div class="app-info-heading">
                            <h5>App Information</h5>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-lg-3 text-center">
                        <div class="app-info-logo">
                            <img src="https://sport11.in/assets/image/sport11_logo.png" class="img-fluid" alt="">
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-9">
                        <div class="row app-info-">
                            <div class="col-sm-4 col-md-6 col-lg-4">
                                <div class="info-main">
                                    <h6>Updated</h6>
                                    <h5>Apr 08, 2021</h5>
                                </div>
                                <div class="info-main">
                                    <h6>Size</h6>
                                    <h5>10.30 MB</h5>
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-6 col-lg-4">
                                <div class="info-main">
                                    <h6>Current Android Version</h6>
                                    <h5>1.0.29</h5>
                                </div>
                                <div class="info-main">
                                    <h6>Requires Android</h6>
                                    <h5>4.4 Required</h5>
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-6 col-lg-4">
                                <div class="info-main">
                                    <h6>Current IOS Version</h6>
                                    <h5>1.20</h5>
                                </div>
                                <div class="info-main">
                                    <h6>Downloads</h6>
                                    <h5>8.0 Lac + Users</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ftr-copyright-rw">
            <div class="container">
                <p>Copyright © 2025 <a href="https://sport11.in/">Sport11 </a>. Power by DNK SPORT LLP All Rights Reserved.</p>
            </div>
        </div>
    </footer>


    <div class="sticky_bottom_btn">
        <a class="btn blue" href="https://play.google.com/store/apps/details?id=com.sport11.app" target="_blank">Download App</a>
    </div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <!-- <script src="js/custom.js"></script> -->

    <script>
    $(document).ready(function () {
    // Scroll handling for top navigation
    $(window).scroll(function () {
        var scroll = $(window).scrollTop();
        if (scroll >= 100) {
            $("body").addClass("top_none");
        } else {
            $("body").removeClass("top_none");
        }
    });

    // Scroll handling for sticky button
    $(window).scroll(function () {
        var scroll = $(window).scrollTop();
        if (scroll >= 300) {
            $("body").addClass("show_btn");
        } else {
            $("body").removeClass("show_btn");
        }
    });

    // Initialize Owl Carousel
    var reviewCarousel = $('#review');
    if (reviewCarousel.length) {
        reviewCarousel.owlCarousel({
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            loop: true,
            margin: 20,
            nav: true,
            dots: true,
            navText: ["<i class='fas fa-chevron-left'></i>", "<i class='fas fa-chevron-right'></i>"],
            responsive: {
                0: {
                    items: 1,
                    nav: true,
                    dots: true
                },
                576: {
                    items: 1,
                    nav: true,
                    dots: true
                },
                768: {
                    items: 2,
                    nav: true,
                    dots: true
                },
                992: {
                    items: 3,
                    nav: true,
                    dots: true
                }
            }
        });
    }

    // Add error handling for images
    $('img').on('error', function() {
        $(this).attr('src', 'images/logo.png');
    });
});

    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const name = this.querySelector('input[name="name"]').value.trim();
        const email = this.querySelector('input[name="email"]').value.trim();
        const message = this.querySelector('textarea[name="message"]').value.trim();

        if (name === '' || email === '' || message === '') {
            alert('Please fill in all fields.');
            return;
        }

        const formData = new FormData(this);
        
        fetch('contact.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                console.error('HTTP error!', response.status, response.statusText);
                return response.text().then(text => {
                    console.error('Response body on error:', text);
                    throw new Error('HTTP error ' + response.status);
                });
            }
            return response.json();
        })
        .then(data => {
            alert(data.message);
            if (data.success) {
                this.reset();
            }
        })
        .catch(error => {
            alert('Error sending message. Please try again.');
            console.error('Fetch caught an error:', error);
        });
    });

    const newsletterForm = document.getElementById('newsletterForm');
    const emailStep = document.getElementById('emailStep');
    const otpStep = document.getElementById('otpStep');
    const newsletterMessageDiv = document.getElementById('newsletterMessage');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');

    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const emailInput = this.querySelector('#emailStep input[name="email"]');
            const email = emailInput.value.trim();

            if (email === '' || !emailInput.checkValidity()) {
                 newsletterMessageDiv.innerHTML = '<div class="alert alert-danger">Please enter a valid email address.</div>';
                 return;
            }

            newsletterMessageDiv.innerHTML = '';
            const formData = new FormData();
            formData.append('email', email);

            fetch('send_otp.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response from send_otp.php:', data);
                if (data.success) {
                    if (emailStep && otpStep) {
                        emailStep.style.display = 'none';
                        otpStep.style.display = 'block';
                    }
                    newsletterMessageDiv.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                } else {
                    newsletterMessageDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                }
            })
            .catch(error => {
                console.error('Error in send OTP fetch:', error);
                newsletterMessageDiv.innerHTML = '<div class="alert alert-danger">Error sending OTP. Please try again.</div>';
            });
        });
    }

    if (verifyOtpBtn) {
        verifyOtpBtn.addEventListener('click', function() {
            const form = document.getElementById('newsletterForm');
            const emailInput = form.querySelector('#emailStep input[name="email"]');
            const email = emailInput ? emailInput.value.trim() : '';
            const otpInput = form.querySelector('#otpStep input[name="otp"]');
            const otp = otpInput ? otpInput.value.trim() : '';

            if (otp === '') {
                newsletterMessageDiv.innerHTML = '<div class="alert alert-danger">Please enter the OTP.</div>';
                return;
            }

            newsletterMessageDiv.innerHTML = '';
            const formData = new FormData();
            formData.append('email', email);
            formData.append('otp', otp);

            fetch('verify_otp.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    newsletterMessageDiv.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                    if (form && emailStep && otpStep) {
                        form.reset();
                        emailStep.style.display = 'block';
                        otpStep.style.display = 'none';
                    }
                } else {
                    newsletterMessageDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                }
            })
            .catch(error => {
                newsletterMessageDiv.innerHTML = '<div class="alert alert-danger">Error verifying OTP. Please try again.</div>';
            });
        });
    }

    const darkModeToggle = document.getElementById('darkModeToggle');
    const body = document.body;
    const darkModeKey = 'sport11DarkMode';

    function setTheme(mode) {
        if (mode === 'dark') {
            body.classList.add('dark-mode');
            body.classList.remove('light-mode');
            localStorage.setItem(darkModeKey, 'dark');
            darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        } else {
            body.classList.remove('dark-mode');
            body.classList.add('light-mode');
            localStorage.setItem(darkModeKey, 'light');
            darkModeToggle.innerHTML = '<i class="fas fa-moon"></i>';
        }
    }

    const savedMode = localStorage.getItem(darkModeKey);
    if (savedMode) {
        setTheme(savedMode);
    } else {
        setTheme('light');
    }

    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', () => {
            if (body.classList.contains('light-mode')) {
                setTheme('dark');
            } else {
                setTheme('light');
            }
        });
    }
    </script>

    <!-- Google Maps Script (Commented out due to API key issues) -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBh3CcaMdFXvSeObF5D6ldjTgTP0qN1LTo&callback=initMap" async defer></script>
    <script>
    function initMap() {
        const location = { lat: 21.236601, lng: 72.875483 };
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 15,
            center: location,
            styles: [
                {
                    "featureType": "all",
                    "elementType": "geometry",
                    "stylers": [{"color": "#242f3e"}]
                },
                {
                    "featureType": "all",
                    "elementType": "labels.text.stroke",
                    "stylers": [{"lightness": -80}]
                },
                {
                    "featureType": "administrative",
                    "elementType": "labels.text.fill",
                    "stylers": [{"color": "#746855"}]
                },
                {
                    "featureType": "administrative.locality",
                    "elementType": "labels.text.fill",
                    "stylers": [{"color": "#d59563"}]
                },
                {
                    "featureType": "poi",
                    "elementType": "labels.text.fill",
                    "stylers": [{"color": "#d59563"}]
                },
                {
                    "featureType": "poi.park",
                    "elementType": "geometry",
                    "stylers": [{"color": "#263c3f"}]
                },
                {
                    "featureType": "poi.park",
                    "elementType": "labels.text.fill",
                    "stylers": [{"color": "#6b9a76"}]
                },
                {
                    "featureType": "road",
                    "elementType": "geometry",
                    "stylers": [{"color": "#38414e"}]
                },
                {
                    "featureType": "road",
                    "elementType": "geometry.stroke",
                    "stylers": [{"color": "#212a37"}]
                },
                {
                    "featureType": "road",
                    "elementType": "labels.text.fill",
                    "stylers": [{"color": "#9ca5b3"}]
                },
                {
                    "featureType": "road.highway",
                    "elementType": "geometry",
                    "stylers": [{"color": "#746855"}]
                },
                {
                    "featureType": "road.highway",
                    "elementType": "geometry.stroke",
                    "stylers": [{"color": "#1f2835"}]
                },
                {
                    "featureType": "road.highway",
                    "elementType": "labels.text.fill",
                    "stylers": [{"color": "#f3d19c"}]
                },
                {
                    "featureType": "transit",
                    "elementType": "geometry",
                    "stylers": [{"color": "#2f3948"}]
                },
                {
                    "featureType": "transit.station",
                    "elementType": "labels.text.fill",
                    "stylers": [{"color": "#d59563"}]
                },
                {
                    "featureType": "water",
                    "elementType": "geometry",
                    "stylers": [{"color": "#17263c"}]
                },
                {
                    "featureType": "water",
                    "elementType": "labels.text.fill",
                    "stylers": [{"color": "#515c6d"}]
                },
                {
                    "featureType": "water",
                    "elementType": "labels.text.stroke",
                    "stylers": [{"lightness": -20}]
                }
            ]
        });

        const marker = new google.maps.Marker({
            position: location,
            map: map,
            title: "DNK SPORT LLP",
            animation: google.maps.Animation.DROP
        });

        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div style="padding: 10px;">
                    <h5 style="margin: 0 0 5px 0;">DNK SPORT LLP</h5>
                    <p style="margin: 0;">305, Sunshine Complex Near Sudama Chowk, opp. CNG Pump, Mota Varrachha, Surat, Gujarat</p>
                </div>
            `
        });

        marker.addListener("click", () => {
            infoWindow.open(map, marker);
        });
    }
    </script>

</body>

</html>
