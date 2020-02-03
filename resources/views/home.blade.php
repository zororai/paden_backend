<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Paden Mobile App</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{url('/https://fonts.googleapis.com/css2?family=Heebo:wght@400;500&family=Jost:wght@500;600;700&display=swap')}}" rel="stylesheet"> 

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
<meta name="google-site-verification" content="-kiIU3D6xAy2VLk1ceoRujHu9-TqtCiHgbfDfHXNW1c" />
    <!-- Libraries Stylesheet -->
    <link href="{{ asset('css/animate.min.css')}}" rel="stylesheet">
    <link href="{{ asset('css/owl.carousel.min.css')}}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('css/bootstrap.min.css')}}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('css/style.css')}}" rel="stylesheet">
</head>

<body data-bs-spy="scroll" data-bs-target=".navbar" data-bs-offset="51">
    <div class="container-xxl bg-white p-0">



        <!-- Navbar & Hero Start -->
        <div class="container-xxl position-relative p-0" id="home">
            <nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0">
                <a href="" class="navbar-brand p-0">
                    <h1 class="m-0">Paden</h1>
                     
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav mx-auto py-0">
                        <a href="#home" class="nav-item nav-link active">Home</a>
                        <a href="{{ url('/about') }}" class="nav-item nav-link">About Landlords</a>
                        <a href="#contact" class="nav-item nav-link">Contact</a>
                    </div>
                    
                </div>
            </nav>

            <div class="container-xxl bg-primary hero-header">
                <div class="container px-lg-5">
                    <div class="row g-5">
                        <div class="col-lg-8 text-center text-lg-start">
                            <h1 class="text-white mb-4 animated slideInDown">Paden: The Revolutionary App That Helps You Choose Your Desired Student Accommodation</h1>
                            <p class="text-white pb-3 animated slideInDown">"Why landlord should Register
on the Paden Platform"</p>
                            <a href="" class="btn btn-primary-gradient py-sm-3 px-4 px-sm-5 rounded-pill me-3 animated slideInLeft">Read More</a>
                            <a href="" class="btn btn-secondary-gradient py-sm-3 px-4 px-sm-5 rounded-pill animated slideInRight">Contact Us</a>
                        </div>
                        <div class="col-lg-4 d-flex justify-content-center justify-content-lg-end wow fadeInUp" data-wow-delay="0.3s">
                            <div class="owl-carousel screenshot-carousel">
                                <img class="img-fluid" src="{{ asset('storage/img/screenshot-1.png') }}" alt="">
                                <img class="img-fluid" src="{{ asset('storage/img/screenshot-2.png') }}" alt="">
                                <img class="img-fluid" src="{{ asset('storage/img/screenshot-3.png') }}" alt="">
                                <img class="img-fluid" src="{{ asset('storage/img/screenshot-4.png') }}" alt="">
                                <img class="img-fluid" src="{{ asset('storage/img/screenshot-5.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Navbar & Hero End -->


        <!-- About Start -->
        <div class="container-xxl py-5" id="about">
            <div class="container py-5 px-lg-5">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                        <h5 class="text-primary-gradient fw-medium">About App</h5>
                        <h1 class="mb-4">Paden</h1>
                        <p class="mb-4">Paden is renting service mobile application that helps student to find the best accommodation off campus.This application will help students to find accommodation for both long term and short term purposes . It also help's student to search for there desired accommodation at the comfort of there home safe and sound .</p>
                   
                        <a href="" class="btn btn-primary-gradient py-sm-3 px-4 px-sm-5 rounded-pill mt-3">Read More</a>
                    </div>
                    <div class="col-lg-6">
                        <img class="img-fluid wow fadeInUp" data-wow-delay="0.5s"   src="{{ asset('storage/img/about.png') }}" >
                    </div>
                </div>
            </div>
        </div>
        <!-- About End -->

