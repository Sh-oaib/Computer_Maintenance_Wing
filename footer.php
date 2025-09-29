<style> 
    footer{ 
        bottom: 0;
        left: 0;
        height: 100px;
        background-color: #ffffff; 
        padding: 20px;
        text-align: center;
        align-items: center;
        display: flex;
        justify-content: space-between;
        border-top: 1px solid #ccc;
        margin-left: 0px;
        max-width: 1700px; /* increased max width */
        margin: 0 auto; /* center footer */
    }
    .footerlist {
        display: flex;
        gap: 8px;
        background-color: white;
        justify-content: flex-start;
        align-items: center;
        margin-right: 190px;
        margin-bottom: 10px;
    }
</style>
<footer style="background-color: #ffffff; padding: 20px; text-align: center; border-top: 1px solid #ccc; display: 
flex; justify-content: space-between; align-items: center; max-width: 1700px; margin: 0 auto; overflow-x: hidden;">
<div style="color: #555; font-size: 14px; margin: 0;">
        &copy; <?php echo date("Y"); ?> Baba Ghulam Shah Badshah University. All Rights Reserved.
</div>
    <div class="footerlist">
        <a href="index.php" style="margin: 0 10px; text-decoration: none; color: #007bff;">Home</a>
        <a href="faqs.php" style="margin: 0 10px; text-decoration: none; color: #007bff;">FAQs</a>
        
    </div>
    
</footer>
