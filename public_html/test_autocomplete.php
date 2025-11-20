<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Autocomplete Demo</title>

    <!-- jQuery & jQuery UI from CDN -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
</head>
<body>
    <h2>Autocomplete Demo</h2>

    <p>Type a listing title:</p>
    <input type="text" id="demoTitle">

    <script>
        $(function () {
            $("#demoTitle").autocomplete({
                source: "backend/autocomplete_dynamic.php"  // bonus version
            });
        });
    </script>
</body>
</html>