<!-- Universities List Start -->
<div class="container-xxl py-5" id="universities">
    <div class="container py-5">
        <h2 class="text-center mb-5">Universities Covered by Paden</h2>
        <div class="row">
            <!-- University 1 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset('storage/img/uz_logo.jpg') }}" alt="University of Zimbabwe" class="img-fluid mb-3" style="max-height: 100px;">
                        <h5 class="card-title">University of Zimbabwe</h5>
                        <p class="card-text">Located in Harare, it is one of the oldest universities in Zimbabwe.</p>
                    </div>
                </div>
            </div>
            <!-- University 2 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset('storage/img/nust_logo.jpg') }}" alt="National University of Science and Technology" class="img-fluid mb-3" style="max-height: 100px;">
                        <h5 class="card-title">National University of Science and Technology (NUST)</h5>
                        <p class="card-text">Located in Bulawayo, NUST is known for its focus on technology and innovation.</p>
                    </div>
                </div>
            </div>
            <!-- University 3 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset('storage/img/msu_logo.jpg') }}" alt="Midlands State University" class="img-fluid mb-3" style="max-height: 100px;">
                        <h5 class="card-title">Midlands State University</h5>
                        <p class="card-text">Located in Gweru, MSU offers a wide range of undergraduate and postgraduate programs.</p>
                    </div>
                </div>
            </div>
            <!-- University 4 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset('storage/img/cut_logo.jpg') }}" alt="Chinhoyi University of Technology" class="img-fluid mb-3" style="max-height: 100px;">
                        <h5 class="card-title">Chinhoyi University of Technology</h5>
                        <p class="card-text">Located in Chinhoyi, it specializes in technology, engineering, and business.</p>
                    </div>
                </div>
            </div>
            <!-- University 5 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset('storage/img/bindura_logo.jpg') }}" alt="Bindura University of Science Education" class="img-fluid mb-3" style="max-height: 100px;">
                        <h5 class="card-title">Bindura University of Science Education</h5>
                        <p class="card-text">Located in Bindura, it is known for its focus on science and education.</p>
                    </div>
                </div>
            </div>
            <!-- University 6 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset('storage/img/gzu_logo.png') }}" alt="Great Zimbabwe University" class="img-fluid mb-3" style="max-height: 100px;">
                        <h5 class="card-title">Great Zimbabwe University</h5>
                        <p class="card-text">Located in Masvingo, it is a comprehensive university offering a variety of courses.</p>
                    </div>
                </div>
            </div>
            <!-- University 7 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset('storage/img/africa_university_logo.png') }}" alt="Africa University" class="img-fluid mb-3" style="max-height: 100px;">
                        <h5 class="card-title">Africa University</h5>
                        <p class="card-text">Located in Mutare, Africa University provides various undergraduate and postgraduate programs.</p>
                    </div>
                </div>
            </div>
            <!-- University 8 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset('storage/img/harare_institute_of_technology_logo.jpg') }}" alt="Harare Institute of Technology" class="img-fluid mb-3" style="max-height: 100px;">
                        <h5 class="card-title">Harare Institute of Technology</h5>
                        <p class="card-text">Located in Harare, it focuses on applied sciences, engineering, and technology.</p>
                    </div>
                </div>
            </div>
            <!-- University 9 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset('storage/img/wua_logo.jpg') }}" alt="Women's University in Africa" class="img-fluid mb-3" style="max-height: 100px;">
                        <h5 class="card-title">Women's University in Africa</h5>
                        <p class="card-text">Located in Harare, WUA focuses on empowering women through higher education in various disciplines across Zimbabwe.</p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
