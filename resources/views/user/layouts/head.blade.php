<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8" />
 <meta name="viewport" content="width=device-width, initial-scale=1.0" />
 <title>MK7 Slot</title>
 <link rel="icon" href="{{ asset('/assets/img/logo.png') }}">
 <link rel="stylesheet" href="{{ asset('slot_app/css/style.css') }}" />
 <!-- Bootstrap 5 CSS -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

 <link rel="preconnect" href="https://fonts.googleapis.com" />
 <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
 <link href="https://fonts.googleapis.com/css2?family=Alumni+Sans+Inline+One&family=Inter&family=Poppins:wght@300;400;500&family=Rubik+Mono+One&display=swap" rel="stylesheet" />

 <!-- Material Css -->
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/material-icons@1.13.12/iconfont/material-icons.min.css" />
 <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
 <!-- font awesome  -->
 <link rel="stylesheet" 
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

 @yield('style')
 <style>
    #main {
        background-image: url("{{ asset('assets/img/background.jpg') }}"); /* Dynamically set background image */
        /* fallback background color, uncomment if needed */
        /* background: #000; */
        min-height: 100vh; /* Ensure the container takes up the full viewport height */
        background-repeat: no-repeat; /* Prevent the image from repeating */
        background-size: cover; /* Ensure the image covers the entire container */
        background-position: center; /* Center the image within the container */
    }
    .login-card{
        display: grid;
        align-items: center;
    }
 </style>
</head>