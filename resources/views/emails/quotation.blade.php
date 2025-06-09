<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Quotation #{{ $quotation->quotation_no }}</title>
  <style>
    /* Reset & Base Styles */
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      line-height: 1.6;
      color: #333333;
      background-color: #ffffff;
    }

    /* Layout */
    .container {
      width: 100%;
      max-width: 600px;
      margin: 0 auto;
    }

    .header {
      background-color: #f5f5f5;
      padding: 20px;
      text-align: center;
    }

    .content {
      padding: 20px;
    }

    .footer {
      padding: 20px;
      font-size: 12px;
      color: #777777;
      text-align: center;
    }

    /* Typography */
    h2 {
      margin: 0;
      color: inherit;
    }

    p {
      margin: 0 0 16px 0;
    }

    /* Links */
    a {
      color: #1a73e8;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Header -->
    <div class="header">
      <h2>Quotation #{{ $quotation->quotation_no }}</h2>
    </div>

    <!-- Content -->
    <div class="content">
      <p>Dear {{ $quotation->customer->name }},</p>

      <p>Thank you for your quotation request. Please find your PDF quotation attached.</p>

      <p>
        If you have any questions or need assistance,
        please <a href="mailto:{{ config('mail.from.address') }}">contact us</a>.
      </p>

      <p>
        Best regards,<br>
        <strong>ITC Finance</strong>
      </p>
    </div>

    <!-- Footer -->
    <div class="footer">
      &copy; {{ now()->year }} ITC Finance. All rights reserved.
    </div>
  </div>
</body>
</html>
