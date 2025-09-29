<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Front Page</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=home" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
   
        .content-container {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            max-width: 1000px;
            margin: 10px auto; 
        }
        .process-heading {
            font-size: 16px; 
            color: #333;
            margin-bottom: 5px; 
            margin-top: 2px; 
        }
        .content-container:nth-of-type(2) {
            margin-top: 0; 
        }
        .content-container img {
            max-width: 180px; 
            height: 240px; 
            margin-left: 10px; 
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .content-container p, .content-container ul {
            font-size: 16px;
            line-height: 1.8; 
            color: #555;
            text-align: justify;
            max-width: 800px; 
        }
        .content-container h2 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
            margin-top: 5px;
        }
        .content-container ul {
            display: block;
            padding-left: 20px;
            list-style: disc;
        }
        .content-container ul li {
            margin-bottom: 10px;
            background: none;
            border-radius: 0;
            padding: 0;
            box-shadow: none;
            min-width: 0;
            font-size: 16px;
            color: #555;
        }
        .content-container ul li {
            margin-bottom: 0;
            background: #f9f9f9;
            border-radius: 8px;
            padding: 16px 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            min-width: 220px;
            font-size: 15px;
            color: #444;
        }
        }
        .content-container ul li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100vw; margin: 0 auto;">
        <div class="content-container" style="width: 100%; max-width: 1000px; margin: 10px auto; display: flex; align-items: flex-start; justify-content: flex-start;">
        <div style="flex: 1;">
            <h2>Computer Maintenance Wing (CMW)</h2>
            <p>
                The Computer Maintenance Wing (CMW), established under the Centre for Information
                Technology Enabled Services (CITES) at Baba Ghulam Shah Badshah University (BGSBU),
                plays a crucial role in ensuring seamless hardware and software functionality for 
                desktop users within the university premises. Strategically located at the Computer
                Science Department, technical support is available between 09:00 AM to 04:30 PM from
                 Monday to Saturday (Except Holidays).
            </p>
        </div>
        <img src="https://www.bgsbu.ac.in/uniimg/pc3.jpg" alt="CMW Image" style="max-width:180px; height:240px; margin-left:10px; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,0.1);">
    </div>
    <div class="content-container" style="width: 100%; max-width: 1000px; margin: 10px auto; display: flex; align-items: flex-start; justify-content: center;">
        <div style="flex: 1;">
            <h2 class="process-heading">Process</h2>
            <ul >
                <li>The User has to lodge a complaint by submitting a job card.</li>
                <li>User is given a job number as a reference. On the basis of the job card and the corresponding reference number, problems with regard to hardware/software are rectified and the same is given back to the user after necessary repair work.</li>
                <li>In case of problems related to the operating system of a desktop or laptops under warranty (e.g., Windows), the supplier of the desktop or OEM of the desktop/laptop (i.e., DELL/ACER/WIPRO/HP, etc.) should be contacted.</li>
                <li>To start the process, please register on the portal. Once your account is verified by the admin, you will be able to log in, submit a Job Card to lodge a complaint, and request an Indent as needed.</li>
            </ul>
        </div>
        <img src="https://www.bgsbu.ac.in/uniimg/pc.jpg" alt="Process Image" style="max-width:180px; height:240px; margin-left:10px; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,0.1);">
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>