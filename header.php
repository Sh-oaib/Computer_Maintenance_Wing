<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it hasn't been started
}
?>
<header>
    <img src="https://bgsbu.ac.in/public/assets/frontend/img/logofinal.png" alt="Logo">
    <div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Show Logout Button if User is Logged In -->
            <a href="logout.php" class="btn">Logout</a>
        <?php else: ?>
            <!-- Show Login and Register Buttons if User is Not Logged In -->
            <a href="login.php" class="btn" target="_blank">Login </a>
            <a href="registration.php" class="btn" target="_blank">Register</a>
        <?php endif; ?>
    </div>
</header>
<nav>
    <ul>
        <li><a href="Index.php">Home</a></li>
        <li><a href="JobCard.php">JobCard</a></li>
        <li><a href="indent.php">Indent</a></li>
        <li><a href="complaint_status.php">Complaint Status</a></li>
        <li><a href="indent_status.php">Indent Status</a></li>
        <li><a href="services.php">Services</a></li>
    </ul>
<style>
    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        background-color: white;
    }
    header img {
        max-width: 720px;
        height: auto;
        margin-right: 20px;
    }
    .dropdown {
        position: relative;
        display: inline-block;
    }
    .dropbtn {
        cursor: pointer;
    }
    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 220px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .dropdown-content li a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        text-align: left;
    }
    .dropdown-content li a:hover {
        background-color: #f1f1f1;
    }
    .dropdown:hover .dropdown-content {
        display: block;
    }
    /* Only one set of nav ul li a styles below */
    nav {
        background-color: white;
        border-top: 1px solid rgba(128, 128, 128, 0.5);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        position: static;
        z-index: 1000;
        width: 100vw;
        margin: 0;
        min-height: 70px;
    }
    nav ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100vw;
    }
    nav ul li {
        margin: 0;
        padding: 0;
    }
    nav ul li a {
        display: block;
        padding: 20px 50px;
        text-decoration: none;
        font-size: 18px;
        color: black;
        text-align: center;
        position: relative;
    }
    nav ul li a:hover {
        color: rgb(126, 34, 206);
    }
    nav ul li a::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background-color: rgb(126, 34, 206);
       
    }
    nav ul li a:hover::after {
        width: 100%;
    }
</style>
