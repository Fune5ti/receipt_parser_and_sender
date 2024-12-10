<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 0.9em;
            color: #666;
        }

        .content {
            margin: 20px 0;
        }

        .important {
            color: #1a73e8;
            font-weight: bold;
        }

        .note {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            font-size: 0.9em;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>DEVGO-DESENVOLVIMENTO DE SOFTWARES, LDA</h2>
    </div>

    <div class="content">
        <p>Dear {{ $employee->name }},</p>

        <p>Please find attached your payroll receipt for <span class="important">{{ $month }} {{ $year }}</span>.</p>

        <div class="note">
            <p>📌 Important Information:</p>
            <ul>
                <li>This is an automatically generated email - please do not reply.</li>
                <li>The attached document is in PDF format.</li>
                <li>Please keep this receipt for your records.</li>
            </ul>
        </div>

        <p>If you have any questions about your payroll receipt, please contact the HR department directly.</p>
    </div>

    <div class="footer">
        <p>This email and any attached documents are confidential and intended solely for the named recipient.</p>
        <p>DEVGO-DESENVOLVIMENTO DE SOFTWARES, LDA<br>
            Monte Sossego - AV Holanda<br>
            2110-000</p>
    </div>
</body>

</html>