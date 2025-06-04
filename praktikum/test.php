<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <button onclick="tampil()">tampil</button>
    <button onclick="hilang()">hilang</button>
    <div id="demo"></div>
    <script>
        function tampil() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("demo").innerHTML =
                    this.responseText;
                }
            };
            xhttp.open("GET", "tabel_2.php", true);
            xhttp.send();
        }
        function hilang() {
            document.getElementById("demo").innerHTML = "";
        }
    </script>
</body>
</html>