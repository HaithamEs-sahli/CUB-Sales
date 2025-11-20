<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>CUB Sales – Search Listings by User</title>

    <!-- Your site CSS -->
    <link rel="stylesheet" href="style.css">

    <!-- jQuery + jQuery UI for autocomplete -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
</head>
<body>

<header class="topbar">
    <div class="brand">
        <a href="index.html">CUB Sales</a>
    </div>
    <nav class="nav">
        <a href="index.html">Home</a>
        <a href="maintenance.php">Maintenance</a>
        <a href="imprint.html">Imprint</a>
    </nav>
</header>

<main class="container section">
    <h1>Search Listings by User</h1>

    <p>Start typing a username or email. Choose a suggestion, and the User ID will be filled automatically.</p>

    <form action="search_result_1.php" method="get" class="form">

        <!-- Text field with autocomplete -->
        <label for="user_search">Search user (name or email):</label>
        <input
            type="text"
            id="user_search"
            placeholder="Type username or email"
            autocomplete="off"
        >

        <!-- Numeric field actually used by the search_result_1.php query -->
        <label for="user_id">User ID:</label>
        <input
            type="number"
            name="user_id"
            id="user_id"
            placeholder="User ID will be filled when you pick a user"
            required
        >

        <button type="submit" class="btn">Search</button>
    </form>
</main>

<footer class="footer">
    <p><a href="index.html">Back to Home</a></p>
</footer>

<!-- Autocomplete script -->
<script>
    $(function () {
        $("#user_search").autocomplete({
            source: "backend/autocomplete_users.php",
            minLength: 1,
            select: function (event, ui) {
                // ui.item.value is user_id from PHP
                $("#user_id").val(ui.item.value);
            }
        });
    });
</script>

</body>
</html>