<!-- Universities List End -->

        
        <!-- Features Start -->
        <div class="container-xxl py-5" id="feature">
            <div class="container py-5 px-lg-5">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h5 class="text-primary-gradient fw-medium">App Features</h5>
                    <h1 class="mb-5">Awesome Features</h1>
                </div>
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="feature-item bg-light rounded p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-gradient rounded-circle mb-4" style="width: 60px; height: 60px;">
                                <i class="fa fa-eye text-white fs-4"></i>
                            </div>
                            <h5 class="mb-3">High Resolution</h5>
                            <p class="m-0"></p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="feature-item bg-light rounded p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-secondary-gradient rounded-circle mb-4" style="width: 60px; height: 60px;">
                                <i class="fa fa-home text-white fs-4"></i>
                            </div>
                            <h5 class="mb-3">Propert Details</h5>
                            <p class="m-0"></p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="feature-item bg-light rounded p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-gradient rounded-circle mb-4" style="width: 60px; height: 60px;">
                                <i class="fa fa-map text-white fs-4"></i>
                            </div>
                            <h5 class="mb-3">Live Google map</h5>
                            <p class="m-0"></p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="feature-item bg-light rounded p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-secondary-gradient rounded-circle mb-4" style="width: 60px; height: 60px;">
                                <i class="fa fa-shield-alt text-white fs-4"></i>
                            </div>
                            <h5 class="mb-3">Fully Secured</h5>
                            <p class="m-0"></p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="feature-item bg-light rounded p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-gradient rounded-circle mb-4" style="width: 60px; height: 60px;">
                                <i class="fa fa-cloud text-white fs-4"></i>
                            </div>
                            <h5 class="mb-3">Cloud Storage</h5>
                            <p class="m-0"></p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="feature-item bg-light rounded p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-secondary-gradient rounded-circle mb-4" style="width: 60px; height: 60px;">
                                <i class="fa fa-mobile-alt text-white fs-4"></i>
                            </div>
                            <h5 class="mb-3">Fully Responsive</h5>
                            <p class="m-0"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Features End -->


        <!-- Screenshot Start -->
        <div class="container-xxl py-5">
            <div class="container py-5 px-lg-5">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-8 wow fadeInUp" data-wow-delay="0.1s">
                        <h5 class="text-primary-gradient fw-medium">Screenshot</h5>
                        <h1 class="mb-4">User Friendly interface And Very Easy To Use Real estate agent App</h1>
                        <p class="mb-4">We specialize in digital
                            marketing for your property so
                            that it can attract suitable
                            tenants, increase your online
                            presence, and engage your
                            target audience</p>
                       
                    </div>
                    <div class="col-lg-4 d-flex justify-content-center justify-content-lg-end wow fadeInUp" data-wow-delay="0.3s">
                        <div class="owl-carousel screenshot-carousel">
                          <img class="img-fluid" src="{{ asset('storage/img/screenshot-1.png') }}" alt="">
                          <img class="img-fluid" src="{{ asset('storage/img/screenshot-2.png') }}" alt="">
                          <img class="img-fluid" src="{{ asset('storage/img/screenshot-3.png') }}" alt="">
                          <img class="img-fluid" src="{{ asset('storage/img/screenshot-4.png') }}" alt="">
                          <img class="img-fluid" src="{{ asset('storage/img/screenshot-5.png') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Screenshot End -->


        <!-- Process Start -->
        <div class="container-xxl py-5">
            <div class="container py-5 px-lg-5">
                <div class="text-center pb-4 wow fadeInUp" data-wow-delay="0.1s">
                    <h5 class="text-primary-gradient fw-medium">How It Works</h5>
                    <h1 class="mb-5">3 Easy Steps</h1>
                </div>
                <div class="row gy-5 gx-4 justify-content-center">
                    <div class="col-lg-4 col-sm-6 text-center pt-4 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="position-relative bg-light rounded pt-5 pb-4 px-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-gradient rounded-circle position-absolute top-0 start-50 translate-middle shadow" style="width: 100px; height: 100px;">
                                <i class="fa fa-cog fa-3x text-white"></i>
                            </div>
                            <h5 class="mt-4 mb-3">Install the App</h5>
                            <p class="mb-0">Go to your device's app store (e.g., Google Play Store, Apple App Store) and download the application</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 text-center pt-4 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="position-relative bg-light rounded pt-5 pb-4 px-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-secondary-gradient rounded-circle position-absolute top-0 start-50 translate-middle shadow" style="width: 100px; height: 100px;">
                                <i class="fa fa-address-card fa-3x text-white"></i>
                            </div>
                            <h5 class="mt-4 mb-3">Setup Your Profile</h5>
                            <p class="mb-0">Look for an option to create a new account. It may be labeled as "Sign Up," "Register," or "Create Account" in the application.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 text-center pt-4 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="position-relative bg-light rounded pt-5 pb-4 px-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-gradient rounded-circle position-absolute top-0 start-50 translate-middle shadow" style="width: 100px; height: 100px;">
                                <i class="fa fa-check fa-3x text-white"></i>
                            </div>
                            <h5 class="mt-4 mb-3">Enjoy The Features</h5>
                            <p class="mb-0">Enjoy  our Paden application, including advanced property search, customizable alerts, and detailed market insights </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Process Start -->


        <!-- Download Start -->
        <div class="container-xxl py-5">
            <div class="container py-5 px-lg-5">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6">
                        <img class="img-fluid wow fadeInUp" data-wow-delay="0.1s" src="{{ asset('storage/img/about.png')}}">
                    </div>
                    <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                        <h5 class="text-primary-gradient fw-medium">Download</h5>
                        <h1 class="mb-4">Download The Latest Version Of Our App</h1>
                       <div class="row g-4">
                            <div class="col-sm-6 wow fadeIn" data-wow-delay="0.5s">
                                <a href="" class="d-flex bg-primary-gradient rounded py-3 px-4">
                                    <i class="fab fa-apple fa-3x text-white flex-shrink-0"></i>
                                    <div class="ms-3">
                                        <p class="text-white mb-0">Available On</p>
                                        <h5 class="text-white mb-0">App Store</h5>
                                    </div>
                                </a>
                            </div>
                            <div class="col-sm-6 wow fadeIn" data-wow-delay="0.7s">
                                <a href="https://play.google.com/store/apps/details?id=com.mycompany.paden2&pcampaignid=web_share" class="d-flex bg-secondary-gradient rounded py-3 px-4">
                                    <i class="fab fa-android fa-3x text-white flex-shrink-0"></i>
                                    <div class="ms-3">
                                        <p class="text-white mb-0">Available On</p>
                                        <h5 class="text-white mb-0">Play Store</h5>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Download End -->


   


        <!-- Testimonial Start -->
    
        <!-- Testimonial End -->


        <!-- Contact Start -->
        <div class="container-xxl py-5" id="contact">
            <div class="container py-5 px-lg-5">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h5 class="text-primary-gradient fw-medium">Contact Us</h5>
                    <h1 class="mb-5">Get In Touch!</h1>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <div class="wow fadeInUp" data-wow-delay="0.3s">
                           <form>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="name" placeholder="Your Name">
                                            <label for="name">Your Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="email" placeholder="Your Email">
                                            <label for="email">Your Email</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="subject" placeholder="Subject">
                                            <label for="subject">Subject</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control" placeholder="Leave a message here" id="message" style="height: 150px"></textarea>
                                            <label for="message">Message</label>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button class="btn btn-primary-gradient rounded-pill py-3 px-5" type="submit">Send Message</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact End -->
        

        <!-- Footer Start -->
        <div class="container-fluid bg-primary text-light footer wow fadeIn" data-wow-delay="0.1s">
            <div class="container py-5 px-lg-5">
                <div class="row g-5">
                    <div class="col-md-6 col-lg-3">
                        <h4 class="text-white mb-4">Contact details</h4>
            
                        <p><i class="fa fa-envelope me-3"></i>infor@paden.co.zw</p>
                        <div class="d-flex pt-2">
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-instagram"></i></a>
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <h4 class="text-white mb-4">Quick Link</h4>
                        <a class="btn btn-link" href="">About Us</a>
                        <a class="btn btn-link" href="">Contact Us</a>
                        <a class="btn btn-link" href="">Privacy Policy</a>
                        <a class="btn btn-link" href="">Terms & Condition</a>
                        <a class="btn btn-link" href="">Career</a>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <h4 class="text-white mb-4">Popular Link</h4>
                        <a class="btn btn-link" href="">About Us</a>
                        <a class="btn btn-link" href="">Contact Us</a>
                        <a class="btn btn-link" href="">Privacy Policy</a>
                        <a class="btn btn-link" href="">Terms & Condition</a>
                        <a class="btn btn-link" href="">Career</a>
                    </div>
               
                </div>
            </div>
            <div class="container px-lg-5">
                <div class="copyright">
                    <div class="row">
                        <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                            &copy; <a class="border-bottom" href="#">Paden</a>, All Right Reserved. 
							
							<!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
							Designed By <a class="border-bottom" href="https://lotusdreammaker.co.zw">lotusdreammaker</a>
                        </div>
                        <div class="col-md-6 text-center text-md-end">
                            <div class="footer-menu">
                                <a href="index.html">Home</a>
                       
                                <a href="">Help</a>
                              
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-lg-square back-to-top pt-2"><i class="bi bi-arrow-up text-white"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/wow.min.js')}}"></script>
    <script src="{{ asset('js/easing.min.js')}}"></script>
    <script src="{{ asset('js/waypoints.min.js')}}"></script>
    <script src="{{ asset('js/counterup.min.js')}}"></script>
    <script src="{{ asset('js/owl.carousel.min.js')}}"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>

