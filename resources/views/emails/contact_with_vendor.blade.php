<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fashino Email Template</title>
    <style>
        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
                padding: 0 10px;
            }
            .responsive-img {
                width: 100% !important;
                height: auto !important;
            }
            .full-width {
                display: block;
                width: 100% !important;
                text-align: center !important;
            }
            .stack-column {
                display: block;
                width: 100% !important;
            }
            .footer-widget {
                padding: 10px 0 !important;
                text-align: center !important;
            }
            .footer-menu {
                padding: 0;
            }
        }
        .footer-menu {
            list-style-type: none;
            padding: 0;
        }
        .footer-menu li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif;">

    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #2d3e50;">
        <tr>
            <td align="center">
                <table class="container" width="600" border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff; width: 600px;">
                    <tr>
                        <td align="center" style="padding: 20px 0;">
                            <img src="https://siostore.eu/backend_assets/images/siostore_logo.png" alt="Fashino" style="width: 150px;" class="responsive-img">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 20px;">
                            <h1 style="font-size: 24px; color: #333333;">Best
                                E-Commerce Platform</h1>
                            <p style="font-size: 18px; color: #555555;"></p>
                            <a href="https://siostore.eu/" style="display: inline-block; padding: 10px 20px; background-color: #333333; color: #ffffff; text-decoration: none; margin-top: 10px;">SHOP NOW</a>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 20px 0; background-color: #333333; color: #ffffff;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" style="padding: 10px;" class="stack-column">
                                        <p>PAYMENT METHODS</p>
                                    </td>
                                    <td align="center" style="padding: 10px;" class="stack-column">
                                        <p>FREE DELIVERY</p>
                                    </td>
                                    <td align="center" style="padding: 10px;" class="stack-column">
                                        <p>RETURN POLICY</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 20px;">
                            <h2 style="font-size: 20px; color: #333333;">Contact with Customer/Vendor Details</h2>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" style="padding: 10px;" class="stack-column">
                                        <p style="font-size: 16px; color: #333333;">First Name : {{ $data->first_name }}</p>
                                        <p style="font-size: 16px; color: #333333;">Last Name : {{ $data->last_name }}</p>
                                        <p style="font-size: 16px; color: #333333;">Phone Number : {{ $data->phone_number }}</p>
                                        <a style="font-size: 16px; color: #333333;">Email : {{ $data->email }}</a>
                                    </td>
                                    <td align="center" style="padding: 10px;" class="stack-column">
                                       <p><b>Note:</b>
                                        You can contact with each other through the email or Mobile Number but the payment can be paid throuh the siostore.eu platform.</p>
                                    </td>
                                   
                                </tr>
                            </table>
                        </td>
                    </tr>
                     
                    <!-- Footer Section -->
                    <tr>
                        <td align="center" style="padding: 20px; background-color: #333333; color: #ffffff;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="stack-column" style="padding: 20px;">
                                        <div class="footer-widget">
                                            <div style="text-align: center; margin-bottom: 15px;">
                                                <a href="https://siostore.eu">
                                                    <img src="https://siostore.eu/backend_assets/images/siostore_logo.png" width="180" alt="" style="max-width: 100%; height: auto;">
                                                </a>
                                            </div>
                                            <p>SIOSTORE is an online store that allows you to buy and shop for your numerous demands at your convenience.</p>
                                            <div style="text-align: center; margin-top: 15px;">
                                                <ul style="list-style: none; padding: 0; margin: 0; display: inline-block;">
                                                    <li style="display: inline; margin-right: 10px;"><a href="JavaScript:Void(0);" style="color: #ffffff; text-decoration: none;"><i class="fab fa-facebook-f"></i></a></li>
                                                    <li style="display: inline; margin-right: 10px;"><a href="JavaScript:Void(0);" style="color: #ffffff; text-decoration: none;"><i class="fab fa-linkedin-in"></i></a></li>
                                                    <li style="display: inline; margin-right: 10px;"><a href="JavaScript:Void(0);" style="color: #ffffff; text-decoration: none;"><i class="fab fa-instagram"></i></a></li>
                                                    <li style="display: inline; margin-right: 10px;"><a href="JavaScript:Void(0);" style="color: #ffffff; text-decoration: none;"><i class="fab fa-twitter"></i></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="stack-column" style="padding: 20px;">
                                        <div class="footer-widget">
                                            <h4 style="font-size: 16px; margin-bottom: 10px;">Quick Shop</h4>
                                            <ul class="footer-menu">
                                                <li><a href="https://siostore.eu" style="color: #ffffff; text-decoration: none;">Home</a></li>
                                                <li><a href="https://siostore.eu/contact" style="color: #ffffff; text-decoration: none;">Contact Us</a></li>
                                                <li><a href="https://siostore.eu/term-condition" style="color: #ffffff; text-decoration: none;">Terms &amp; Conditions</a></li>
                                                <li><a href="https://siostore.eu/disclaimer" style="color: #ffffff; text-decoration: none;">Disclaimer</a></li>
                                                <li><a href="https://siostore.eu/privacy-policy" style="color: #ffffff; text-decoration: none;">Privacy Policy</a></li>
                                                <li><a href="https://siostore.eu/licence" style="color: #ffffff; text-decoration: none;">License &amp; Agreements</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td class="stack-column" style="padding: 20px;">
                                        <div class="footer-widget">
                                            <h4 style="font-size: 16px; margin-bottom: 10px;">My Account</h4>
                                            <ul class="footer-menu">
                                                <li><a href="https://siostore.eu/login" style="color: #ffffff; text-decoration: none;">Login</a></li>
                                                <li><a href="https://siostore.eu/signup" style="color: #ffffff; text-decoration: none;">Sign Up</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="stack-column" style="padding: 20px;">
                                        <div class="footer-widget">
                                            <h4 style="font-size: 16px; margin-bottom: 10px;">More Links</h4>
                                            <div style="display: flex; flex-direction: column;">
                                                <a class="text-white mb-2" target="_blank" href="https://siopay.eu" style="color: #ffffff; text-decoration: none;">Payment &amp; Digital Services</a>
                                                <a class="text-white mb-2" target="_blank" href="https://sioshipping.eu" style="color: #ffffff; text-decoration: none;">Shipping &amp; Courier Services</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="stack-column" style="padding: 20px;">
                                        <div class="footer-widget">
                                            <h4 style="font-size: 16px; margin-bottom: 10px;">Newsletter</h4>
                                            <p style="margin-bottom: 10px;">Subscribe to our newsletter to get updates on our products and services</p>
                                            <form action="https://siostore.eu/subscriber" method="POST">
                                                <input type="hidden" name="_token" value="2QEQ9VAi3zNrDsRxpSBo6psDDREic8CI9wmqNwEe">
                                                <div style="display: flex; justify-content: center;">
                                                    <input type="email" style="padding: 5px; width: 70%;" name="email" placeholder="Your Email Address">
                                                    <button type="submit" style="padding: 5px 10px; background-color: #0056b3; color: #ffffff; border: none;">Subscribe</button>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
