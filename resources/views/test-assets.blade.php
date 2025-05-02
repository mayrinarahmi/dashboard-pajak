<!DOCTYPE html>
<html>
<head>
    <title>Test Assets</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .test-box {
            width: 200px;
            height: 200px;
            background-color: red;
            margin: 20px;
        }
    </style>
</head>
<body>
    <h1>Test Assets Loading</h1>
    <div class="test-box"></div>
</body>
</html>